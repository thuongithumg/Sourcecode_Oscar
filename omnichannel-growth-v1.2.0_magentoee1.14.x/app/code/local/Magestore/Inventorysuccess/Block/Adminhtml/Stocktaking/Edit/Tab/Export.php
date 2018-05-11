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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Products
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Export extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Products constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('stocktakingproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * prepare product collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('inventorysuccess/stocktaking_product')->getCollection();
        $stocktakingId = $this->getRequest()->getParam('id');
        $productCollection = $collection->getDifferentProducts($stocktakingId);
        $this->setCollection($productCollection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header' => Mage::helper('inventorysuccess')->__('Product Name'),
            'align' => 'left',
            'name' => 'name',
            'width' => '550',
            'index' => 'name'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('inventorysuccess')->__('SKU'),
            'width' => '80px',
            'name' => 'sku',
            'index' => 'sku'
        ));

        $this->addColumn('old_qty', array(
            'header' => Mage::helper('inventorysuccess')->__('Qty in Warehouse'),
            'index' => 'old_qty',
            'name' => 'old_qty'
        ));

        $this->addColumn('stocktaking_qty', array(
            'header' => Mage::helper('inventorysuccess')->__('Counted Qty'),
            'type' => 'number',
            'index' => 'stocktaking_qty',
            'width' => '80px',
            'default' => '0'
        ));
        $this->addColumn('stocktaking_reason', array(
            'header' => Mage::helper('inventorysuccess')->__('Reason of discrepancy'),
            'index' => 'stocktaking_reason',
        ));
    }

    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array(
            '_current' => true,
            'id' => $this->getRequest()->getParam('id'),
            'store' => $this->getRequest()->getParam('store')
        ));
    }

    /**
     * get row url
     *
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * get currrent store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

}
