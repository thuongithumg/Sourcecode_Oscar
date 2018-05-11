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
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Edit_Tab_Stocksummary
    extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Edit_Tab_Stocksummary constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productList');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setChild('download_summary',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label' => Mage::helper('adminhtml')->__('Download Summary'),
                                           'onclick' => "location.href ='" . $this->getUrl('*/*/exportSummary',
                                                                                           array( '_current' => true )) . "'",
                                           'class' => 'task'
                                       ))
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->getChildHtml('download_summary');
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getModel('inventorysuccess/transferstock_product')->getCollection()
                          ->addFieldToFilter('transferstock_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => Mage::helper('catalog')->__('Product ID'),
            'align' => 'center',
            'type' => 'number',
            'width' => '150px',
            'index' => 'product_id',
            'name' => 'product_id'
        ));
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'align' => 'center',
            'width' => '150px',
            'index' => 'product_sku',
            'name' => 'product_sku'
        ));
        $this->addColumn('product_name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'width' => '350px',
            'index' => 'product_name',
            'name' => 'product_name'
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('catalog')->__('Qty Transfered'),
            'index' => 'qty',
            'type' => 'number',
            'name' => 'qty',
            'width' => '20px',
        ));
        return parent::_prepareColumns();
    }
}