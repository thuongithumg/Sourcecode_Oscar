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
 * Class Magestore_Giftvoucher_Model_Total_Quote_Giftcardcreditaftertax
 */
class Magestore_Giftvoucher_Model_Total_Quote_Giftcardcreditaftertax extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Magestore_Giftvoucher_Model_Total_Quote_Giftcardcreditaftertax constructor.
     */
    public function __construct() {
        $this->setCode('giftcardcredit_after_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('giftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if (!$applyGiftAfterTax) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');
        
        if (!is_object($session)) {
            return $this;
        }

        if (!Mage::helper('giftvoucher')->getGeneralConfig('enablecredit', $quote->getStoreId())) {
            $session->setBaseUseGiftCreditAmount(0);
            $session->setUseGiftCreditAmount(0);
            return $this;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        if ($address->getAddressType() == 'billing' && !$quote->isVirtual() || !$session->getUseGiftCardCredit() || !$customer->getId()
        ) {

            return $this;
        }
        $credit = Mage::getModel('giftvoucher/credit')->load(
                $customer->getId(), 'customer_id'
        );
        if ($credit->getBalance() < 0.0001) {
            $session->setBaseUseGiftCreditAmount(0);
            $session->setUseGiftCreditAmount(0);
            return $this;
        }
        $store = $quote->getStore();
        $baseBalance = 0;
        if ($rate = $store->getBaseCurrency()->getRate($credit->getData('currency'))) {
            $baseBalance = $credit->getBalance() / $rate;
        }
        if ($baseBalance < 0.0001) {
            $session->setBaseUseGiftCreditAmount(0);
            $session->setUseGiftCreditAmount(0);
            return $this;
        }

        if ($session->getMaxCreditUsed() > 0.0001) {
            $baseBalance = min($baseBalance, floatval($session->getMaxCreditUsed()) / $store->convertPrice(1, false, false));
        }

        $baseTotalDiscount = 0;
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'giftvoucher') {
                        $itemDiscount = $child->getBaseRowTotal() - $child->getMagestoreBaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                        $baseTotalDiscount += $itemDiscount;
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'giftvoucher') {
                    $itemDiscount = $item->getBaseRowTotal() - $item->getMagestoreBaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                    $baseTotalDiscount += $itemDiscount;
                }
            }
        }
        if (Mage::getStoreConfig('giftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            $shipDiscount = $address->getBaseShippingAmount() - $address->getMagestoreBaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
            $baseTotalDiscount += $shipDiscount;
        }
        $baseDiscount = min($baseTotalDiscount, $baseBalance);
        $discount = $store->convertPrice($baseDiscount);
        if ($baseTotalDiscount != 0)
            $this->prepareGiftDiscountForItem($address, $baseDiscount / $baseTotalDiscount, $store, $baseDiscount);

        if ($baseDiscount && $discount) {
            $session->setBaseUseGiftCreditAmount($baseDiscount);
            $session->setUseGiftCreditAmount($discount);

            $address->setGiftcardCreditAmount($baseDiscount * $rate);
            $address->setBaseUseGiftCreditAmount($baseDiscount);
            $address->setUseGiftCreditAmount($discount);

            $address->setMagestoreBaseDiscount($address->getMagestoreBaseDiscount() + $baseDiscount);

            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount);
            $address->setGrandTotal($store->convertPrice($address->getBaseGrandTotal()));
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
        $amount = $address->getUseGiftCreditAmount();
        if ($amount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('giftvoucher')->__('Gift Card credit'),
                'value' => -$amount
            ));
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $rateDiscount
     * @param $store
     * @param $baseDiscount
     * @return $this
     */
    public function prepareGiftDiscountForItem(Mage_Sales_Model_Quote_Address $address, $rateDiscount, $store, $baseDiscount) {
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $discountGiftcardCredit = 0;
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'giftvoucher') {
                        $itemDiscount = $child->getBaseRowTotal() - $child->getMagestoreBaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                        $child->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemDiscount * $rateDiscount);
                        $child->setBaseUseGiftCreditAmount($child->getBaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                        $child->setUseGiftCreditAmount($child->getUseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'giftvoucher') {
                    $itemDiscount = $item->getBaseRowTotal() - $item->getMagestoreBaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                    $item->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemDiscount * $rateDiscount);
                    $item->setBaseUseGiftCreditAmount($item->getBaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                    $item->setUseGiftCreditAmount($item->getUseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));
                }
            }
        }
        if (Mage::getStoreConfig('giftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            $shipDiscount = $address->getBaseShippingAmount() - $address->getMagestoreBaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
            $address->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setBaseGiftcreditDiscountForShipping($address->getBaseGiftvoucherDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setGiftcreditDiscountForShipping($address->getGiftvoucherDiscountForShipping() + $store->convertPrice($shipDiscount * $rateDiscount));
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
