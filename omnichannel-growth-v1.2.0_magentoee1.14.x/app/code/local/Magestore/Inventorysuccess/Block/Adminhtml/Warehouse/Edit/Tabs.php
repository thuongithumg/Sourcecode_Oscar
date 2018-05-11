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
 * Warehouse Edit Tabs Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouse_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Warehouse Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        /** information form */
        $this->addTab('general_information', array(
            'label' => $this->__('General Information'),
            'title' => $this->__('General Information'),
            'content' => $this->getLayout()
                ->createBlock('inventorysuccess/adminhtml_warehouse_edit_tab_general')
                ->toHtml(),
        ));
        if ($this->getRequest()->getParam('id')) {
            $warehouse = Mage::registry('current_warehouse');
            $permission = Magestore_Coresuccess_Model_Service::permissionService();
            if ($permission->checkPermission(
                'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand', $warehouse
            )) {
                $active = (boolean) $this->getRequest()->getParam('stock_on_hand', false);
                $this->addTab('stock_on_hand', array(
                    'label' => $this->__('Stock On Hand'),
                    'url' => $this->getUrl('*/*/stockonhand', array('_current' => true)),
                    'class' => 'ajax',
                    'active' => $active
                ));
            }
            if ($permission->checkPermission(
                'admin/inventorysuccess/stockcontrol/stock_movement_history', $warehouse
            ))
                $this->addTab('stock_movement', array(
                    'label' => $this->__('Stock Movement'),
                    'url' => $this->getUrl('*/*/stockmovement', array('warehouse_id' => $warehouse->getId())),
                    'class' => 'ajax'
                ));
            if ($permission->checkPermission(
                'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/manage_permission', $warehouse
            ))
                $this->addTab('warehouse_permission', array(
                    'label' => $this->__('Warehouse Permission'),
                    'url' => $this->getUrl('*/*/permission', array('_current' => true)),
                    'class' => 'ajax'
                ));

            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
                $this->addTab('orders', array(
                    'label'     => Mage::helper('customer')->__('Orders'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/orders', array('_current' => true)),
                ));
            }

            $this->addTab('dashboard', array(
                'label' => $this->__('Dashboard'),
                'url' => $this->getUrl('*/*/dashboard', array('_current' => true)),
                'class' => 'ajax'
            ));
        }


        return parent::_beforeToHtml();
    }
}