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
class Magestore_Inventorysuccess_Block_Adminhtml_Sales_Shipment_View_Warehouse
    extends Mage_Adminhtml_Block_Template
{
    
    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('inventorysuccess/sales/shipment/view/warehouse.phtml');
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
     * Get ship Warehouse
     * 
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getShipWarehouse()
    {
        if(!$this->hasData('ship_warehouse')) {
            $warehouse = Magestore_Coresuccess_Model_Service::shipmentViewService()->getShipWarehouse($this->getShipment()->getId());
            $this->setData('ship_warehouse', $warehouse);
        }
        return $this->getData('ship_warehouse');
    }
    
    /**
     * 
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     * @return string
     */
    public function getWarehouseDisplay($warehouse)
    {
        $html = '';
        if($warehouse->getId()) {
            $html .= $warehouse->getWarehouseName();
            $html .= ' (<a href="'.  $this->getUrl('*/inventorysuccess_warehouse/edit', array('id' => $warehouse->getId())) .'" target="_blank">';
            $html .= $warehouse->getWarehouseCode();
            $html .= '</a>)';
        } else {
            $html .= ': '. $this->__('N/A');
        }
        return $html;
    } 
}