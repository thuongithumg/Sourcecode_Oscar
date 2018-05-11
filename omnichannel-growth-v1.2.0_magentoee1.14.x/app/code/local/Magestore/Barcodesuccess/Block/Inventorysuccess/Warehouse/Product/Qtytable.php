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
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */


/**
 * @method setProduct($product)
 * @method getProduct()
 * Class Magestore_Barcodesuccess_Block_Inventorysuccess_Warehouse_Product_Qtytable
 */
class Magestore_Barcodesuccess_Block_Inventorysuccess_Warehouse_Product_Qtytable extends
    Mage_Core_Block_Template
{
    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setTemplate('barcodesuccess/inventorysuccess/warehouse/product/qtytable.phtml');
        return parent::_prepareLayout();
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     */
    public function getStocks()
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
                          ->joinProductCollection();
        $collection->addProductIdsToFilter(array($this->getProduct()->getId()));
        return $collection;
    }

    /**
     * @param $warehouseId
     * @return string
     */
    public function getWarehouseName( $warehouseId )
    {
        return Mage::getModel('inventorysuccess/warehouse')->load($warehouseId)->getWarehouseName();
    }

    /**
     * @param $warehouseId
     * @return string
     */
    public function getEditWarehouseUrl( $warehouseId )
    {
        return Mage::helper('adminhtml')->getUrl('*/inventorysuccess_warehouse/edit', array('id' => $warehouseId));
    }

}
