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
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Sales_Creditmemo_Create_Items_Column_Qty
    extends Mage_Adminhtml_Block_Sales_Items_Column_Qty
{
    /**
     * Get list of available warehouses to return items
     * 
     * @return array
     */
    public function getAvailableWarehouses()
    {
        return Magestore_Coresuccess_Model_Service::creditmemoFormService()->getAvailableWarehouses();
    }
    
    /**
     * 
     * @param int $warehouseId
     * @param int $itemId
     * @return bool
     */
    public function isSelectedWarehouse($warehouseId, $itemId)
    {
        $creditMemoData = $this->getRequest()->getParam('creditmemo');
        $selectedWarehouse = null;
        if(isset($creditMemoData['items'][$itemId]['warehouse']))
            $selectedWarehouse = $creditMemoData['items'][$itemId]['warehouse'];
        
        if($selectedWarehouse == $warehouseId) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return boolean
     */
    public function isRequired($item)
    {
        $creditMemoData = $this->getRequest()->getParam('creditmemo');
        $refundQty = 0;
        if(isset($creditMemoData['items'][$item->getItemId()]['qty'])) {
            $refundQty = $creditMemoData['items'][$item->getItemId()]['qty'];
        } else {
            $refundQty = $item->getQtyToRefund();
        }
        if(!$refundQty) {
            return false;
        }
        return true;
    }
    
    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function isShow($item)
    {
        $creditMemoData = $this->getRequest()->getParam('creditmemo');
        $refundQty = 0;
        if(isset($creditMemoData['items'][$item->getItemId()]['qty'])) {
            $refundQty = $creditMemoData['items'][$item->getItemId()]['qty'];
        } else {
            $refundQty = $item->getQtyToRefund();
        }
        
        if(!$refundQty) {
            return false;
        }
        return true;        
    }    
    
    /**
     * 
     * @return string
     */
    public function getNoShippedQtyNotice()
    {
        return $this->__('No shipped item');
    }
    
    /**
     * 
     * @return string
     */
    public function getWarehouseSelector()
    {
        return $this->getLayout()
                ->createBlock('inventorysuccess/adminhtml_sales_creditmemo_create_items_column_qty')
                ->setItem($this->getItem())
                ->setTemplate('inventorysuccess/sales/creditmemo/create/items/column/warehouse.phtml')
                ->toHtml();
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return float
     */
    public function getQtyToShip($item) 
    {
        if($item->getParentItem() && !$item->isShipSeparately()) {
            $qtyToShip = $item->getParentItem()->getQtyToShip() * $item->getQtyOrdered() / $item->getParentItem()->getQtyOrdered();
            return $qtyToShip * 1;
        }
        return $item->getQtyToShip();;
    }
    
    /**
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function isShowWarehouseSelector($item)
    {
        if($item->getIsVirtual()) {
            return false;
        }
        return true;
    }
}