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
 * Purchaseorder Service
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_PaymentTerm as PaymentTermOption;

class Magestore_Purchaseordersuccess_Model_Service_Config_PaymentTerm
    extends Magestore_Purchaseordersuccess_Model_Service_Config_AbstractConfig
{
    const PURCHASE_ORDER_CONFIG_PATH = 'purchaseordersuccess/payment_term/payment_term';
    
    /**
     * @var string
     */
    protected $errorMessage = 'Please enter payment term.';

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return string
     */
    protected function initNewConfig($purchaseOrder){
        if(!$purchaseOrder->getPaymentTerm())
            $purchaseOrder->setPaymentTerm(PaymentTermOption::OPTION_NONE_VALUE);
        if($purchaseOrder->getPaymentTerm() == PaymentTermOption::OPTION_NEW_VALUE){
            $purchaseOrder->setPaymentTerm($purchaseOrder->getData('new_payment_term'));
        }
        return $purchaseOrder->getPaymentTerm();
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function isNoneValueMethod($purchaseOrder){
        if($purchaseOrder->getPaymentTerm() == PaymentTermOption::OPTION_NONE_VALUE)
            return true;
        return false;
    }

    /**
     * Generate new element config.
     *
     * @param string $newConfig
     * @return array
     */
    public function generateNewConfig($newConfig){
        return array(
            'name' => $newConfig,
            'description' => '',
            'status' => Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Status::ENABLE_VALUE
        );
    }
}