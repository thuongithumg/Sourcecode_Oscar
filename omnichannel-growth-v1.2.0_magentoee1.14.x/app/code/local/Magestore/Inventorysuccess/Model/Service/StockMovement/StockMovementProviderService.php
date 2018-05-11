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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Observer Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_StockMovement_StockMovementProviderService
{
    /**
     * @var array
     */
    protected $actionConfig;

    /**
     * Get all action config of stock movement
     *
     * @return array
     */
    public function getActionConfig()
    {
        if (!$this->actionConfig) {
            $this->actionConfig = array();
            $activityServices = Mage::getStoreConfig('inventorysuccess/stockmovement/activity');
            foreach ($activityServices as $key => $value) {
                $service = Mage::getModel($value);
                if ($service) {
                    $this->actionConfig[$service->getStockMovementActionCode()] = array(
                        'label' => $service->getStockMovementActionLabel(),
                        'class' => $service
                    );
                }
            }
        }
        return $this->actionConfig;
    }

    /**
     * option hash from key to value of an action config
     *
     * @return array
     */
    public function toActionOptionHash()
    {
        $result = array();
        foreach ($this->getActionConfig() as $key => $value) {
            $result[$key] = $value['label'];
        }
        return $result;
    }
}
