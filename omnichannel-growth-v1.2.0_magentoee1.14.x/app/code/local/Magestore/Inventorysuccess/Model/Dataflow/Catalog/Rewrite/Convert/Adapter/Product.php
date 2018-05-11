<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Magestore_Inventorysuccess_Model_Dataflow_Catalog_Rewrite_Convert_Adapter_Product
    extends Mage_Catalog_Model_Convert_Adapter_Product
{

    protected $warehouseIds = array();
    protected $warehouses = array();
    protected $warehouseAdjustStock = array();
    protected $warehouseAdjustIds = array();
    protected $warehouseProductLocations = array();
    protected $warehouseLocationIds = array();
    protected $nonWarehouse;

    /**
     * add default value
     */
    public function _initData()
    {
        /** @var set data to session to update adjust stock and warehouse stock $saveData */
        $saveData = Mage::getModel('adminhtml/session')->getData('save_data_flow', array());
        if (isset($saveData['warehouses'])) {
            $this->warehouses = $saveData['warehouses'];
        }

        if (isset($saveData['warehouse_ids'])) {
            $this->warehouseIds = $saveData['warehouse_ids'];
        }

        if (isset($saveData['warehouse_adjust_stock'])) {
            $this->warehouseAdjustStock = $saveData['warehouse_adjust_stock'];
        }

        if (isset($saveData['warehouse_adjust_ids'])) {
            $this->warehouseAdjustIds = $saveData['warehouse_adjust_ids'];
        }

        if (isset($saveData['warehouse_product_locations'])) {
            $this->warehouseProductLocations = $saveData['warehouse_product_locations'];
        }

        if (isset($saveData['warehouse_location_ids'])) {
            $this->warehouseLocationIds = $saveData['warehouse_location_ids'];
        }

    }
    
    /**
     * Save product (import)
     *
     * @param  array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        if(version_compare(Mage::getVersion(), '1.9', '>=')) {
            return $this->saveRow19($importData);
        }
        
        if(version_compare(Mage::getVersion(), '1.8', '>=')) {
            return $this->saveRow18($importData);
        }      
        
        return $this->saveRow18($importData);
    }
    
    /**
     * Save product (import)
     *
     * @param  array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow18(array $importData)
    {
        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $this->setProductTypeInstance($product);

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        
        /* start custom code from InventorySuccess */

        /** @var import with warehouse */
        if (!count($this->warehouseIds)) {
            /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
            $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
            /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
            foreach ($warehouseCollection as $warehouse) {
                $this->warehouseIds[] = $warehouse->getId();
                $this->warehouses[$warehouse->getId()] = $warehouse->getData();
            }
        }
        /** @var end import with warehouse */

        /** check update qty with warehouse */
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            /** get qty product when have warehouse */
            $this->nonWarehouse = true;
            if (!empty($this->warehouseIds)) {
                $qty = $this->getQtyProductToImport($product->getId(), $importData);
                $warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
                /** if product is not in any warehouse, the product will be added to primary warehouse with qty of catalog product*/
                if (!$this->nonWarehouse) {
                    $stockData['qty'] = $qty;
                }
                if ($this->nonWarehouse) {
                    $primaryWarehouse = $warehouseService->getPrimaryWarehouse();
                    $this->warehouseAdjustStock[$primaryWarehouse->getId()]['products'][$product->getId()] = array(
                        'adjust_qty' => $stockData['qty'],
                        'product_name' => $importData['name'],
                        'product_sku' => $importData['sku'],
                        'old_qty' => 0
                    );
                    if (!in_array($primaryWarehouse->getId(), $this->warehouseAdjustIds)) {
                        $this->warehouseAdjustIds[] = $primaryWarehouse->getId();
                    }
                }
            }
        }

        $product->setStockData($stockData);

        /** @var set data to session to update adjust stock and warehouse stock $saveData */
        $this->updateDataWarehouseProduct();
        
        /* endof custom code from InventorySuccess */

        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }

        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }  
    
    /**
     * Save product (import)
     *
     * @param  array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow19(array $importData)
    {
        $this->_initData();

        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skip importing row, the required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skip importing row, the store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skip importing row, the required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip importing row, the value "%s" is invalid for field "%s."', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip importing row, the value "%s" is invalid for field "%s."', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skip importing row, the required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        // process row with media data only
        if (isset($importData['_media_image']) && strlen($importData['_media_image'])) {
            $this->saveImageDataRow($product, $importData);
            return true;
        }

        $this->setProductTypeInstance($product);

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }
        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        
        /* start custom code from InventorySuccess */

        /** @var import with warehouse */
        if (!count($this->warehouseIds)) {
            /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
            $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
            /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
            foreach ($warehouseCollection as $warehouse) {
                $this->warehouseIds[] = $warehouse->getId();
                $this->warehouses[$warehouse->getId()] = $warehouse->getData();
            }
        }
        /** @var end import with warehouse */

        /** check update qty with warehouse */
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            /** get qty product when have warehouse */
            $this->nonWarehouse = true;
            if (!empty($this->warehouseIds)) {
                $qty = $this->getQtyProductToImport($product->getId(), $importData);
                $warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
                /** if product is not in any warehouse, the product will be added to primary warehouse with qty of catalog product*/
                if (!$this->nonWarehouse) {
                    $stockData['qty'] = $qty;
                }
                if ($this->nonWarehouse) {
                    $primaryWarehouse = $warehouseService->getPrimaryWarehouse();
                    $this->warehouseAdjustStock[$primaryWarehouse->getId()]['products'][$product->getId()] = array(
                        'adjust_qty' => $stockData['qty'],
                        'product_name' => $importData['name'],
                        'product_sku' => $importData['sku'],
                        'old_qty' => 0
                    );
                    if (!in_array($primaryWarehouse->getId(), $this->warehouseAdjustIds)) {
                        $this->warehouseAdjustIds[] = $primaryWarehouse->getId();
                    }
                }
            }
        }

        $product->setStockData($stockData);

        /** @var set data to session to update adjust stock and warehouse stock $saveData */
        $this->updateDataWarehouseProduct();
        
        /* endof custom code from InventorySuccess */
        
        $arrayToMassAdd = array();

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$this->_galleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }

        $addedFilesCorrespondence = $this->_galleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $this->_galleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }

    /**
     * get qty product by warehouse to import product to catalog
     * @param $stockItem
     * @return int
     */
    public function getQtyProductToImport($productId, $rowData)
    {
        $qty = 0;
        $warehouseStockService = Magestore_Coresuccess_Model_Service::warehouseStockService();
        foreach ($this->warehouseIds as $warehouseId) {
            /** @var Magestore_Inventorysuccess_Model_Warehouse_Product $warehouseStock */
            $warehouseStock = $warehouseStockService->getStocks($warehouseId, array($productId))
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
            $oldWarehouseProductQty = 0;
            /** check qty product in warehouse */
            if (isset($rowData['qty_'.$warehouseId])) {
                $this->nonWarehouse = false;
                $qty += $rowData['qty_'.$warehouseId];
                if ($warehouseStock->getId()) {
                    $qty -= $warehouseStock->getQtyToShip();
                    $oldWarehouseProductQty = $warehouseStock->getTotalQty();
                }
                $newWarehouseProductQty = $rowData['qty_'.$warehouseId];
                if ($oldWarehouseProductQty != $newWarehouseProductQty) {
                    $this->warehouseAdjustStock[$warehouseId]['products'][$productId] = array(
                        'adjust_qty' => $newWarehouseProductQty,
                        'product_name' => $rowData['name'],
                        'product_sku' => $rowData['sku'],
                        'old_qty' => 0
                    );
                    if (!in_array($warehouseId, $this->warehouseAdjustIds)) {
                        $this->warehouseAdjustIds[] = $warehouseId;
                    }
                }
            } else {
                if ($warehouseStock->getId()) {
                    $this->nonWarehouse = false;
                    $qty += $warehouseStock->getTotalQty() - $warehouseStock->getQtyToShip();
                }
            }
            /** check shelf location product in warehouse */
            if (isset($rowData['location_'.$warehouseId])) {
                $this->warehouseProductLocations[$warehouseId][$productId] = $rowData['location_'.$warehouseId];
                if (!in_array($warehouseId, $this->warehouseLocationIds)) {
                    $this->warehouseLocationIds[] = $warehouseId;
                }
            }
        }
        return $qty;
    }

    /**
     * update data warehouse product
     */
    public function updateDataWarehouseProduct()
    {
        $saveData = array();

        $saveData['warehouse_ids'] = $this->warehouses;

        $saveData['warehouse_ids'] = $this->warehouseIds;

        $saveData['warehouse_adjust_stock'] = $this->warehouseAdjustStock;

        $saveData['warehouse_adjust_ids'] = $this->warehouseAdjustIds;

        $saveData['warehouse_product_locations'] = $this->warehouseProductLocations;

        $saveData['warehouse_location_ids'] = $this->warehouseLocationIds;
        Mage::getModel('adminhtml/session')->setData('save_data_flow', $saveData);
    }

}
