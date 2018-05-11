<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Inventory;

use \Magento\Framework\ObjectManagerInterface as ObjectManagerInterface;
use \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item as StockItemResource;
use \Magento\CatalogInventory\Model\Stock\Item as StockItemModel;
use \Magento\CatalogInventory\Api\StockItemRepositoryInterface as StockItemRepositoryInterface;
use \Magento\Framework\Api\SortOrder;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;

/**
 * Class StockItemRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockItemRepository implements \Magestore\Webpos\Api\Inventory\StockItemRepositoryInterface
{

    /**
     *
     * @var \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item
     */
    protected $resource;

    /**
     *
     * @var \Magento\CatalogInventory\Model\Stock\Item
     */
    protected $stockItemModel;

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     *
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     *
     * @var StockRegistryProviderInterface
     */
    protected $stockRegistryProvider;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $permissionManager;

    /**
     * @var \Magestore\Webpos\Api\Synchronization\StockInterface
     */
    protected $stockSynchronization;

    /**
     * @var \Magestore\Webpos\Api\Synchronization\ProductInterface
     */
    protected $productSynchronization;

    protected $collectionSize;

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

    /**
     * StockItemRepository constructor.
     * @param StockItemResource $resource
     * @param StockItemModel $stockItemModel
     * @param StockItemRepositoryInterface $stockItemRepository
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StockItemResource $resource,
        StockItemModel $stockItemModel,
        StockItemRepositoryInterface $stockItemRepository,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magestore\Webpos\Helper\Permission $permissionManager,
        \Magestore\Webpos\Api\Synchronization\StockInterface $stockSynchronization,
        \Magestore\Webpos\Api\Synchronization\ProductInterface $productSynchronization
    )
    {
        $this->resource = $resource;
        $this->stockItemModel = $stockItemModel;
        $this->stockItemRepository = $stockItemRepository;
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->eventManager = $eventManager;
        $this->permissionManager = $permissionManager;
        $this->stockSynchronization = $stockSynchronization;
        $this->productSynchronization = $productSynchronization;
    }

    /**
     * @inheritdoc
     */
    public function getStockItems(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        if ($this->stockSynchronization->isTableExists($this->stockSynchronization->getTableName())
            && $this->stockSynchronization->isUseIndexTable()
        ) {
            $data = $this->prepareStockDataFromFlatTable($searchCriteria);

            $searchResult = $this->objectManager->create('Magento\Framework\Api\Search\SearchResultFactory')->create();
            $searchCriteria->setFilterGroups([]);
            $searchCriteria->setSortOrders([]);
            $searchCriteria->setPageSize(0);
            $searchCriteria->setCurrentPage(0);
            $searchResult->setSearchCriteria($searchCriteria);
            $searchResult->setItems($data);
            $searchResult->setTotalCount($this->collectionSize);
            return $searchResult;
        } else {
            $collection = $this->objectManager->create('\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection');
            $storeId = $this->storeManager->getStore()->getId();
            $collection->addAttributeToSelect('name');
            $collection->getSelect()->group('e.entity_id');
            $collection->setStoreId($storeId)->addStoreFilter($storeId);
            $collection->addAttributeToFilter('type_id', ['in' => $this->getProductTypeIds()]);
            $collection = $this->resource->addStockDataToCollection($collection, false);

            /** Integrate webpos **/
            $this->eventManager->dispatch('webpos_inventory_stockitem_getstockitems', [
                'collection' => $collection,
                'location' => $this->permissionManager->getCurrentLocation()
            ]);

            /** End integrate webpos **/

            foreach ($searchCriteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
            foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field, ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }

            $collection->setCurPage($searchCriteria->getCurrentPage());
            $collection->setPageSize($searchCriteria->getPageSize());
            $collection->load();

            $searchResult = $this->objectManager->get('Magento\Framework\Api\Search\SearchResultFactory')->create();
            $searchResult->setSearchCriteria($searchCriteria);
            $searchResult->setItems($collection->getItems());
            $searchResult->setTotalCount($collection->getSize());
            return $searchResult;
        }
    }

    protected function prepareStockDataFromFlatTable(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $storeId = $this->storeManager->getStore()->getId();
        if ($sql = $this->stockSynchronization->getAllDataFromTable()) {
            $productTableName = $this->productSynchronization->getTableName($storeId);
            if ($this->productSynchronization->isTableExists($productTableName)) {
                $sql .= ' LEFT JOIN ' . $this->resource->getTable($productTableName) . ' AS product'
                    . ' ON product.id = e.product_id';

                $sql = str_replace('SELECT e.*', 'SELECT e.*', $sql);
            }

            $where = [];

            // add filter
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $where[] = $this->addFilterGroupToSql($group);
            }

            $obj = new \Magento\Framework\DataObject();
            $obj->setData($where);
            /** Integrate webpos **/
            $this->eventManager->dispatch('webpos_inventory_stockitem_getstockitems_flattable', [
                'object' => $obj,
                'location' => $this->permissionManager->getCurrentLocation()
            ]);

            $where = $obj->getData();
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

            // add sort order
            if ($sortOrder = $searchCriteria->getSortOrders()) {
                if (is_array($sortOrder)) {
                    $sortOrder = $sortOrder[0];
                }
                if ($sortOrder) {
                    $sql .= ' ORDER BY ' . $sortOrder->getField() . ' ' . $sortOrder->getDirection();
                }
            }

            $selectSql = explode('FROM', $sql);
            $countSql = str_replace($selectSql[0], 'SELECT count(*) ', $sql);

            // get page size before set page size and current page
            $this->collectionSize = $this->resource->getConnection()->fetchCol($countSql)[0];

            // page size and cur page
            $pageSize = $searchCriteria->getPageSize();
            $curPage = $searchCriteria->getCurrentPage();
            if($pageSize != null && $curPage != null) {
                $sql .= " LIMIT " . (int)$pageSize .
                    " OFFSET " . (int)$pageSize * ($curPage - 1);
            }

            $result = $this->resource->getConnection()->fetchAll($sql);
            $result = $this->stockSynchronization->prepareGetData($result);
            return $result;
        } else {
            return [];
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
//            if(!in_array($filter->getField(), $this->listAttributes)) {
//                continue;
//            }
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $searchString = $filter->getValue() ? $filter->getValue() : $searchString;
            $field = $filter->getField();

            if ($field == 'e.sku') {
                $field = 'sku';
            } elseif ($field == 'cpev.value') {
                $field = 'name';
            } elseif ($field == 'e.entity_id') {
                $field = 'product_id';
            }

            $conditionType = $this->listCondition[$conditionType];

            if (!$first) {
                $where .= ' OR ';
            }
            if(strtolower($conditionType) != 'in') {
                $where .= "e." . $field . " " . $conditionType . " '" . $searchString . "'";
            } else {
                $where .= "e." . $field . " " . $conditionType . " (" . $searchString . ")";
            }
            $first = false;
        }

        $where .= ')';

        return $where;
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
            $types[] = 'giftvoucher';
        }
        return $types;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup, \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    )
    {
        $where = '(';
        $first = true;
        foreach ($filterGroup->getFilters() as $filter) {
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $condition = $this->convertCondition($conditionType);
            $value = is_array($filter->getValue()) ? "('" . implode("','", $filter->getValue()) . "')" : $filter->getValue();

            if (in_array($condition, ['IN', 'NOT IN'])) {
                $value = '(' . $value . ')';
            } else {
                $value = "'" . $value . "'";
            }


            if (!$first) {
                $where .= ' OR ';
            }
            $where .= $filter->getField() . " " . $condition . ' ' . $value;
            $first = false;
        }

        $where .= ')';
        $collection->getSelect()->where($where);
    }

    /**
     * Convert sql condition from Magento to Zend Db Select
     *
     * @param string $type
     * @return string
     */
    protected function convertCondition($type)
    {
        switch ($type) {
            case 'gt':
                return '>';
            case 'gteq':
                return '>=';
            case 'lt':
                return '<';
            case 'lteq':
                return '=<';
            case 'eq':
                return '=';
            case 'in':
                return 'IN';
            case 'nin':
                return 'NOT IN';
            case 'neq':
                return '!=';
            case 'like':
                return 'LIKE';
            default:
                return '=';
        }
    }

    /**
     *
     * @param array $stockItems
     * @return bool
     */
    public function massUpdateStockItems($stockItems)
    {
        if (count($stockItems)) {
            foreach ($stockItems as $stockItem) {
                if (!$stockItem->getItemId())
                    continue;
                $this->updateStockItem($stockItem->getItemId(), $stockItem);
            }
        }
        return true;
    }

    /**
     *
     * @param string $itemId
     * @param \Magestore\Webpos\Api\Data\Inventory\StockItemInterface $stockItem
     * @return int
     */
    public function updateStockItem($itemId, \Magestore\Webpos\Api\Data\Inventory\StockItemInterface $stockItem)
    {
        $origStockItem = $this->stockItemModel->load($itemId);
        $changeQty = $stockItem->getQty() - $origStockItem->getQty();
        $data = $stockItem->getData();
        if ($origStockItem->getItemId()) {
            unset($data['item_id']);
        }
        $origStockItem->addData($data);

        $stockItem = $this->stockItemRepository->save($origStockItem);

        $this->eventManager->dispatch('webpos_inventory_stockitem_update', [
            'stock_item' => $stockItem,
            'change_qty' => $changeQty,
            'location' => $this->permissionManager->getCurrentLocation(),
            'user' => $this->permissionManager->getCurrentStaffModel()->getUsername(),
        ]);

        return $stockItem->getItemId();
    }

    /**
     * @return StockConfigurationInterface
     *
     * @deprecated
     */
    private function getStockConfiguration()
    {
        if ($this->stockConfiguration === null) {
            $this->stockConfiguration = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\CatalogInventory\Api\StockConfigurationInterface');
        }
        return $this->stockConfiguration;
    }

}
