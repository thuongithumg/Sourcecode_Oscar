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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_ImportExport_ImportProductService extends Mage_ImportExport_Model_Export_Entity_Product
{

    /**
     * Get attributes codes which are appropriate for export.
     *
     * @return array
     */
    public function getExportAttrCodes()
    {
        if (null === self::$attrCodes) {
            if (!empty($this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_SKIP])
                && is_array($this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_SKIP])) {
                $skipAttr = array_flip($this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_SKIP]);
            } else {
                $skipAttr = array();
            }
            $attrCodes = array();

            foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
                if (!isset($skipAttr[$attribute->getAttributeId()])
                    || in_array($attribute->getAttributeCode(), $this->_permanentAttributes)) {
                    $attrCodes[] = $attribute->getAttributeCode();
                }
            }
            self::$attrCodes = $attrCodes;
        }
        return self::$attrCodes;
    }

    /**
     * prepare data to download
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Notification $notification
     * @return array
     * @throws Exception
     */
    public function getPrepareDataToDownload()
    {
        $heading = array();
        $attributes = array('sku', 'qty');
        foreach ($attributes as $attribute) {
            $heading[] = $attribute;
            $attributeCodes[] = $attribute;
        }

        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
        $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouseIds = array();
        /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
        foreach ($warehouseCollection as $warehouse) {
            $heading[] = 'qty_'.$warehouse->getId();
            $heading[] = 'location_'.$warehouse->getId();
            $warehouseIds[] = $warehouse->getId();
        }
        $path = Mage::getBaseDir('var') . DS . 'importexport' . DS . 'download' . DS;
        $outputFile = "import_product_sample". date('Ymd_His').".csv";
        $filename = $path.$outputFile;

        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($filename, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($heading);

        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addAttributeToSelect(array('sku', 'type_id'));
        $productCollection->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        $productCollection->setPageSize(5);
        $productCollection->setCurPage(1);
        $productCollection->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productCollection as $product) {
            $productData = $this->getProductData($product, $attributeCodes);
            $productWarehouseData = $this->getProductWarehouseData($product, $warehouseIds);
            $row = array_merge($productData, $productWarehouseData);
            $io->streamWriteCsv($row);
        }
        $io->streamUnlock();
        $io->streamClose();
        return array(
            'type'  => 'filename',
            'value' => $filename,
            'rm'    => true // can delete file after use
        );
    }

    /**
     * get product data by attribute code
     * @param Mage_Catalog_Model_Product $product
     * @param array $attributeCodes
     * @return array
     */
    public function getProductData(Mage_Catalog_Model_Product $product, $attributeCodes = array())
    {
        $productData = array();
        foreach ($attributeCodes as $attributeCode) {
            $productData[] = $product->getData($attributeCode);
        }
        return $productData;
    }

    /**
     * get qty and shelf location of Product in Warehouse(s)
     * @param Mage_Catalog_Model_Product $product
     * @param array $warehouseIds
     * @return array
     */
    public function getProductWarehouseData(Mage_Catalog_Model_Product $product, $warehouseIds = array())
    {
        $productWarehouseData = array();
        $warehouseStockService = Magestore_Coresuccess_Model_Service::warehouseStockService();
        foreach ($warehouseIds as $warehouseId) {
            /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection $warehouseProduct */
            $warehouseProductCollection = $warehouseStockService->getStocks($warehouseId, array($product->getId()));
            /** @var Magestore_Inventorysuccess_Model_Warehouse_Product $warehouseProduct */
            $warehouseProduct = $warehouseProductCollection->setCurPage(1)
                ->setCurPage(1)
                ->getFirstItem();
            $warehouseProductQty = '';
            $warehouseProductShelfLocation = '';
            if ($warehouseProduct->getId()) {
                $warehouseProductQty = $warehouseProduct->getTotalQty();
                $warehouseProductShelfLocation = $warehouseProduct->getShelfLocation();
            }
            $productWarehouseData[] = $warehouseProductQty;
            $productWarehouseData[] = $warehouseProductShelfLocation;
        }
        return $productWarehouseData;
    }
}