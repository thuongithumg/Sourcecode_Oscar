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
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_PaymentMethod
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    const PAYMENT_METHOD_CONFIG_PATH = 'purchaseordersuccess/payment_method/payment_method';

    const OPTION_NEW_VALUE = 'os_new_payment_method';
    
    public function getPaymentMethodOptions(){
        $config = Mage::getStoreConfig(self::PAYMENT_METHOD_CONFIG_PATH);
        $paymentMethods = $this->unserializeArray($config);
        $options = array('' => Mage::helper('purchaseordersuccess')->__('Select a payment method'));
        if($paymentMethods)
            foreach ($paymentMethods as $method){
                if($method['status'] == Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Status::ENABLE_VALUE)
                    $options[$method['name']] = $method['name'];
            }
        $options[self::OPTION_NEW_VALUE] = Mage::helper('purchaseordersuccess')->__('New payment method');
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return array()
     */
    public function getOptionHash()
    {
        return $this->getPaymentMethodOptions();
    }
}