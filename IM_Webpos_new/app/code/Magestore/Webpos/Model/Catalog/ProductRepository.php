<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog;

use Magento\Framework\Api\SortOrder;
use Magento\Catalog\Api\Data\ProductExtension;
use \Magento\CatalogInventory\Model\Stock as Stock;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductRepository extends \Magento\Catalog\Model\ProductRepository
    implements \Magestore\Webpos\Api\Catalog\ProductRepositoryInterface
{
    /** @var */
    protected $_productCollection;

    protected $listAttributes = [
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

    protected $listCondition = [
        'eq' => '=',
        'neq' => '!=',
        'like' => 'like',
        'gt' => '>',
        'gteq' => '>=',
        'lt' => '<',
        'lteq' => '<=',
        'in' => 'in'
    ];

    protected $collectionSize = 0;

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Synchronization\ProductInterface $productSynchronization */
        $productSynchronization = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magestore\Webpos\Api\Synchronization\ProductInterface'
        );
        $storeId = $this->storeManager->getStore()->getId();
        if ($productSynchronization->isTableExists($productSynchronization->getTableName($storeId))
            && $productSynchronization->isUseIndexTable()
        ) {
            $productData = $this->prepareFromFlatTable($searchCriteria);
            $searchResult = $this->searchResultsFactory->create();
            $searchCriteria->setFilterGroups([]);
            $searchCriteria->setSortOrders([]);
            $searchCriteria->setPageSize(0);
            $searchCriteria->setCurrentPage(0);
            $searchResult->setSearchCriteria($searchCriteria);
            $searchResult->setItems($productData);
            $searchResult->setTotalCount($this->collectionSize);
            return $searchResult;
        } else {
            $this->prepareCollection($searchCriteria);
            $this->_productCollection->setStoreId($storeId)->addStoreFilter($storeId);
            $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
            $this->_productCollection->setPageSize($searchCriteria->getPageSize());
            $searchResult = $this->searchResultsFactory->create();
            $searchResult->setSearchCriteria($searchCriteria);
            $searchResult->setItems($this->_productCollection->getItems());
            $searchResult->setTotalCount($this->_productCollection->getSize());
            return $searchResult;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsWithoutOptions(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $moduleManager = $objectManager->create('\Magento\Framework\Module\Manager');
        $this->prepareCollection($searchCriteria);
        $storeId = $this->storeManager->getStore()->getId();
        $this->_productCollection->setStoreId($storeId)->addStoreFilter($storeId);
        $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
        $this->_productCollection->setPageSize($searchCriteria->getPageSize());
        $this->_productCollection->addAttributeToSelect($this->listAttributes);
        if (!$moduleManager->isEnabled('Magestore_InventorySuccess')) {
            $this->_productCollection->getSelect()->joinLeft(
                array('stock_item' => $this->_productCollection->getTable('cataloginventory_stock_item')),
                'e.entity_id = stock_item.product_id AND stock_item.stock_id = "' . Stock::DEFAULT_STOCK_ID . '"',
                array('qty', 'manage_stock', 'backorders', 'min_sale_qty', 'max_sale_qty', 'is_in_stock',
                    'enable_qty_increments', 'qty_increments', 'is_qty_decimal')
            );
        }
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($this->_productCollection->getItems());
        $searchResult->setTotalCount($this->_productCollection->getSize());
        return $searchResult;
    }

    public function prepareFromFlatTable(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Synchronization\ProductInterface $productSynchronization */
        $productSynchronization = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magestore\Webpos\Api\Synchronization\ProductInterface'
        );
        $storeId = $this->storeManager->getStore()->getId();
        if ($sql = $productSynchronization->getAllDataFromTable($storeId)) {
            /** @var \Magento\Framework\Registry $registry */
            $registry = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Framework\Registry'
            );
            $tableName = $this->productFactory->create()->getCollection()->getTable($productSynchronization->getTableName($storeId));

            $registry->register('webpos_get_product_list', true);

            $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Framework\Event\ManagerInterface'
            );
            $permissionHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Helper\Permission'
            );
            $obj = new \Magento\Framework\DataObject();
            $obj->setSql($sql);
            $obj->setWarehouse('');
            $eventManage->dispatch(
                'webpos_catalog_product_getlist_flat_table',
                ['object' => $obj, 'location' => $permissionHelper->getCurrentLocation()]
            );
            $sql = $obj->getSql();

            $this->getFlatStockData($sql, $obj->getWarehouse());

            $where = [];
            $subViewSql = 'SELECT * FROM '. $tableName;
            $viewWhere = [];

            // check stock
            $stockWhere = $this->checkFlatStock($sql);
            if ($stockWhere) {
                $where[] = $stockWhere;
            }

            // add filter
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $viewWhere[] = $this->addFilterGroupToSql($group);
            }
            $isFirstWhere = true;
            foreach ($where as $subWhere) {
                if ($isFirstWhere) {
                    $sql .= ' WHERE ';
                } else {
                    $sql .= ' AND ';
                }
                $sql .= $subWhere;
                $isFirstWhere = false;
            }

            $subViewSql = $this->getSubViewSql($subViewSql, $viewWhere, $searchCriteria->getSortOrders());

            // add sort order
//            if ($sortOrder = $searchCriteria->getSortOrders()) {
//                if (is_array($sortOrder)) {
//                    $sortOrder = $sortOrder[0];
//                }
//                if ($sortOrder) {
//                    $sql .= ' ORDER BY ' . $sortOrder->getField() . ' ' . $sortOrder->getDirection();
//                }
//            }

            /** @var \Magento\Framework\App\ResourceConnection $resource */
            $resource = $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Framework\App\ResourceConnection'
            );

            /***************/
            // get total data
            // change sql with view select
            $countSql = str_replace($tableName, '('.$subViewSql.')', $sql);
            $selectSql = explode('FROM', $countSql);
            $countSql = str_replace($selectSql[0], 'SELECT count(*) ', $countSql);
            // get page size before set page size and current page
            $this->collectionSize = $resource->getConnection()->fetchCol($countSql)[0];
            /***************/

            /***************/
            // page size and cur page
            $pageSize = $searchCriteria->getPageSize();
            $curPage = $searchCriteria->getCurrentPage();
            if($pageSize != null && $curPage != null) {
                $subViewSql .= " LIMIT " . (int)$pageSize .
                    " OFFSET " . (int)$pageSize * ($curPage - 1);
            }
            // change sql with view select
            $sql = str_replace($tableName, '('.$subViewSql.')', $sql);
            /***************/

            $result = $resource->getConnection()->fetchAll($sql);
            $result = $productSynchronization->prepareGetData($result);
            return $result;
        } else {
            return [];
        }
    }

    protected function getSubViewSql($sql, $where, $sortOrder) {
        // add filter
        $isFirstWhere = true;
        foreach ($where as $subWhere) {
            if ($isFirstWhere) {
                $sql .= ' WHERE ';
            } else {
                $sql .= ' AND ';
            }
            $sql .= $subWhere;
            $isFirstWhere = false;
        }

        // add order by
        if($sortOrder) {
            if (is_array($sortOrder)) {
                $sortOrder = $sortOrder[0];
            }
            if ($sortOrder) {
                $sql .= ' ORDER BY ' . $sortOrder->getField() . ' ' . $sortOrder->getDirection();
            }
        }

        return $sql;
    }

    public function getFlatStockData(&$sql, $warehouseId) {
        $warehouseId = $warehouseId ?: 0;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $collection = $objectManager->get(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );

        $sql .= ' INNER JOIN `'.$collection->getTable('cataloginventory_stock_item').'` AS `warehouse_product`'
            . ' ON e.id = warehouse_product.product_id'
            . ' AND warehouse_product.website_id = ' . $warehouseId;

        $sql = str_replace(
            'SELECT e.*',
            'SELECT e.*,warehouse_product.qty AS qty,warehouse_product.qty AS qty_online,warehouse_product.is_in_stock AS is_in_stock',
            $sql
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prepareCollection($searchCriteria)
    {
        if (empty($this->_productCollection)) {

            /** @var \Magento\Framework\Registry $registry */
            $registry = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Framework\Registry'
            );

            $registry->register('webpos_get_product_list', true);

            $this->_productCollection = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection'
            );

            /** Integrate webpos **/
            $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Framework\Event\ManagerInterface'
            );
            $permissionHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Helper\Permission'
            );
            $eventManage->dispatch(
                'webpos_catalog_product_getlist',
                ['collection' => $this->_productCollection, 'location' => $permissionHelper->getCurrentLocation()]
            );
            /** End integrate webpos **/

            $this->extensionAttributesJoinProcessor->process($this->_productCollection);
            $this->_productCollection->addAttributeToSelect($this->listAttributes);
            $this->_productCollection->addAttributeToSort('name', 'ASC');
            $this->_productCollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $this->_productCollection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $this->_productCollection->addAttributeToFilter(array(
                array('attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'),
                array('attribute' => 'webpos_visible', 'eq' => \Magento\Catalog\Model\Product\Attribute\Source\Boolean::VALUE_YES, 'left'),
            ), '', 'left');
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $this->_productCollection);
            }
            /** @var SortOrder $sortOrder */
            foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $this->_productCollection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
            $this->_productCollection->addAttributeToFilter('type_id', ['in' => $this->getProductTypeIds()]);
            $this->checkStocks();
        }
    }

    /**
     * get product attributes to select
     * @return array
     */
    public function getSelectProductAtrributes()
    {
        return [
            self::TYPE_ID,
            self::NAME,
            self::PRICE,
            self::SPECIAL_PRICE,
            self::SPECIAL_FROM_DATE,
            self::SPECIAL_TO_DATE,
            self::SKU,
            self::SHORT_DESCRIPTION,
            self::DESCRIPTION,
            self::IMAGE,
            self::FINAL_PRICE
        ];
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $moduleManager = $objectManager->create('\Magento\Framework\Module\Manager');
        if ($moduleManager->isEnabled('Magestore_Customercredit')) {
            $types[] = 'customercredit';
        }
        if ($moduleManager->isEnabled('Magestore_Giftvoucher')) {
            $types[] = \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE;
        }
        if ($moduleManager->isEnabled('Magento_Giftcard')) {
            $types[] = \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD;
        }
        return $types;
    }

    /**
     * Get info about product by product SKU
     *
     * @param string $id
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id, $editMode = false, $storeId = null, $forceReload = false)
    {

        $cacheKey = $this->getCacheKey([$editMode, $storeId]);
        if (!isset($this->instancesById[$id][$cacheKey]) || $forceReload) {
            $product = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Model\Catalog\Product'
            );
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            if ($storeId !== null) {
                $product->setData('store_id', $storeId);
            }
            $product->load($id);
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested product doesn\'t exist'));
            }
            $this->instancesById[$id][$cacheKey] = $product;
            $this->instances[$product->getSku()][$cacheKey] = $product;
        }
        return $this->instancesById[$id][$cacheKey];
    }

    /**
     * Get product options
     *
     * @param string $id
     * @param bool $editMode
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptions($id, $editMode = false, $storeId = null)
    {
        $product = $this->getProductById($id, $editMode, $storeId);
        $data = array();
        $data['custom_options'] = $this->getCustomOptions($product);
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $data['bundle_options'] = $product->getBundleOptions();
        }
        if ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $data['grouped_options'] = $product->getGroupedOptions();
        }
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $data['configurable_options'] = $product->getConfigOptions();
            $data['json_config'] = $product->getJsonConfig();
            $data['price_config'] = $product->getPriceConfig();
        }
        return \Zend_Json::encode($data);
    }


    /**
     * get custom options
     * @params \Magestore\Webpos\Api\Data\Catalog\ProductInterface $product
     * @return array
     */
    public function getCustomOptions($product)
    {
        $customOptions = $product->getOptions();
        $options = array();
        foreach ($customOptions as $child) {
            $values = array();
            if ($child->getValues()) {
                foreach ($child->getValues() as $value) {
                    $values[] = $value->getData();
                }
                $child['values'] = $values;
            }
            $options[] = $child->getData();
        }
        return $options;
    }

    /**
     *
     */
    public function checkFlatStock(&$sql)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Manager $moduleManager */
        $moduleManager = $objectManager->create(
            '\Magento\Framework\Module\Manager'
        );
        /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig */
        $scopeConfig = $objectManager->get(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        );
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $objectManager->get(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
        /** @var \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration */
        $stockConfiguration = $objectManager->get(
            \Magento\CatalogInventory\Api\StockConfigurationInterface::class
        );
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $objectManager->get(
            '\Magento\Framework\App\Request\Http'
        );
        $showOutOfStock = $request->getParam('show_out_stock');
        $isShowOutOfStock = $scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$showOutOfStock) {
            $isFilterInStock = !$isShowOutOfStock;
            $websiteId = $stockConfiguration->getDefaultScopeId();

            $warehouseId = '';
            if ($moduleManager->isEnabled('Magestore_InventorySuccess')) {
                /** @var \Magestore\InventorySuccess\Api\Warehouse\WarehouseManagementInterface $warehouseManagement */
                $warehouseManagement = $objectManager->get(
                    'Magestore\InventorySuccess\Api\Warehouse\WarehouseManagementInterface'
                );
                $storeId = $this->storeManager->getStore()->getId();
                $warehouseId = $warehouseManagement->getWarehouseByStoreId($storeId)->getWarehouseId();
            }
            if (!$warehouseId) {
                $warehouseId = Stock::DEFAULT_STOCK_ID;
            }

            $method = $isFilterInStock ? 'JOIN ' : 'LEFT JOIN';

            $sql = str_replace(
                'SELECT e.*',
                'SELECT e.*,stock_status_index.stock_status AS is_salable',
                $sql
            );

            $sql .= ' ' . $method . ' ' . $collection->getTable('cataloginventory_stock_status')
                . ' AS stock_status_index ON '
                . 'e.id = stock_status_index.product_id AND '
                . 'stock_status_index.website_id = ' . $websiteId
                . ' AND stock_status_index.stock_id = ' . $warehouseId;

            $where = '';
            if ($isFilterInStock) {
                $where = '(stock_status_index.stock_status = ' . Stock\Status::STATUS_IN_STOCK . ')';
            }
            return $where;
        }
    }

    public function checkStocks()
    {
        $request = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\Request\Http');
        $showOutOfStock = $request->getParam('show_out_stock');
        if (!$showOutOfStock) {
            $stockHelper = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\CatalogInventory\Helper\Stock');
            $this->_productCollection->setFlag('require_stock_items', true);
            $stockHelper->addIsInStockFilterToCollection($this->_productCollection);
        }
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    )
    {
        $fields = [];
        $categoryFilter = [];
        $searchString = '';
        foreach ($filterGroup->getFilters() as $filter) {
            /** search with '#' character  */
            $value = $filter->getValue();
            if (strpos($filter->getValue(), '/@$%@$%/') == true) {
                $value = str_replace('/@$%@$%/','#',$value);
            }

            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

            if ($filter->getField() == 'category_id') {
                $categoryFilter['in'][] = str_replace("%", "", $value);
                continue;
            }
            $fields[] = ['attribute' => $filter->getField(), $conditionType => $value];
            $searchString = $value ? $value : $searchString;
        }

        if ($categoryFilter && empty($fields)) {
            $collection->addCategoriesFilter($categoryFilter);
        }

        /** Integrate Inventory Barcode **/
        $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Framework\Event\ManagerInterface'
        );
        $array = [];
        $result = new \Magento\Framework\DataObject();
        $result->setData($array);
        $eventManage->dispatch(
            'webpos_catalog_product_search_online',
            ['search_string' => $searchString, 'result' => $result]
        );
        foreach ($result->getData() as $key => $value) {
            if ($result->getData()) {
                $fields[] = ['attribute' => 'sku', 'like' => $value];
            }
        }
        /** End integrate Inventory Barcode **/

        if ($fields) {
            $collection->addAttributeToFilter($fields, '', 'left');
        }
    }

    protected function addFilterGroupToSql(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup
    )
    {
        $searchString = '';
        $where = '(';
        $first = true;
        foreach ($filterGroup->getFilters() as $filter) {
            if (!in_array($filter->getField(), $this->listAttributes)) {
                continue;
            }
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $searchString = $filter->getValue() ? $filter->getValue() : $searchString;
            $field = $filter->getField();
            if ($field == 'category_id') {
                $conditionType = 'like';
            }
            $conditionType = $this->listCondition[$conditionType];

            if (!$first) {
                $where .= ' OR ';
            }
            if(strtolower($conditionType) != 'in') {
                $where .= $field . " " . $conditionType . " '" . $searchString . "'";
            } else {
                $where .= $field . " " . $conditionType . " (" . $searchString . ")";
            }
            $first = false;
        }

        /** Integrate Inventory Barcode **/
        $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Framework\Event\ManagerInterface'
        );
        $result = new \Magento\Framework\DataObject();
        $array = [];
        $result->setData($array);
        $eventManage->dispatch(
            'webpos_catalog_product_search_online',
            ['search_string' => $searchString, 'result' => $result]
        );

        foreach ($result->getData() as $sku) {
            $where .= ' OR ';
            $where .= "sku = '" . $sku . "'";
        }

        $where .= ')';

        return $where;
    }
}