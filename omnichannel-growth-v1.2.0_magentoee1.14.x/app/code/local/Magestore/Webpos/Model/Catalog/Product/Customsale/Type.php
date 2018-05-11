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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Product Type Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_Webpos_Model_Catalog_Product_Customsale_Type extends Mage_Catalog_Model_Product_Type_Abstract {

    /**
     * @param Varien_Object $buyRequest
     * @param null $product
     * @return array|string
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null) {
        if (version_compare(Mage::getVersion(), '1.5.0', '>=')) {
            return parent::prepareForCart($buyRequest, $product);
        }
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $result = parent::prepareForCart($buyRequest, $product);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareWebPOSProduct($buyRequest, $product);
        return $result;
    }

    /**
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode) {
        if (version_compare(Mage::getVersion(), '1.5.0', '<')) {
            return parent::_prepareProduct($buyRequest, $product, $processMode);
        }
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareWebPOSProduct($buyRequest, $product);
        return $result;
    }

    /**
     * @param Varien_Object $buyRequest
     * @param $product
     * @return array
     */
    protected function _prepareWebPOSProduct(Varien_Object $buyRequest, $product) {
        $options = $buyRequest->getData('options');
        if($options && isset($options['is_virtual'])){
            $product->addCustomOption('is_virtual', $options['is_virtual']);
        }
        if($options && isset($options['tax_class_id'])){
            $product->addCustomOption('tax_class_id', $options['tax_class_id']);
        }
        if($options && isset($options['name'])){
            $product->addCustomOption('name', $options['name']);
            $product->setName($options['name']);
        }
        if($options && isset($options['price'])){
            $product->addCustomOption('price', $options['price']);
        }
        return array($product);
    }

    /**
     * @param null $product
     * @return bool
     */
    public function isVirtual($product = null) {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        if ($isVirtual = $product->getCustomOption('is_virtual')) {
            return (bool) $isVirtual->getValue();
        }
        return true;
    }

    /**
     * @param null $product
     * @return bool
     */
    public function isSalable($product = null)
    {
        $route = Mage::app()->getRequest()->getRouteName();
        $isWebposApi = Mage::helper('webpos/permission')->getCurrentSession();
		return ($route == 'webpos' || $isWebposApi)?parent::isSalable($product):false;
    }
}
