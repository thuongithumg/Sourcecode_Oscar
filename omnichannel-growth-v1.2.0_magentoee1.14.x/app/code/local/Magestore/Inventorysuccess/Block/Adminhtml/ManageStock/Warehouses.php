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
 * Inventorysuccess Warehouses Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Warehouses extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/managestock/warehouse.phtml';
    
    public function getHeaderText()
    {
        return $this->__('Manage Stock');
    }

    /**
     * @return array
     */
    public function getOptionWarehouses(){
        $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
        $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
            $collection, 'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand'
        );
        return $collection->getItems();
    }
}