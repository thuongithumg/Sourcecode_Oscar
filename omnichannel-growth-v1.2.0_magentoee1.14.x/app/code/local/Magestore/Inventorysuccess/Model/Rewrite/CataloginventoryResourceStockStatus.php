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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */

class Magestore_Inventorysuccess_Model_Rewrite_CataloginventoryResourceStockStatus
    extends Mage_CatalogInventory_Model_Resource_Stock_Status
{
    /**
     * get current stock id
     * 
     * @return int
     */
    public function getStockId()
    {
        return Magestore_Coresuccess_Model_Service::stockService()->getStockId();
    }
    
    /**
     * Save Product Status per website
     *
     * @param Mage_CatalogInventory_Model_Stock_Status $object
     * @param int $productId
     * @param int $status
     * @param float $qty
     * @param int $stockId
     * @param int|null $websiteId
     * @return Mage_CatalogInventory_Model_Resource_Stock_Status
     */
    public function saveProductStatus(Mage_CatalogInventory_Model_Stock_Status $object, $productId, $status, $qty = 0,
        $stockId = 1, $websiteId = null)
    {
        $stockId = $this->getStockId();
        return parent::saveProductStatus($object, $productId, $status, $qty, $stockId, $websiteId);
    }

    /**
     * Retrieve product status
     * Return array as key product id, value - stock status
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductStatus($productIds, $websiteId, $stockId = 1)
    {
        $stockId = $this->getStockId();
        return parent::getProductStatus($productIds, $websiteId, $stockId);
    }

    /**
     * Retrieve product(s) data array
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductData($productIds, $websiteId, $stockId = 1)
    {
        $stockId = $this->getStockId();
        return parent::getProductData($productIds, $websiteId, $stockId);

    }    
    
    /**
     * Add stock status to prepare index select
     *
     * @param Varien_Db_Select $select
     * @param Mage_Core_Model_Website $website
     * @return Mage_CatalogInventory_Model_Resource_Stock_Status
     */
    public function addStockStatusToSelect(Varien_Db_Select $select, Mage_Core_Model_Website $website)
    {
        $websiteId = $website->getId();
        $select->joinLeft(
            array('stock_status' => $this->getMainTable()),
            'e.entity_id = stock_status.product_id AND stock_status.website_id='.$websiteId.
            ' AND stock_status.stock_id=' . $this->getStockId(),
            array('salable' => 'stock_status.stock_status')
        );

        return $this;
    }

    /**
     * Add stock status limitation to catalog product price index select object
     *
     * @param Varien_Db_Select $select
     * @param string|Zend_Db_Expr $entityField
     * @param string|Zend_Db_Expr $websiteField
     * @return Mage_CatalogInventory_Model_Resource_Stock_Status
     */
    public function prepareCatalogProductIndexSelect(Varien_Db_Select $select, $entityField, $websiteField)
    {
        $select->join(
            array('ciss' => $this->getMainTable()),
            "ciss.product_id = {$entityField} AND ciss.website_id = {$websiteField}".
            ' AND ciss.stock_id=' . $this->getStockId(),
            array()
        );
        $select->where('ciss.stock_status = ?', Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);

        return $this;
    }

    /**
     * Add only is in stock products filter to product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Mage_CatalogInventory_Model_Resource_Stock_Status
     */
    public function addIsInStockFilterToCollection($collection)
    {
        $websiteId = Mage::app()->getStore($collection->getStoreId())->getWebsiteId();
        $joinCondition = $this->_getReadAdapter()
            ->quoteInto('e.entity_id = stock_status_index.product_id'
                . ' AND stock_status_index.website_id = ?', $websiteId
            );

        $joinCondition .= $this->_getReadAdapter()->quoteInto(
            ' AND stock_status_index.stock_id = ?',
            $this->getStockId()
        );

        $collection->getSelect()
            ->join(
                array('stock_status_index' => $this->getMainTable()),
                $joinCondition,
                array()
            )
            ->where('stock_status_index.stock_status=?', Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);

        return $this;
    }    
}