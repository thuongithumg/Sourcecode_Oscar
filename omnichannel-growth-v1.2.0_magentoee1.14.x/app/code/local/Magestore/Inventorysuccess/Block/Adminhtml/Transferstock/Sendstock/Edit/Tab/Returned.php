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
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Edit_Tab_Returned
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     */
    protected $returnedCollection;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Edit_Tab_Receiving constructor.
     */
    public function __construct()
    {
        $transfer = Mage::getModel('inventorysuccess/transferstock')->load($this->getRequest()->getParam('id'));
        if ($transfer->getId() && $transfer->getStatus() != Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED) {
            return;
        }
        parent::__construct();
        $this->setId('returned');
        $this->setDefaultSort('transferstock_product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }


    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getReturnedCollection()->getSize() > 0)
            return parent::_toHtml();
        else
            return '';
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->getChildHtml('return_all');
        $html .= $this->getChildHtml('return_items');
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->getReturnedCollection();
        $this->setCollection($this->returnedCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     * @throws Exception
     */
    protected function getReturnedCollection()
    {
        if (!$this->returnedCollection) {
            /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
            $this->returnedCollection = Mage::getModel('inventorysuccess/transferstock_product')->getCollection()
                ->addFieldToFilter(
                    Magestore_Inventorysuccess_Model_Transferstock_Product::TRANSFERSTOCK_ID,
                    $this->getRequest()->getParam('id')
                )->addFieldToFilter(
                    Magestore_Inventorysuccess_Model_Transferstock_Product::QTY_RETURNED,
                    array('gt' => 0)
                );
        }
        return $this->returnedCollection;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_sku', array(
            'header' => $this->__('SKU'),
            'align' => 'center',
            'width' => '150px',
            'index' => 'product_sku',
        ));
        $this->addColumn('product_name', array(
            'header' => $this->__('Name'),
            'width' => '350px',
            'align' => 'left',
            'index' => 'product_name',
        ));
        $this->addColumn('qty', array(
            'header' => $this->__('Qty Sent'),
            'width' => '10px',
            'type' => 'number',
            'index' => 'qty',
        ));
        $this->addColumn('qty_received', array(
            'header' => $this->__('Qty Received'),
            'width' => '20px',
            'index' => 'qty_received',
            'type' => 'number',
            'name' => 'qty_received',
        ));
        $this->addColumn('differences', array(
            'header' => $this->__('Differences'),
            'index' => 'differences',
            'type' => 'number',
            'width' => '20px',
        ));
        $this->addColumn('qty_returned', array(
            'header' => $this->__('Qty Returned'),
            'index' => 'qty_returned',
            'type' => 'number',
            'width' => '20px',
        ));
        $this->addColumn('qty_remaining', array(
            'header' => $this->__('Qty Remaining'),
            'index' => 'qty_remaining',
            'type' => 'number',
            'width' => '20px',
        ));
        return parent::_prepareColumns();
    }

    public function getSelectedProducts()
    {
        return array();
    }

    /**
     * Grid url getter
     * Version of getGridUrl() but with parameters
     *
     * @param array $params url parameters
     * @return string current grid url
     */
    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/returnedgrid', array(
            '_current' => true,
        ));
    }

    /**
     * @param $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }
}