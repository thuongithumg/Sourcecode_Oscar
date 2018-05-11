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

use Magestore_Inventorysuccess_Model_StockMovement as StockMovement;
/**
 * Class Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Transfer_Edit_Tab_Items
 */
class Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Transfer_Edit_Tab_Items extends
    Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Transfer_Edit_Tab_Items constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('stock_transfer_items');
        $this->setDefaultSort('stock_movement_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    public function modifyCollection($collection){
        $stockTransferId = $this->getRequest()->getParam('stock_transfer_id');
        $collection = $collection->addFieldToFilter(StockMovement::STOCK_TRANSFER_ID, $stockTransferId);
        return $collection;
    }

    /**
     * @return $this
     */
    protected function modifyColumn(){
        $this->removeColumn('warehouse_id');
        return $this;
    }

    protected function _prepareColumns(){
        parent::_prepareColumns();

        $this->_exportTypes = array();
        $this->addExportType('*/*/exportItemsCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportItemsXml', $this->__('Excel XML'));

        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_stockmovement_transfer/items', array('_current' => true));
    }
}
