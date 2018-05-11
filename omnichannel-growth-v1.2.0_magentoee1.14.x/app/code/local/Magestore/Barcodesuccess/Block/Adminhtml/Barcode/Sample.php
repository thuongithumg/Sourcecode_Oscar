<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Sample extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Sample constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('barcodeSampleGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection()
                          ->setCurPage(1)->setPageSize(3)->load();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareColumns()
    {

        if ( !$this->getForProduct() ) {
            $this->addColumn('sku', array(
                'header' => Mage::helper('barcodesuccess')->__('SKU'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'sku',
            ));
        }

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE, array(
            'header'   => Mage::helper('barcodesuccess')->__('BARCODE'),
            'align'    => 'left',
            'width'    => '250px',
            'renderer' => 'barcodesuccess/adminhtml_barcode_sample_renderer_barcode',
        ));


        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::QTY, array(
            'header'   => Mage::helper('barcodesuccess')->__('QTY'),
            'align'    => 'left',
            'width'    => '250px',
            'renderer' => 'barcodesuccess/adminhtml_barcode_sample_renderer_qty',
        ));

        if ( !$this->getForProduct() ) {
            $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE, array(
                'header' => Mage::helper('barcodesuccess')->__('SUPPLIER'),
                'width'  => '250px',
                'index'  => Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE,
            ));

            $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME, array(
                'header' => Mage::helper('barcodesuccess')->__('PURCHASE_TIME'),
                'width'  => '250px',
                'index'  => Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME,
                //            'renderer' => 'barcodesuccess/adminhtml_barcode_sample_renderer_purchasedtime',
            ));
        }

        return parent::_prepareColumns();
    }
}