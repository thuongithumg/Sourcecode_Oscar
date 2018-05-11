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
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Edit_Tab_Differences
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     */
    protected $differenceCollection;

    protected $_selectedProducts;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Edit_Tab_Receiving constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('differences');
        $this->setDefaultSort('transferstock_product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setChild('download_shortfall',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Download Shortfall List'),
                    'onclick' => "location.href ='" . $this->getUrl('*/*/exportShortfall', array('_current' => true)) . "'",
                    'class' => 'task',
                ))
        );

        if ( $this->getDifferenceCollection()->getSize() > 0) {
            $messsage = $this->__('Are you sure you want to return all remaining items to source warehouse?');
            $returnAllUrl = $this->getUrl('*/*/returnAll', array('_current' => true));
            $this->setChild('return_all',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('adminhtml')->__('Return All'),
                        'onclick' => "if(confirm('$messsage')){location.href = '$returnAllUrl'}",
                        'class' => 'back',
                    ))
            );
            $this->setChild('return_items',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('adminhtml')->__('Return Items'),
                        'onclick' => "editForm.submit($('edit_form').action+'back/edit/step/return_items');",
                        'class' => 'back'
                    ))
            );

            $this->setChild('return_items_by_new_send_stock',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('adminhtml')->__('Return by send stock'),
                        'onclick' => "editForm.submit($('edit_form').action+'back/edit/step/return_items_by_send_stock');",
                        'class' => 'back'
                    ))
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->getChildHtml('download_shortfall');
        if($this->editAble()) {
            //$html .= $this->getChildHtml('return_all');
            $html .= $this->getChildHtml('return_items');
            $html .= $this->getChildHtml('return_items_by_new_send_stock');
        }
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->getDifferenceCollection();
        $this->setCollection($this->differenceCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     * @throws Exception
     */
    protected function getDifferenceCollection()
    {
        //if (!$this->differenceCollection) {
            /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
            $this->differenceCollection = Mage::getModel('inventorysuccess/transferstock_product')->getCollection()
                ->addFieldToFilter('transferstock_id', $this->getRequest()->getParam('id'))
                ->getDifferences()
                ->getRemainingItems();
        //}
        return $this->differenceCollection;
    }
    /**
     * prepare column to filter
     *
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_returning') {
            $transfer_productIds = $this->getSelectedProducts();
            if (empty($transfer_productIds)) {
                $transfer_productIds = 0;
            }
            if ($column->getFilter()->getValue() == 1) {
                $this->getCollection()->addFieldToFilter('product_id', array('in' => $transfer_productIds));
            } elseif($column->getFilter()->getValue() == 0) {
                $this->getCollection()->addFieldToFilter('product_id', array('nin' => $transfer_productIds));
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_returning', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'align' => 'center',
            'width' => '10px',
            'index' => 'product_id',
            'use_index' => true,
            'values' => $this->getSelectedProducts(),
            'filter'       => false
        ));
        $this->addColumn('product_sku_lable', array(
            'header' => $this->__('SKU'),
            'align' => 'center',
            'width' => '150px',
            'index' => 'product_sku',
        ));
        $this->addColumn('product_sku', array(
            'header' => $this->__(' '),
            'align' => 'center',
            'width' => '150px',
            'index' => 'product_sku',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));
        $this->addColumn('product_name_lable', array(
            'header' => $this->__('Name'),
            'width' => '350px',
            'align' => 'left',
            'index' => 'product_name',
        ));
        $this->addColumn('product_name', array(
            'header' => $this->__(' '),
            'width' => '350px',
            'align' => 'left',
            'index' => 'product_name',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
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
        $this->addColumn('new_qty', array(
            'header' => Mage::helper('catalog')->__('Qty to Return'),
            'name' => 'new_qty',
            'type' => 'input',
            'width' => '20px',
            'editable' => true,
            'edit_only' => true,
            'filter' => false,
            'inline_css' => 'validate-number validate-zero-or-greater',
        ));
        return parent::_prepareColumns();
    }

    public function getSelectedProducts()
    {
        if ($products = $this->getRequest()->getParam('products_selected'))
            return $products;

        if ( !$this->_selectedProducts ) {
            $data = array();
            if ( Mage::getSingleton('adminhtml/session')->getData('send_shortfall_products') ) {
                $data = Mage::getSingleton('adminhtml/session')->getData('send_shortfall_products');
            }
            $this->_selectedProducts = $data;
        }
        return array_keys($this->_selectedProducts);
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
        return $this->getUrl('*/*/differencesgrid', array(
            '_current' => true,
        ));
    }

    public function isComplete(){
        $transfer = Mage::getModel('inventorysuccess/transferstock')->load(Mage::app()->getRequest()->getParam('id'));
        return $transfer->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED;
    }

    public function editAble(){
        $transfer = Mage::getModel('inventorysuccess/transferstock')->load(Mage::app()->getRequest()->getParam('id'));
        return $transfer->getStatus() != Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED && Mage::helper('inventorysuccess')->hasPermission($transfer->getData('des_warehouse_id'));
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