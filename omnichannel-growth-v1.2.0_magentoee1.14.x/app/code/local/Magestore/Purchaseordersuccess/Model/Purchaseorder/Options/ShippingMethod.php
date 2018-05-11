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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_ShippingMethod
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    const SHIPPING_METHOD_CONFIG_PATH = 'purchaseordersuccess/shipping_method/shipping_method';
    
    const OPTION_NONE_VALUE = 'os_none_payment_method';
    
    const OPTION_NEW_VALUE = 'os_new_shipping_method';
    
    public function getShippingMethodOptions(){
        $config = Mage::getStoreConfig(self::SHIPPING_METHOD_CONFIG_PATH);
        $shippingMethods = $this->unserializeArray($config);
        $options = array(self::OPTION_NONE_VALUE => Mage::helper('purchaseordersuccess')->__('Select a shipping method'));
        if($shippingMethods)
            foreach ($shippingMethods as $method){
                if($method['status'] == Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Status::ENABLE_VALUE)
                    $options[$method['name']] = $method['name'];
            }
        $options[self::OPTION_NEW_VALUE] = Mage::helper('purchaseordersuccess')->__('New shipping method');
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return array()
     */
    public function getOptionHash()
    {
        return $this->getShippingMethodOptions();
    }
}