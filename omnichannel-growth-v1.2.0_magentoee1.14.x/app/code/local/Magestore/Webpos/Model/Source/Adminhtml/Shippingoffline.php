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

class Magestore_Webpos_Model_Source_Adminhtml_Shippingoffline {

    protected $_allowShippings = array();

    public function __construct() {
        $this->_allowShippings = array('webpos_shipping','flatrate','freeshipping');
    }

    public function toOptionArray() {
        $collection = Mage::getModel('shipping/config')->getActiveCarriers();

        if (!count($collection))
            return;

        $options = array();
        foreach ($collection as $code => $carrier) {
            if (!in_array($code, $this->_allowShippings))
                continue;
            $methods = $carrier->getAllowedMethods();
            if(count($methods) > 0) {
                foreach ($methods as $mcode => $method) {
                    $title = $carrier->getConfigData('title') . ' - ' . $carrier->getConfigData('name');
                    if(($code == 'webpos_shipping') && ($mcode != 'storepickup')){
                        $title = $carrier->getConfigData('title') . ' - ' . $carrier->getConfigData($mcode.'_name');
                    }
                    $options[] = array('value' => $code, 'label' => $title);
                }
            }
        }

        return $options;
    }

    public function getAllowShippingMethods() {
        return $this->_allowShippings;
    }

    public function getOfflineShippingData(){
        $collection = Mage::getModel('shipping/config')->getActiveCarriers();
        $shippingList = array();
        if(count($collection) > 0) {
            foreach ($collection as $code => $carrier) {
                $methods = $carrier->getAllowedMethods();
                if(count($methods) > 0){
                    foreach ($methods as $mcode => $method) {
                        $offlineMethods = Mage::getStoreConfig('webpos/shipping/specificshipping');
                        if (!in_array($code, $this->_allowShippings) || !in_array($code, explode(',', $offlineMethods)))
                            continue;
                        $isDefault = '0';
                        $methodCode = $code.'_'.$mcode;
                        if($methodCode == Mage::getStoreConfig('webpos/shipping/defaultshipping')) {
                            $isDefault = '1';
                        }
                        if($code == 'webpos_shipping'){
                            $methodTitle = $carrier->getConfigData('title').' - '.$carrier->getConfigData('name');
                            $methodPrice = ($carrier->getConfigData('price') != null) ? $carrier->getConfigData('price') : '0';
                            if($mcode != 'storepickup'){
                                $methodTitle = $carrier->getConfigData('title').' - '.$carrier->getConfigData($mcode.'_name');
                                $methodPrice = ($carrier->getConfigData($mcode.'_price') != null) ? $carrier->getConfigData($mcode.'_price') : '0';
                            }
                            $methodPriceType = '';
                            $methodDescription = '0';
                            $methodSpecificerrmsg = '';
                        }else{
                            $methodTitle = $carrier->getConfigData('title').' - '.$carrier->getConfigData('name');
                            $methodPrice = ($carrier->getConfigData('price') != null) ? $carrier->getConfigData('price') : '0';
                            $methodPriceType = ($carrier->getConfigData('type') != null) ? $carrier->getConfigData('type') : '';
                            $methodDescription = ($carrier->getConfigData('description') != null) ?$carrier->getConfigData('description') : '0';
                            $methodSpecificerrmsg = ($carrier->getConfigData('specificerrmsg') != null) ?$carrier->getConfigData('specificerrmsg') : '';
                        }
                        $shippingData = array();
                        $shippingData['code'] = $methodCode;
                        $shippingData['title'] = $methodTitle;
                        $shippingData['price'] = $methodPrice;
                        $shippingData['description'] = $methodDescription;
                        $shippingData['error_message'] = $methodSpecificerrmsg;
                        $shippingData['price_type'] = $methodPriceType;
                        $shippingData['is_default'] = $isDefault;
                        $shippingList[] = $shippingData;
                    }
                }
            }
        }
        return $shippingList;
    }

}
