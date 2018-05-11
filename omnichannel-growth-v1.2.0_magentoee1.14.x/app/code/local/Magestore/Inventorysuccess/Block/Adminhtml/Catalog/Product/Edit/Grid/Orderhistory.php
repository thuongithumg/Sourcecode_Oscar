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
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Grid_Orderhistory
    extends Mage_Adminhtml_Block_Widget_Grid
    //    Mage_Adminhtml_Block_Sales_Order_Grid
{
    //
    //    /**
    //     * Retrieve collection class
    //     *
    //     * @return string
    //     */
    //    protected function _getCollectionClass()
    //    {
    //        return 'inventorysuccess/sales_order_pendingcollection';
    //    }
    //
    //    public function getGridUrl()
    //    {
    //        return $this->getUrl('*/*/*', array('_current'=>true));
    //    }
    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('needtoshipGrid');
        $this->setUseAjax(true);
    }

    /**
     * Init backups collection
     */
    protected function _prepareCollection()
    {
        $productId = $this->getRequest()->getParam('product_id',0);
        $collection = Mage::getSingleton('inventorysuccess/service_sales_pendingOrderItemService')->getCollection($productId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Backup_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('inventorysuccess')->__('Order ID'),
            'index' => 'increment_id',
            'float' => 'left'
        ));
        $this->addColumn('item_id', array(
            'header' => Mage::helper('inventorysuccess')->__('Item ID'),
            'index' => 'item_id',
            'float' => 'left'
        ));
        $this->addColumn('pending_qty', array(
            'header' => Mage::helper('inventorysuccess')->__('Qty need to ship'),
            'index' => 'pending_qty',
            'type' => 'number',
            'float' => 'left',
            'filter_condition_callback' => array($this, '_filterQtyCallback')
        ));
        /*
        $this->addColumn('action',
            array(
                'header' => $this->__('Action'),
                'renderer' => '',
                'index' => 'order_id',
                'align' => 'right',
                'type' => 'action',
                'filter' => false,
                'order' => false,
                'is_system' => true,
            )
        );
        */
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     */
    protected function _filterQtyCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        return Mage::getSingleton('inventorysuccess/service_sales_pendingOrderItemService')->_filterQtyCallback($collection,$column->getId(),$value);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array(
                'store'=>$this->getRequest()->getParam('store'),
                'order_id'=>$row->getOrderId())
        );
    }


}