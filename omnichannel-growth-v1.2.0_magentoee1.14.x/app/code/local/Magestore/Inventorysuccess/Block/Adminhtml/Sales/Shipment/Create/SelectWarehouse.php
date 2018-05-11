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
class Magestore_Inventorysuccess_Block_Adminhtml_Sales_Shipment_Create_SelectWarehouse
    extends Mage_Adminhtml_Block_Template
{
    
    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('inventorysuccess/sales/shipment/create/select_warehouse.phtml');
    }
    
    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }    
    
    /**
     * Get list of availabel warehouses to create shipment
     * 
     * @return array
     */
    public function getAvailableWarehouses()
    {
        if(!$this->hasData('available_warehouses')) {
            $warehouses = Magestore_Coresuccess_Model_Service::shipmentFormService()->getAvailableWarehouses($this->getOrder());
            $warehouses = $this->_formatWarehouseList($warehouses);
            $this->setData('available_warehouses', $warehouses);
        }
        return $this->getData('available_warehouses');
    }
    
    /**
     * @return string
     */
    public function getWarehouseJson()
    {
        return Zend_Json::encode($this->getAvailableWarehouses());
    }
    
    /**
     * Format warehouse list before returning
     * 
     * @param array $warehouses
     * @return array
     */
    protected function _formatWarehouseList($warehouses)
    {
        $formatList = array();
        if(count($warehouses)) {
            foreach($warehouses as $warehouseId => $warehouse){
                $warehouseInfo = $warehouse['info'];
                $stockStatusInfo = '';
                if($warehouse['lack_qty']) {
                    $stockStatusInfo = '('.$this->__('lack %s items', $warehouse['lack_qty']).')';
                }
                $formatList[$warehouseId] = array(
                    'label' => $warehouseInfo['warehouse_code'] .' - '. $warehouseInfo['warehouse_name'] .' '. $stockStatusInfo,
                    'items' => $warehouse['items'],
                    'lack_qty' => $warehouse['lack_qty'],
                    'info' => $warehouse['info'],
                );
            }
        }
        return $formatList;
    }
    
    /**
     * 
     * @return string
     */
    public function getRequestStockUrl()
    {
        return $this->getUrl('*/inventorysuccess_transferstock_requeststock/new', array('order_id' => $this->getOrder()->getIncrementId()));
    }    
}