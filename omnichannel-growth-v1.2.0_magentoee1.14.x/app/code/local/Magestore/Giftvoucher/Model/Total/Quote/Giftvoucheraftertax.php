<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Giftvoucher_Model_Total_Quote_Giftvoucheraftertax
 */
class Magestore_Giftvoucher_Model_Total_Quote_Giftvoucheraftertax extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Magestore_Giftvoucher_Model_Total_Quote_Giftvoucheraftertax constructor.
     */
    public function __construct() {
        $this->setCode('giftvoucher_after_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('giftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if (!$applyGiftAfterTax) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');

        if ($address->getAddressType() == 'billing' && !$quote->isVirtual() || !$session->getUseGiftCard()) {
            return $this;
        }

        if ($codes = $session->getGiftCodes()) {
            $codesArray = array_unique(explode(',', $codes));
            $store = $quote->getStore();

            $baseTotalDiscount = 0;
            $totalDiscount = 0;

            $codesBaseDiscount = array();
            $codesDiscount = array();

            $baseSessionAmountUsed = explode(',', $session->getBaseAmountUsed());
            $baseAmountUsed = array_combine($codesArray, $baseSessionAmountUsed);
            $amountUsed = $baseAmountUsed;

            $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
            if (!is_array($giftMaxUseAmount)) {
                $giftMaxUseAmount = array();
            }
            foreach ($codesArray as $key => $code) {
                $model = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
                if ($model->getStatus() != Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE || $model->getBalance() == 0 || $model->getBaseBalance() <= $baseAmountUsed[$code] || !$model->validate($address)
                ) {
                    $codesBaseDiscount[] = 0;
                    $codesDiscount[] = 0;
                } else {
                    if (Mage::helper('giftvoucher')->canUseCode($model)) {
                        $baseBalance = $model->getBaseBalance() - $baseAmountUsed[$code];
                        if (array_key_exists($code, $giftMaxUseAmount)) {
                            $maxDiscount = max(floatval($giftMaxUseAmount[$code]), 0) / $store->convertPrice(1, false, false);
                            $baseBalance = min($baseBalance, $maxDiscount);
                        }
                        if ($baseBalance > 0) {
                            $baseDiscountTotal = 0;
                            foreach ($address->getAllItems() as $item) {
                                if ($item->getParentItemId())
                                    continue;
                                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                                    foreach ($item->getChildren() as $child) {
                                        if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'giftvoucher' && $model->getActions()->validate($child)) {
                                            $itemDiscount = $child->getBaseRowTotal() - $child->getMagestoreBaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                                            $baseDiscountTotal += $itemDiscount;
                                        }
                                    }
                                } elseif ($item->getProduct()) {
                                    if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'giftvoucher' && $model->getActions()->validate($item)) {
                                        $itemDiscount = $item->getBaseRowTotal() - $item->getMagestoreBaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                                        $baseDiscountTotal += $itemDiscount;
                                    }
                                }
                            }
                            if (Mage::getStoreConfig('giftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
                                $shipDiscount = $address->getBaseShippingAmount() - $address->getMagestoreBaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
                                $baseDiscountTotal += $shipDiscount;
                            }
                        } else
                            $baseDiscount = 0;
                    } else {
                        $baseDiscount = 0;
                    }
                    $baseDiscount = min($baseDiscountTotal, $baseBalance);
                    $discount = $store->convertPrice($baseDiscount);
                    if ($baseDiscountTotal != 0)
                        $this->prepareGiftDiscountForItem($address, $baseDiscount / $baseDiscountTotal, $store, $model, $baseDiscount);

                    $baseAmountUsed[$code] += $baseDiscount;
                    $amountUsed[$code] = $store->convertPrice($baseAmountUsed[$code]);

                    $baseTotalDiscount += $baseDiscount;
                    $totalDiscount += $discount;

                    $codesBaseDiscount[] = $baseDiscount;
                    $codesDiscount[] = $discount;
                }
            }
            $codesBaseDiscountString = implode(',', $codesBaseDiscount);
            $codesDiscountString = implode(',', $codesDiscount);

            //update session
            $session->setBaseAmountUsed(implode(',', $baseAmountUsed));

            $session->setBaseGiftVoucherDiscount($session->getBaseGiftVoucherDiscount() + $baseTotalDiscount);
            $session->setGiftVoucherDiscount($session->getGiftVoucherDiscount() + $totalDiscount);

            $session->setCodesBaseDiscount($session->getBaseAmountUsed());
            $session->setCodesDiscount(implode(',', $amountUsed));

            //update address
            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseTotalDiscount);
            $address->setGrandTotal($store->convertPrice($address->getBaseGrandTotal()));

            $address->setBaseGiftVoucherDiscount($baseTotalDiscount);
            $address->setGiftVoucherDiscount($totalDiscount);

            $address->setGiftCodes($codes);
            $address->setCodesBaseDiscount($codesBaseDiscountString);
            $address->setCodesDiscount($codesDiscountString);

            $address->setMagestoreBaseDiscount($address->getMagestoreBaseDiscount() + $baseTotalDiscount);

            //update quote
            $quote->setBaseGiftVoucherDiscount($session->getBaseGiftVoucherDiscount());
            $quote->setGiftVoucherDiscount($session->getGiftVoucherDiscount());

            $quote->setGiftCodes($codes);
            $quote->setCodesBaseDiscount($session->getCodesBaseDiscount());
            $quote->setCodesDiscount($session->getCodesDiscount());
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('giftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if (!$applyGiftAfterTax) {
            return $this;
        }
        $giftVoucherDiscount = $address->getGiftVoucherDiscount();
        if ($giftVoucherDiscount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('giftvoucher')->__('Gift Card'),
                'value' => -$giftVoucherDiscount,
                'gift_codes' => $address->getGiftCodes(),
                'codes_base_discount' => $address->getCodesBaseDiscount(),
                'codes_discount' => $address->getCodesDiscount()
            ));
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $rateDiscount
     * @param $store
     * @param $model
     * @param $baseDiscount
     * @return $this
     */
    public function prepareGiftDiscountForItem(Mage_Sales_Model_Quote_Address $address, $rateDiscount, $store, $model, $baseDiscount) {
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $discountGiftcardCodes = 0;
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'giftvoucher' && $model->getActions()->validate($child)) {
                        $itemDiscount = $child->getBaseRowTotal() - $child->getMagestoreBaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                        $child->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemDiscount * $rateDiscount);
                        $child->setBaseGiftVoucherDiscount($child->getBaseGiftVoucherDiscount() + $itemDiscount * $rateDiscount);
                        $child->setGiftVoucherDiscount($child->getGiftVoucherDiscount() + $store->convertPrice($itemDiscount * $rateDiscount));
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'giftvoucher' && $model->getActions()->validate($item)) {
                    $itemDiscount = $item->getBaseRowTotal() - $item->getMagestoreBaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                    $item->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemDiscount * $rateDiscount);
                    $item->setBaseGiftVoucherDiscount($item->getBaseGiftVoucherDiscount() + $itemDiscount * $rateDiscount);
                    $item->setGiftVoucherDiscount($item->getGiftVoucherDiscount() + $store->convertPrice($itemDiscount * $rateDiscount));
                }
            }
        }
        if (Mage::getStoreConfig('giftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            $shipDiscount = $address->getBaseShippingAmount() - $address->getMagestoreBaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
            $address->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setBaseGiftvoucherDiscountForShipping($address->getBaseGiftvoucherDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setGiftvoucherDiscountForShipping($address->getGiftvoucherDiscountForShipping() + $store->convertPrice($shipDiscount * $rateDiscount));
        }
        return $this;
    }

    /**
     * @param $session
     */
    public function clearGiftcardSession($session) {
        if ($session->getUseGiftCard())
            $session->setUseGiftCard(null)
                    ->setGiftCodes(null)
                    ->setBaseAmountUsed(null)
                    ->setBaseGiftVoucherDiscount(null)
                    ->setGiftVoucherDiscount(null)
                    ->setCodesBaseDiscount(null)
                    ->setCodesDiscount(null)
                    ->setGiftMaxUseAmount(null);
        if ($session->getUseGiftCardCredit()) {
            $session->setUseGiftCardCredit(null)
                    ->setMaxCreditUsed(null)
                    ->setBaseUseGiftCreditAmount(null)
                    ->setUseGiftCreditAmount(null);
        }
    }

}
