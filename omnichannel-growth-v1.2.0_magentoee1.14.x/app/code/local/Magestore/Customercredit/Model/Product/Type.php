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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Customercredit Model
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_CustomerCredit_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{

    /**
     * @param Varien_Object $buyRequest
     * @param null $product
     * @return array
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        if (version_compare(Mage::getVersion(), '1.5.0', '>='))
            return parent::prepareForCart($buyRequest, $product);
        if (is_null($product))
            $product = $this->getProduct();
        $result = parent::prepareForCart($buyRequest, $product);
        if (is_string($result))
            return $result;
        reset($result);
        $product = current($result);
        $result = $this->_prepareCustomerCredit($buyRequest, $product);
        return $result;
    }

    /**
     * @param Varien_Object $buyRequest
     * @param $product
     * @param $processMode
     * @return array
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        if (version_compare(Mage::getVersion(), '1.5.0', '<'))
            return parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_null($product))
            $product = $this->getProduct();
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result))
            return $result;
        reset($result);
        $product = current($result);
        $result = $this->_prepareCustomerCredit($buyRequest, $product);
        return $result;
    }

    /**
     * @param Varien_Object $buyRequest
     * @param $product
     * @return array
     */
    protected function _prepareCustomerCredit(Varien_Object $buyRequest, $product)
    {
        if (Mage::app()->getStore()->isAdmin())
            $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        else
            $store = Mage::app()->getStore();

        $amount = $buyRequest->getAmount();

        //if add to cart from page different detail page
        $creditAmount = Mage::helper('customercredit/creditproduct')->getCreditValue($product);
        if (!$amount && $creditAmount['type'] == 'static') {
            $amount = $this->convertPrice($product, $creditAmount['value']);
            $fnPrice = $creditAmount['credit_price'];
        }
        if ($amount) {
            switch ($creditAmount['type']) {
                case 'range':
                    if ($amount < $this->convertPrice($product, $creditAmount['from'])) {
                        $amount = $this->convertPrice($product, $creditAmount['from']) * $creditAmount['storecredit_rate'];
                    } elseif ($amount > $this->convertPrice($product, $creditAmount['to'])) {
                        $amount = $this->convertPrice($product, $creditAmount['to']) * $creditAmount['storecredit_rate'];
                    } else {
                        if ($amount > 0) {
                            $amount = $amount * $creditAmount['storecredit_rate'];
                        } else {
                            $amount = 0;
                        }
                    }
                    $fnPrice = $amount;
                    //Edit by hades
                    $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                    $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
                    $fnPrice = Mage::helper('directory')->currencyConvert($amount, $currentCurrencyCode, $baseCurrencyCode);
                    //End edit by hades
                    break;
                case 'dropdown':
                    if (!empty($creditAmount['options'])) {
                        $check = false;
                        $giftDropdown = array();
                        for ($i = 0; $i < count($creditAmount['options']); $i++) {
                            $giftDropdown[$i] = $this->convertPrice($product, $creditAmount['options'][$i]);
                            if ($amount == $giftDropdown[$i]) {
                                $check = true;
                            }
                        }
                        if (!$check) {
                            $amount = $creditAmount['options'][0];
                        }

                        $fnPrices = array_combine($giftDropdown, $creditAmount['prices']);
                        $fnPrice = $fnPrices[$amount];
                    }
                    break;
                case 'static':
                    if ($amount != $this->convertPrice($product, $creditAmount['value'])) {
                        $amount = $creditAmount['value'];
                    }
                    $fnPrice = $creditAmount['credit_price'];
                    break;
                default:
                    return Mage::helper('customercredit')->__('Please enter Store Credit information.');
            }
        } else
            return Mage::helper('customercredit')->__('Please enter Store Credit information.');

        $buyRequest->setAmount((string)$amount);
        $product->addCustomOption('credit_price_amount', $fnPrice);

        foreach (Mage::helper('customercredit')->getFullCreditProductOptions() as $key => $label) {
            if ($value = $buyRequest->getData($key)) {
                $product->addCustomOption($key, ($value)?(string)$value:'');
            }
        }
        return array($product);
    }

    /**
     * @param null $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        return true;
    }

    /**
     * @param null $product
     * @return bool
     */
    public function hasRequiredOptions($product = null)
    {
        return true;
    }

    /**
     * @param null $product
     * @return bool
     */
    public function canConfigure($product = null)
    {
        return true;
    }

    /**
     * @param $product
     * @param $price
     * @return mixed
     */
    public function convertPrice($product, $price)
    {
        $includeTax = (Mage::getStoreConfig('tax/display/type') != 1);
        if (Mage::app()->getStore()->isAdmin())
            $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        else
            $store = Mage::app()->getStore();

        $priceWithTax = Mage::helper('tax')->getPrice($product, $price, $includeTax);
        return $store->convertPrice($priceWithTax);
    }

}
