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
 * Inventorysuccess Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Activity_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Activity_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('activityGrid');
        $this->setDefaultSort('activity_product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Toexternal_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventorysuccess/transferstock_activity_product')->getCollection()
            ->addFieldToFilter('activity_id', $this->getRequest()->getParam('activity_id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Toexternal_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('activity_product_id', array(
            'header' => Mage::helper('inventorysuccess')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'activity_product_id'
        ));
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('inventorysuccess')->__('Product SKU'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_sku'
        ));
        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventorysuccess')->__('Product Name'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_name'
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('inventorysuccess')->__('Qty'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'qty'
        ));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Toexternal_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * get grid url (use for ajax load)
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}