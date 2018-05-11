<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Service\Synchronization;

/**
 * class \Magestore\Webpos\Model\Service\Synchronization\Product
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Product extends \Magestore\Webpos\Model\Service\Synchronization
    implements \Magestore\Webpos\Api\Synchronization\ProductInterface
{
    const SYNCHRONIZATION_TYPE = 'product';

    const SYNCHRONIZATION_CONFIG_LINK = 'ms_webpos/sync_time/product';

    const SYNCHRONIZATION_CONFIG_UPDATE = 'ms_webpos/process_update/product';

    const SYNCHRONIZATION_CONFIG_USE = 'webpos/offline/product_sync_index';

    const SYNCHRONIZATION_TABLE = 'ms_webpos_catalog_product_flat';

    /**
     * @var array
     */
    protected $arrayColumn = [
        'custom_options',
        'config_options',
        'extension_attributes',
        'images',
        'stocks',
        'tier_prices',
        'barcode_options',
        'grouped_options',
        'bundle_options',
        'children_products'
    ];

    /**
     * @var array
     */
    protected $listProductAttributes = [
        'entity_id',
        'type_id',
        'category_ids',
        'description',
        'gift_card_type',
        'gift_dropdown',
        'gift_from',
        'gift_code_sets',
        'gift_message_available',
        'gift_price',
        'gift_price_type',
        'gift_template_ids',
        'gift_to',
        'gift_type',
        'gift_value',
        'has_options',
        'image',
        'media_gallery',
        'name',
        'price',
        'short_description',
        'sku',
        'special_from_date',
        'special_to_date',
        'special_price',
        'status',
        'storecredit_dropdown',
        'storecredit_from',
        'storecredit_rate',
        'storecredit_to',
        'storecredit_type',
        'storecredit_value',
        'tax_class_id',
        'tier_price',
        'updated_at',
        'webpos_visible',
        'weight',
    ];

    protected function verifyBeforeInsert(&$product)
    {
        // remove data qty
//        unset($product['qty']);
    }

    /**
     * get all attributes product have scope is store view or website
     */
    public function getListAttributesProduct()
    {
        $sql = 'SELECT main_table.attribute_code'
            . ' FROM ' . $this->resource->getTableName('eav_attribute') . ' AS main_table'
            . ' JOIN ' . $this->resource->getTableName('catalog_eav_attribute') . ' AS additional_table'
            . ' ON additional_table.attribute_id = main_table.attribute_id'
            . ' WHERE main_table.entity_type_id = 4 AND (additional_table.is_global = 0 OR additional_table.is_global = 2)'
            . ' AND main_table.attribute_code NOT IN ("image")';
        $attributes = [];
        foreach ($this->connection->fetchAll($sql) as $item) {
            $attributes[] = $item['attribute_code'];
        }
        return array_intersect($attributes, $this->listProductAttributes);
    }

    public function createIndexTable($storeId = null)
    {
        if ($storeId !== null) {
            return parent::createIndexTable($storeId);
        }
        foreach ($this->getListStoreIds() as $storeId) {
            parent::createIndexTable($storeId);
        }
    }

    public function addIndexTableData($storeId = null)
    {
        if ($storeId !== null) {
            return parent::addIndexTableData($storeId);
        }
        foreach ($this->getListStoreIds() as $storeId) {
            parent::addIndexTableData($storeId);
        }
    }

    /**
     *
     *
     * @param $updatedTime
     * @param null $storeId
     * @return array|object|void
     */
    public function prepareSynchronizationData($updatedTime, $storeId = null, $curentPage = 1)
    {
        if ($storeId != 0 && !$this->getTotalItemTmpTable($storeId)) {
            return $this->prepareSynchronizationDuplicateData($storeId);
        }
        return $this->prepareSynchronizationUpdateData($updatedTime, $storeId, $curentPage);
    }

    /**
     * @param $storeId
     * @return array
     */
    protected function prepareSynchronizationDuplicateData($storeId)
    {
        $storeTable = $this->resource->getTableName($this->getTableName($storeId));
        $sourceTable = $this->resource->getTableName($this->getTableName(0));
        // copy data from default store view flat table
        $sql = "INSERT INTO $storeTable SELECT * FROM $sourceTable";

        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = $this->objectManager->get('\Magento\Framework\App\ResourceConnection');
        $this->connection->query($sql);

        // change data for attribute use multi store view
        $listMultiStoreAttribute = $this->getListAttributesProduct();
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['entity_id']);
        $productCollection->setFlag('has_stock_status_filter', true);

        $productCollection->setStoreId($storeId)->addStoreFilter($storeId);
        $this->extensionAttributesJoinProcessor->process($productCollection);
        $productCollection->addAttributeToSelect(array_values($listMultiStoreAttribute));
        $productCollection->addFieldToSelect('entity_id');
        $productCollection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $productCollection->addAttributeToFilter(array(
            array('attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'),
            array('attribute' => 'webpos_visible', 'eq' => \Magento\Catalog\Model\Product\Attribute\Source\Boolean::VALUE_YES, 'left'),
        ), '', 'left');
        $productCollection->addAttributeToFilter('type_id', ['in' => $this->getProductTypeIds()]);
        $collection = clone $productCollection;

        $dataProduct = [];
        foreach ($collection->getItems() as $item) {
            $tmp = $item->getData();
            $tmp['id'] = $tmp['entity_id'];
            unset($tmp['entity_id']);

            $dataProduct[] = $tmp;
        }
        return $dataProduct;
    }

    /**
     * @param $updatedTime
     * @param null $storeId
     * @return array
     */
    protected function prepareSynchronizationUpdateData($updatedTime, $storeId = null, $curentPage = 1)
    {
        $collection = $this->getUpdatedCollection($updatedTime, $storeId);
        $collection->setPageSize(static::PAGESIZE);
        $collection->setCurPage($curentPage);
        $data = $this->convertValue($collection->getItems(), 'Magestore\Webpos\Api\Data\Catalog\ProductInterface[]');
        return $data;
    }

    public function getTotalUpdatedData($updatedTime, $storeId = null)
    {
        if ($storeId != 0 && !$this->getTotalItemTmpTable($storeId)) {
            return 1;
        }
        $collection = $this->getUpdatedCollection($updatedTime, $storeId);
        return $collection->getSize();
    }

    /**
     * @param $updatedTime
     * @param null $storeId
     * @return \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection
     */
    public function getUpdatedCollection($updatedTime, $storeId = null)
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setFlag('has_stock_status_filter', true);

        $collection->setStoreId($storeId)->addStoreFilter($storeId);
        $this->extensionAttributesJoinProcessor->process($collection);
        $collection->addAttributeToSelect($this->listProductAttributes);
        $collection->addAttributeToSort('name', 'ASC');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter(array(
            array('attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'),
            array('attribute' => 'webpos_visible', 'eq' => \Magento\Catalog\Model\Product\Attribute\Source\Boolean::VALUE_YES, 'left'),
        ), '', 'left');
        $collection->addAttributeToFilter('type_id', ['in' => $this->getProductTypeIds()]);
        if ($updatedTime) {
            $collection->addAttributeToFilter('updated_at', ['gteq' => $updatedTime]);
        }
        return $collection;
    }

    /**
     * get product type ids to support
     * @return array
     */
    public function getProductTypeIds()
    {
        $types = [
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
        ];
        $moduleManager = $this->objectManager->create('\Magento\Framework\Module\Manager');
        if ($moduleManager->isEnabled('Magestore_Customercredit')) {
            $types[] = 'customercredit';
        }
        if ($moduleManager->isEnabled('Magestore_Giftvoucher')) {
            $types[] = \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE;
        }
        return $types;
    }
}