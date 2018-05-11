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
class Magestore_Inventorysuccess_Block_Adminhtml_Sales_Creditmemo_View_Items_Column_Warehouse
    extends Mage_Adminhtml_Block_Sales_Items_Column_Default
{
    
    /**
     * 
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getWarehouse()
    {
        if(!$this->hasData('warehouse')) {
            $item = $this->_getData('item');
            $warehouse = Magestore_Coresuccess_Model_Service::creditmemoViewService()
                                ->getReturnWarehouse($item);
            $this->setData('warehouse', $warehouse);
        }
        return $this->getData('warehouse');
    }
    
    /**
     * 
     * @return string
     */
    public function getWarehouseInfo()
    {
        $warehouse = $this->getWarehouse();
        return $warehouse->getWarehouseName() .
                ' (<a target="_blank" href="'.$this->getUrl('*/inventorysuccess_warehouse/edit', array('id' => $warehouse->getId())).'">'. 
                $warehouse->getWarehouseCode().'</a>)';
    }
    
    /**
     * 
     * @return float|null
     */
    public function getReturnQty()
    {
        $item = $this->_getData('item');
        return Magestore_Coresuccess_Model_Service::creditmemoViewService()
                            ->getReturnQty($item);        
    }
    
    /**
     * @return float
     */
    public function getRefundedQty()
    {
        $item = $this->_getData('item');
        return $item->getQty() * 1; 
    }
}