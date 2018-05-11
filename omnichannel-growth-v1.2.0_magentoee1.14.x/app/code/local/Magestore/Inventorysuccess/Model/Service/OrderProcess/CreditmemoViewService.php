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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_OrderProcess_CreditmemoViewService
{
    
    protected $returnItems = array();
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Creditmemo_Item $creditmemoItem
     * @return Magestore_Inventorysuccess_Model_StockMovement
     */
    public function getReturnActivity($creditmemoItem)
    {
        if(!isset($this->returnItems[$creditmemoItem->getId()])) {
            $productId = $creditmemoItem->getProductId();
            if($creditmemoItem->getOrderItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                foreach($creditmemoItem->getOrderItem()->getChildrenItems() as $childItem) {
                    $productId = $childItem->getProductId();
                    break;
                }
            }
            $returnActivity = Mage::getModel('inventorysuccess/stockMovement')
                                ->getCollection()
                                ->addFieldToFilter('product_id', $productId)
                                ->addFieldToFilter('action_number', $creditmemoItem->getCreditmemo()->getIncrementId())
                                ->addFieldToFilter('action_code', Magestore_Inventorysuccess_Model_Service_StockMovement_Activity_SalesRefundService::STOCK_MOVEMENT_ACTION_CODE)
                                ->setPageSize(1)
                                ->setCurPage(1)
                                ->getFirstItem();
            $this->returnItems[$creditmemoItem->getId()] = $returnActivity;
        }
        return $this->returnItems[$creditmemoItem->getId()];
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Creditmemo_Item $creditmemoItem
     * @return Magestore_Inventorysuccess_Model_Warehouse|null
     */
    public function getReturnWarehouse($creditmemoItem)
    {
        $returnActivity = $this->getReturnActivity($creditmemoItem);
        if($returnActivity->getId()) {
            return Mage::getModel('inventorysuccess/warehouse')->load($returnActivity->getWarehouseId());
        }
        return null;
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Creditmemo_Item $creditmemoItem
     * @return float|null
     */    
    public function getReturnQty($creditmemoItem)
    {
        $returnActivity = $this->getReturnActivity($creditmemoItem);
        return $returnActivity->getQty() * 1;
    }
}