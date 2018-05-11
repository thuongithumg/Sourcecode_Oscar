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
 * Warehouse Edit Stock On Hand Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockmovement
    extends Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Grid
{
    public function modifyCollection($collection){
        $collection->addWarehouseToFilter($this->getRequest()->getParam('warehouse_id'));
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
        $this->addExportType('*/inventorysuccess_warehouse_stockmovement/exportCsv', $this->__('CSV'));
        $this->addExportType('*/inventorysuccess_warehouse_stockmovement/exportXml', $this->__('Excel XML'));
        
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_stockmovement/grid', array('_current' => true));
    }
}