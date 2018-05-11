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
 * Customercredit Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Adminhtml_Product_View extends Mage_Catalog_Block_Product_View_Abstract
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getCreditAmount($product)
    {
        $creditValue = Mage::helper('customercredit/creditproduct')->getCreditValue($product);
        $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        switch ($creditValue['type']) {
            case 'range':
                $creditValue['from'] = $this->convertPrice($product, $creditValue['from']);
                $creditValue['to'] = $this->convertPrice($product, $creditValue['to']);
                $creditValue['from_txt'] = $store->formatPrice($creditValue['from']);
                $creditValue['to_txt'] = $store->formatPrice($creditValue['to']);
                break;
            case 'dropdown':
                $creditValue['options'] = $this->_convertPrices($product, $creditValue['options']);
                $creditValue['prices'] = $this->_convertPrices($product, $creditValue['prices']);
                $creditValue['prices'] = array_combine($creditValue['options'], $creditValue['prices']);
                $creditValue['options_txt'] = $this->_formatPrices($creditValue['options']);
                break;
            case 'static':
                $creditValue['value'] = $this->convertPrice($product, $creditValue['value']);
                $creditValue['value_txt'] = $store->formatPrice($creditValue['value']);
                $creditValue['price'] = $this->convertPrice($product, $creditValue['credit_price']);
                break;
            default:
                $creditValue['type'] = 'any';
        }

        return $creditValue;
    }

    /**
     * @param $product
     * @param $basePrices
     * @return mixed
     */
    protected function _convertPrices($product, $basePrices)
    {
        foreach ($basePrices as $key => $price) {
            $basePrices[$key] = $this->convertPrice($product, $price);
        }    
        return $basePrices;
    }

    /**
     * @param $product
     * @param $price
     * @return mixed
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ( Mage::getStoreConfig('tax/display/type') != 1 );
        $store = Mage::getSingleton('adminhtml/session_quote')->getStore();

        $priceWithTax = Mage::helper('tax')->getPrice($product, $price, $includeTax);
        return $store->convertPrice($priceWithTax);
    }

    /**
     * @param $prices
     * @return mixed
     */
    protected function _formatPrices($prices)
    {
        $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        foreach ($prices as $key => $price) {
            $prices[$key] = $store->formatPrice($price, false);
        }    
        return $prices;
    }

    /**
     * @return Varien_Object
     */
    public function getFormConfigData()
    {
        $request = Mage::app()->getRequest();
        $action = $request->getRequestedRouteName() . '_' . $request->getRequestedControllerName() . '_' 
            . $request->getRequestedActionName();
        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            $request = Mage::app()->getRequest();
            $options = Mage::getModel('sales/quote_item_option')->getCollection()
                ->addItemFilter($request->getParam('id'));
            $formData = array();
            foreach ($options as $option) {
                $formData[$option->getCode()] = $option->getValue();
            }    
            return new Varien_Object($formData);
        }
        return new Varien_Object();
    }

    /**
     * @return mixed
     */
    public function getPriceFormatJs()
    {
        $priceFormat = Mage::app()->getLocale()->getJsPriceFormat();
        return Mage::helper('core')->jsonEncode($priceFormat);
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getOptionProduct($option){
        $request = Mage::app()->getRequest();
        $action = $request->getRequestedRouteName() . '_' . $request->getRequestedControllerName() . '_'
            . $request->getRequestedActionName();
        if ($action == 'adminhtml_sales_order_create_configureQuoteItems' && $request->getParam('id')) {
            $request = Mage::app()->getRequest();
            $options = Mage::getModel('sales/quote_item_option')->getCollection()
                ->addItemFilter($request->getParam('id'));
            $formData = array();
            foreach ($options as $option_item) {
                $formData[$option_item->getCode()] = $option_item->getValue();
            }
            return (isset($formData[$option]))?$formData[$option]:'';
        }
    }
}
