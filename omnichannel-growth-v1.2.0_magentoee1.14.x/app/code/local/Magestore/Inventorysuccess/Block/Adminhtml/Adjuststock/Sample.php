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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Sample
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Sample extends Mage_Adminhtml_Block_Widget_Grid
{
    const SAMPLE_RECORDS = 3;
    const SAMPLE_QTY = 1;

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection() {
        $adjustStockId = $this->getRequest()->getParam('id');
        $adjustStockModel = Mage::getModel('inventorysuccess/adjuststock')->load($adjustStockId);
        if ($adjustStockModel->getId()) {
            $wareHouseId = $adjustStockModel->getData('warehouse_id');
            if ($wareHouseId) {
                $productCollection = Magestore_Coresuccess_Model_Service::warehouseStockService()
                    ->getStocks($wareHouseId)
                    ->setPageSize(self::SAMPLE_RECORDS)
                    ->setCurPage(1);
                $collection = new Varien_Data_Collection();
                foreach ($productCollection as $product) {
                    $rowObj = new Varien_Object();
                    $rowObj->setData('sku', $product->getData('sku'));
                    $rowObj->setData('adjust_qty', 1);
                    $collection->addItem($rowObj);
                }
                $this->setCollection($collection);
            }
        }
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'index' => 'sku'
        ));

        $this->addColumn('adjust_qty', array(
            'header' => Mage::helper('inventorysuccess')->__('QTY'),
            'index' => 'adjust_qty',
        ));
    }

    /**
     * Retrieve Grid data as CSV
     *
     * @return string
     */
    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                            $column->getRowFieldExport($item)) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                            $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

}
