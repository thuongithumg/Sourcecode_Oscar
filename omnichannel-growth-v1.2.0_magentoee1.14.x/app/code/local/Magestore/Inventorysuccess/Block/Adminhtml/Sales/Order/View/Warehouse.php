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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Sales_Order_View_Warehouse
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Sales_Order_View_Warehouse
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Get current warehouse
     *
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getWarehouse()
    {
        $warehouse = Mage::registry('current_warehouse');
        if (!$warehouse) {
            $warehouseId = $this->getOrder()->getWarehouseId();
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            Mage::register('current_warehouse', $warehouse);
        }
        return $warehouse;
    }

    /**
     *
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     * @return string
     */
    public function getWarehouseDisplay($warehouse)
    {
        $html = '';
        if ($warehouse->getId()) {
            $html .= '<a href="' . $this->getUrl('adminhtml/inventorysuccess_warehouse/edit', array('id' => $warehouse->getId())) . '" target="_blank">';
            $html .= $warehouse->getWarehouseName() . ' (' . $warehouse->getWarehouseCode();
            $html .= ')</a>';
        }
        return $html;
    }

    /**
     * Check current user can change warehouse for order
     *
     * @return bool|mixed
     */
    public function canChangeWarehouse()
    {
        return $this->getOrder()->canShip() &&
        Magestore_Coresuccess_Model_Service::orderProcessService()->canChangeOrderWarehouse();
    }

    /**
     * Get warehouse list to change
     *
     * @return array
     */
    public function getWarehouseOptions()
    {
        return Magestore_Inventorysuccess_Model_Warehouse_Options_Warehouse::getOptionArray();
    }

    /**
     * Get Change Warehouse Url
     *
     * @return string
     */
    public function getChangeWarehouseUrl()
    {
        return $this->getUrl(
            'adminhtml/inventorysuccess_order/changeWarehouse',
            array(
                'order_id' => $this->getOrder()->getId(),
                'warehouse_id' => 'selected_warehouse_id'
            )
        );
    }
}