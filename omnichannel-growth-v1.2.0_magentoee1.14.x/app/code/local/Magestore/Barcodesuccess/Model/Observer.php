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
class Magestore_Barcodesuccess_Model_Observer extends
    Varien_Object
{
    /**
     * ass Print Action to product grid
     * @param $observer
     * @return $this
     */
    public function catalogProductGridPrepareMassaction( $observer )
    {
        $block = $observer->getEvent()->getBlock();
        $block->getMassactionBlock()->addItem('print_barcode', array(
            'label'   => Mage::helper('barcodesuccess')->__('Print Barcode Labels'),
            'url'     => Mage::helper('adminhtml')->getUrl('*/barcodesuccess_barcode/massPrintBarcodes', array('_current' => true)),
            'confirm' => Mage::helper('barcodesuccess')->__('Are you sure?'),
        ));
        return $this;
    }

    /**
     * add barcode tab in product edit
     * @param $observer
     * @return $this
     */
    public function prepareLayoutAfter( $observer )
    {
        $block = $observer->getEvent()->getBlock();
        if ( $block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs ) {
            $product = $block->getProduct();
            if ( !($setId = $product->getAttributeSetId()) ) {
                $setId = Mage::app()->getRequest()->getParam('set', null);
            }
            if ( $setId ) {
                $block->addTab('set', array(
                    'label' => Mage::helper('catalog')->__('Barcode'),
                    'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/barcodesuccess_product/rendertab', array(
                        '_current' => true,
                    )),
                    'class' => 'ajax',
                    'after' => 'inventory'
                ));
            }
        }
        return $this;
    }

    /**
     *  save barcodes when save product
     * @param $observer
     * @return $this
     */
    public function postdispatchAdminhtmlCatalogProductSave( $observer )
    {
        /** @var Mage_Adminhtml_Controller_Action $action */
        $action   = $observer->getEvent()->getData('controller_action');
        $barcodes = $action->getRequest()->getParam('barcodes');
        if ( $barcodes ) {
            $barcodes = Mage::helper('adminhtml/js')->decodeGridSerializedInput($barcodes);
            foreach ( $barcodes as $item ) {
                $model = Mage::getModel('barcodesuccess/barcode')
                             ->load($item['barcode_id']);
                $model->setBarcode($item['barcode'])->save();
            }
        }
        return $this;
    }
}