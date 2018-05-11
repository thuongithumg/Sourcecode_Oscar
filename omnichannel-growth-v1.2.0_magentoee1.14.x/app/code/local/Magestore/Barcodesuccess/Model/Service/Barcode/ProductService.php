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
 * Class Magestore_Barcodesuccess_Model_Service_Barcode_ProductService
 */
class Magestore_Barcodesuccess_Model_Service_Barcode_ProductService
{
    /**
     * get Qty
     * @param $product
     * @return float
     */
    public function getQty( $product )
    {
        return $this->getStockItem($product)->getQty();
    }

    /**
     * get in-stock status
     * @param $product
     * @return bool|int
     */
    public function getIsInStock( $product )
    {
        return $this->getStockItem($product)->getIsInStock();
    }

    /**
     * @param $product
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem( $product )
    {
        return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
    }

    /**
     * get QTY html ( if inventory is installed, return a table html ).
     * @param $product
     * @return string
     */
    public function getQtyHtml( $product )
    {
        $inventoryInstalled = Mage::helper('barcodesuccess')->inventoryInstalled();
        if ( $inventoryInstalled ) {
            return Mage::getBlockSingleton('barcodesuccess/inventorysuccess_warehouse_product_qtytable')
                       ->setProduct($product)
                       ->toHtml();
        } else {
            return $this->getQty($product) . '';
        }
    }

    /**
     * @param $product
     * @return string
     */
    public function getThumbnailHtml( $product )
    {
        try {
            $thumbnail = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(170);
        } catch ( Exception $e ) {
            $thumbnail = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg', array('_area' => 'frontend'));
        }
        $name = $product->getName();
        $html = "<img width='170px' src = '$thumbnail' alt='$name'/>";
        return $html;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getAvailabilityText( $product )
    {
        if ( $this->getIsInStock($product) ) {
            return Mage::helper('catalog')->__("In Stock");
        } else {
            return Mage::helper('catalog')->__("Out of Stock");
        }
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getStatusText( $product )
    {
        if ( $product->getStatus() ) {
            return Mage::helper('catalog')->__("Enabled");
        } else {
            return Mage::helper('catalog')->__("Disabled");
        }
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getDetailUrl( $product )
    {
        return Mage::helper('adminhtml')->getUrl('*/catalog_product/edit', array('id' => $product->getId()));
    }

    /**
     * @param $product
     * @return string
     */
    public function getDetailUrlHtml( $product )
    {
        $url = $this->getDetailUrl($product);
        return "<a target='_blank' href='$url' class='button'>More details</a>";
    }
}