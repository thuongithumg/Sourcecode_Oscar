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
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Delete_Grid 
    extends Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_AbstractGridProduct
{
    /**
     * @var string
     */
    protected $hiddenInputField = 'delete_products';

    /**
     * @var array
     */
    protected $editFields = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouse_stock_delete_list');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    public function modifyCollection($collection){
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        if($warehouseId)
            $collection->addWarehouseToFilter($warehouseId)->getCanDeleteProducts();
        else
            $collection = $collection->addFieldToFilter('product_id', 0);
        return $collection;
    }

    protected function getParamsUrl(){
        $params = array('_current' => true);
        if(!$this->getRequest()->getParam('warehouse_id')){
            $params['warehouse_id'] = $this->getRequest()->getParam('id');
        }
        return $params;
    }

    /**
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_stockonhand_delete/grid', $this->getParamsUrl());
    }

    public function getSaveUrl(){
        return $this->getUrl('*/inventorysuccess_warehouse_stockonhand_delete/save', $this->getParamsUrl());
    }
}