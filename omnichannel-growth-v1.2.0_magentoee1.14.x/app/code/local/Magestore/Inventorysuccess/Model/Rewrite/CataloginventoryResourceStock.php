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

class Magestore_Inventorysuccess_Model_Rewrite_CataloginventoryResourceStock 
    extends Mage_CatalogInventory_Model_Resource_Stock
{
    /**
     * add join to select only in stock products
     *
     * @param Mage_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @return Mage_CatalogInventory_Model_Resource_Stock
     */
    public function setInStockFilterToCollection($collection)
    {
        $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $stockFilter = ' AND {{table}}.stock_id=' . $this->getStock()->getId();
        $cond = array(
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=1' . $stockFilter,
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0' . $stockFilter,
        );

        if ($manageStock) {
            $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=1' . $stockFilter;
        } else {
            $cond[] = '{{table}}.use_config_manage_stock = 1' . $stockFilter;
        }

        $collection->joinField(
            'inventory_in_stock',
            'cataloginventory/stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '(' . join(') OR (', $cond) . ')'
        );
        return $this;
    }    
    
    /**
     * 
     * @return Mage_CatalogInventory_Model_Stock
     */
    public function getStock()
    {
        if(!$this->_stock) {
            $this->_stock = Mage::getModel('cataloginventory/stock');
        }
        return $this->_stock;
    }
}