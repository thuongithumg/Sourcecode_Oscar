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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_PaymentMethod as PaymentMethodOption;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice_Payment as PaymentMethod;

class Magestore_Purchaseordersuccess_Model_Service_Config_PaymentMethod
{
    const PURCHASE_ORDER_CONFIG_PATH = 'purchaseordersuccess/payment_method/payment_method';
    
    /**
     * Save new payment method
     *
     * @param array $params
     * @return array
     */
    public function saveConfig($params = array()){
        if(!isset($params[PaymentMethod::PAYMENT_METHOD])
            || $params[PaymentMethod::PAYMENT_METHOD] == PaymentMethodOption::OPTION_NEW_VALUE){
            $params = $this->initNewConfig($params);
            $this->initAllConfigValue($params);
        }
        return $params;
    }

    /**
     * @param array $params
     * @return mixed
     */
    protected function initNewConfig($params = array()){
        $params[PaymentMethod::PAYMENT_METHOD] = $params['new_'.PaymentMethod::PAYMENT_METHOD];
        return $params;
    }

    /**
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    protected function initAllConfigValue($params = array()){
        $configValue = Mage::getStoreConfig(static::PURCHASE_ORDER_CONFIG_PATH);
        if(!is_array($configValue)){
            $configValue = unserialize($configValue);
            $configValue = !$configValue?array():$configValue;
            $currentConfig = $this->searchSubArray($configValue, 'name', $params[PaymentMethod::PAYMENT_METHOD]);
            if(!is_array($currentConfig)){
                $this->saveNewConfig($configValue, $params[PaymentMethod::PAYMENT_METHOD]);
            }
        }
    }

    /**
     * @param array $configValue
     * @param $newConfig
     * @throws \Exception
     */
    protected function saveNewConfig($configValue, $newConfig){
        $configValue[Mage::app()->getLocale()->date()->getTimestamp()] = $this->generateNewConfig($newConfig);
        try{
            Mage::getConfig()->saveConfig(
                self::PURCHASE_ORDER_CONFIG_PATH,
                serialize($configValue)
            );
        }catch(\Exception $e){
            throw $e;
        }
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
            'status' => Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Status::ENABLE_VALUE
        );
    }

    /**
     * Search an subarray with key and value itself
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array|null
     */
    public function searchSubArray($array, $key, $value) {
        foreach ($array as $subarray){
            if (isset($subarray[$key]) && $subarray[$key] == $value)
                return $subarray;
        }
    }
}