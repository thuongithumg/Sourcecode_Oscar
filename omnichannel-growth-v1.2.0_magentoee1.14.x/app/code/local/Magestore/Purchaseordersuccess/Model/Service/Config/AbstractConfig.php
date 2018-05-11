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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_ShippingMethod as ShippingMethodOption;

class Magestore_Purchaseordersuccess_Model_Service_Config_AbstractConfig
    extends Magestore_Purchaseordersuccess_Model_Service_AbstractService
{
    protected $count = 0;
    
    const PURCHASE_ORDER_CONFIG_PATH = 'purchaseordersuccess/shipping_method/shipping_method';

    /**
     * @var string
     */
    protected $errorMessage = 'Please enter shipping method.';

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return Magestore_Purchaseordersuccess_Model_Purchaseorder
     * @throws \Exception
     */
    public function saveConfig($purchaseOrder){
        $newConfig = $this->initNewConfig($purchaseOrder);
        if(!$newConfig || $newConfig == '')
            throw new \Exception(Mage::helper('purchaseordersuccess')->__($this->errorMessage));
        $this->initAllConfigValue($purchaseOrder, $newConfig);
        return $purchaseOrder;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return string
     */
    protected function initNewConfig($purchaseOrder){
        if(!$purchaseOrder->getShippingMethod())
            $purchaseOrder->setShippingMethod(ShippingMethodOption::OPTION_NONE_VALUE);
        if($purchaseOrder->getShippingMethod() == ShippingMethodOption::OPTION_NEW_VALUE)
            $purchaseOrder->setShippingMethod($purchaseOrder->getData('new_shipping_method'));
        return $purchaseOrder->getShippingMethod();
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return bool
     */
    public function isNoneValueMethod($purchaseOrder){
        if($purchaseOrder->getShippingMethod() == ShippingMethodOption::OPTION_NONE_VALUE)
            return true;
        return false;
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @param string $newConfig
     * @throws \Exception
     */
    protected function initAllConfigValue($purchaseOrder, $newConfig){
        if($this->isNoneValueMethod($purchaseOrder))
            return $this;
        $configValue = Mage::getStoreConfig(static::PURCHASE_ORDER_CONFIG_PATH);
        if(!is_array($configValue)){
            $configValue = unserialize($configValue);
            $configValue = !$configValue?array():$configValue;
            $currentConfig = $this->searchSubArray($configValue, 'name', $newConfig);
            if(!is_array($currentConfig)){
                $this->saveNewConfig($configValue, $newConfig);
            }
        }
    }

    /**
     * @param array $configValue
     * @param $newConfig
     * @throws \Exception
     */
    protected function saveNewConfig($configValue, $newConfig){
        $date = Mage::getModel('core/date');
        $id = '_' . $date->gmtTimestamp() . '_' . rand(0,999);
        $configValue[$id] = $this->generateNewConfig($newConfig);
        try{
            Mage::getConfig()->saveConfig(
                static::PURCHASE_ORDER_CONFIG_PATH,
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