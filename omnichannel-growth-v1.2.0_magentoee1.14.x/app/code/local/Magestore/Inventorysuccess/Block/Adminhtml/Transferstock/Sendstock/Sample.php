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
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Sample
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Sample constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('samplegrid');
        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Sendstock_Grid
     */
    protected function _prepareCollection()
    {
        $transferStockId    = $this->getRequest()->getParam('id');
        $transferStockModel = Mage::getModel('inventorysuccess/transferstock')->load($transferStockId);
        if ($transferStockModel->getId()) {
            $wareHouseId       = $transferStockModel->getData('source_warehouse_id');
            $productCollection = Magestore_Coresuccess_Model_Service::warehouseStockService()
                ->getStocks($wareHouseId,null)
                ->setPageSize(3)
                ->setCurPage(1);
            $productCollection->load();
            $this->setCollection($productCollection);
            return parent::_prepareCollection();
        }
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Sendstock_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header' => Mage::helper('inventorysuccess')->__('SKU'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'sku'
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('inventorysuccess')->__('QTY'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'total_qty'
        ));
        return parent::_prepareColumns();
    }

}