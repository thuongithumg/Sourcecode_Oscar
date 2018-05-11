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
 * Class Magestore_Giftvoucher_Model_Total_Quote_Giftcardcredit
 */
class Magestore_Giftvoucher_Model_Total_Quote_Giftcardcredit extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    protected $_hiddentBaseDiscount = 0;
    protected $_hiddentDiscount = 0;

    /**
     * Magestore_Giftvoucher_Model_Total_Quote_Giftcardcredit constructor.
     */
    public function __construct() {
        $this->setCode('giftcardcredit');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('giftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if ($applyGiftAfterTax) {
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
//            $session->setBaseUseGiftCreditAmount(0);
//            $session->setUseGiftCreditAmount(0);
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
        if ($rateCredit = $store->getBaseCurrency()->getRate($credit->getData('currency'))) {
            $baseBalance = $credit->getBalance() / $rateCredit;
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
                        if (Mage::helper('tax')->priceIncludesTax())
                            $itemDiscount = $child->getRowTotalInclTax() - $child->getMagestoreBaseDiscount() - $child->getDiscountAmount();
                        else
                            $itemDiscount = $child->getBaseRowTotal() - $child->getMagestoreBaseDiscount() - $child->getBaseDiscountAmount();
                        $baseTotalDiscount += $itemDiscount;
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'giftvoucher') {
                    if (Mage::helper('tax')->priceIncludesTax())
                        $itemDiscount = $item->getRowTotalInclTax() - $item->getMagestoreBaseDiscount() - $item->getDiscountAmount();
                    else
                        $itemDiscount = $item->getBaseRowTotal() - $item->getMagestoreBaseDiscount() - $item->getBaseDiscountAmount();
                    $baseTotalDiscount += $itemDiscount;
                }
            }
        }
        if (Mage::getStoreConfig('giftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            if (Mage::helper('tax')->shippingPriceIncludesTax())
                $shipDiscount = $address->getShippingInclTax() - $address->getMagestoreBaseDiscountForShipping() - $address->getShippingDiscountAmount();
            else
                $shipDiscount = $address->getBaseShippingAmount() - $address->getMagestoreBaseDiscountForShipping() - $address->getBaseShippingDiscountAmount();
            $baseTotalDiscount += $shipDiscount;
        }

        $baseDiscount = min($baseTotalDiscount, $baseBalance);
        $discount = $store->convertPrice($baseDiscount);
        $this->prepareGiftDiscountForItem($address, $baseDiscount / $baseTotalDiscount, $store, $baseDiscount);

        if ($baseDiscount && $discount) {
            $session->setBaseUseGiftCreditAmount($baseDiscount);
            $session->setUseGiftCreditAmount($discount);

            $address->setGiftcardCreditAmount($baseDiscount * $rateCredit);
            $address->setBaseUseGiftCreditAmount($baseDiscount);
            $address->setUseGiftCreditAmount($discount);

            $address->setMagestoreBaseDiscount($address->getMagestoreBaseDiscount() + $baseDiscount);

            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $this->_hiddentBaseDiscount - $baseDiscount);
            $address->setGrandTotal($address->getGrandTotal() + $this->_hiddentDiscount - $discount);
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
        if ($applyGiftAfterTax) {
            return $this;
        }
        if ($amount = $address->getUseGiftCreditAmount()) {
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
                        if (Mage::helper('tax')->priceIncludesTax())
                            $itemDiscount = $child->getRowTotalInclTax() - $child->getMagestoreBaseDiscount() - $child->getDiscountAmount();
                        else
                            $itemDiscount = $child->getBaseRowTotal() - $child->getMagestoreBaseDiscount() - $child->getBaseDiscountAmount();
                        $child->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemDiscount * $rateDiscount);
                        $child->setBaseUseGiftCreditAmount($child->getBaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                        $child->setUseGiftCreditAmount($child->getUseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));
                        $baseTaxableAmount = $child->getBaseTaxableAmount();
                        $taxableAmount = $child->getTaxableAmount();

                        $child->setBaseTaxableAmount($child->getBaseTaxableAmount() - $child->getBaseUseGiftCreditAmount());
                        $child->setTaxableAmount($child->getTaxableAmount() - $child->getUseGiftCreditAmount());

                        if (Mage::helper('tax')->priceIncludesTax()) {
                            $rate = Mage::helper('giftvoucher')->getItemRateOnQuote($item->getProduct(), $store);
                            $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                            $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                            $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getBaseTaxableAmount(), $rate, true, false);
                            $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getTaxableAmount(), $rate, true, false);


                            $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                            $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);

                            $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                            $this->_hiddentDiscount += $hiddentDiscount;
                        }
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'giftvoucher') {
                    if (Mage::helper('tax')->priceIncludesTax())
                        $itemDiscount = $item->getRowTotalInclTax() - $item->getMagestoreBaseDiscount() - $item->getDiscountAmount();
                    else
                        $itemDiscount = $item->getBaseRowTotal() - $item->getMagestoreBaseDiscount() - $item->getBaseDiscountAmount();
                    $item->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemDiscount * $rateDiscount);
                    $item->setBaseUseGiftCreditAmount($item->getBaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                    $item->setUseGiftCreditAmount($item->getUseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));

                    $baseTaxableAmount = $item->getBaseTaxableAmount();
                    $taxableAmount = $item->getTaxableAmount();

                    $item->setBaseTaxableAmount($item->getBaseTaxableAmount() - $item->getBaseUseGiftCreditAmount());
                    $item->setTaxableAmount($item->getTaxableAmount() - $item->getUseGiftCreditAmount());

                    if (Mage::helper('tax')->priceIncludesTax()) {
                        $rate = Mage::helper('giftvoucher')->getItemRateOnQuote($item->getProduct(), $store);
                        $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                        $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                        $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getBaseTaxableAmount(), $rate, true, false);
                        $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getTaxableAmount(), $rate, true, false);


                        $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                        $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);

                        $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                        $this->_hiddentDiscount += $hiddentDiscount;
                    }
                }
            }
        }
        if (Mage::getStoreConfig('giftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            if (Mage::helper('tax')->shippingPriceIncludesTax())
                $shipDiscount = $address->getShippingInclTax() - $address->getMagestoreBaseDiscountForShipping() - $address->getShippingDiscountAmount();
            else
                $shipDiscount = $address->getBaseShippingAmount() - $address->getMagestoreBaseDiscountForShipping() - $address->getBaseShippingDiscountAmount();
            $address->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setBaseGiftcreditDiscountForShipping($address->getBaseGiftcreditDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setGiftcreditDiscountForShipping($address->getGiftcreditDiscountForShipping() + $store->convertPrice($shipDiscount * $rateDiscount));

            $baseTaxableAmount = $address->getBaseShippingTaxable();
            $taxableAmount = $address->getShippingTaxable();

            $address->setBaseShippingTaxable($address->getBaseShippingTaxable() - $address->getBaseGiftcreditDiscountForShipping());
            $address->setShippingTaxable($address->getShippingTaxable() - $address->getGiftcreditDiscountForShipping());

            if (Mage::helper('tax')->shippingPriceIncludesTax() && $shipDiscount) {
                $rate = $this->getShipingTaxRate($address, $store);
                $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getBaseShippingTaxable(), $rate, true, false);
                $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getShippingTaxable(), $rate, true, false);

                $this->_hiddentBaseDiscount += Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                $this->_hiddentDiscount += Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);
            }
        }
        return $this;
    }

    /**
     * @param $address
     * @param $store
     * @return mixed
     */
    public function getShipingTaxRate($address, $store) {
        $request = Mage::getSingleton('tax/calculation')->getRateRequest(
                $address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
        );
        $request->setProductClassId(Mage::getSingleton('tax/config')->getShippingTaxClass($store));
        $rate = Mage::getSingleton('tax/calculation')->getRate($request);
        return $rate;
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
