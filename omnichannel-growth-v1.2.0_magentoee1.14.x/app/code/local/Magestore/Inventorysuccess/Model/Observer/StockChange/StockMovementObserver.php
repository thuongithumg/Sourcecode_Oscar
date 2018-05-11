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
 * Stock Change Observer
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Observer_StockChange_StockMovementObserver
{
    /**
     * @var Magestore_Inventorysuccess_Model_Service_StockMovement_StockMovementActionService
     */
    protected $stockMovementActionService;

    /**
     * @var array
     */
    protected $stockMovementConfig;

    /**
     * Magestore_Inventorysuccess_Model_Observer_StockChange constructor.
     */
    public function __construct()
    {
        $this->stockMovementActionService = Magestore_Coresuccess_Model_Service::stockMovementActionService();
        $this->stockMovementConfig = Magestore_Coresuccess_Model_Service::stockMovementProviderService()
            ->getActionConfig();
    }

    public function execute($observer)
    {
        $data = $observer->getData();
        $insertData = $this->processData($data);
        $this->stockMovementActionService->addStockMovementAction($insertData);
        return $this;
    }

    /**
     * process adjustment data to add stock movement
     *
     * @param array $data
     * @return array
     */
    protected function processData($data)
    {
        $insertData = array();
        $actionNumber = $this->getActionNumber($data['action_type'], $data['action_id']);
        foreach ($data['products'] as $productId => $productData) {
            if($productData['adjust_qty'] == $productData['old_qty']) {
                continue;
            }
            $insertData[] = array(
                'product_id' => $productId,
                'product_sku' => $productData['product_sku'],
                'qty' => $productData['adjust_qty'] - $productData['old_qty'],
                'action_code' => $data['action_type'],
                'action_id' => $data['action_id'],
                'action_number' => $actionNumber,
                'warehouse_id' => $data['warehouse_id'],
                'created_at' => date('Y-m-d H:i:s')
            );
        }
        return $insertData;
    }

    /**
     *
     * @param string $actionType
     * @return Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_AbstractService
     */
    protected function getStockMovementActionProvider($actionType)
    {
        $config = $this->stockMovementConfig[$actionType];
        if(!isset($config['class'])) {
            throw new \Exception($this->__('There was an error while saving stock movement.'));
        }
        return $config['class'];
    }

    /**
     * Get action stockchange number 
     * 
     * @param type $data
     */
    protected function getActionNumber($actionType, $actionId)
    {
        return $this->getStockMovementActionProvider($actionType)
            ->getStockMovementActionReference($actionId);
    }

    /**
     *
     * @param array $productIds
     * @return array
     */
    protected function _loadProductData($productIds)
    {
        $productData = array();
        $products = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(array('sku'))
            ->addFieldToFilter('entity_id', array('in' => $productIds));
        if ($products->getSize()) {
            foreach ($products as $product) {
                $productData[$product->getId()] = $product->getData();
            }
        }
        return $productData;
    }
}