<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Grid;

use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Product as WarehouseProductResource;
use Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface as WarehouseProductInterface;
use Magestore\InventorySuccess\Model\ResourceModel\WarehouseStoreViewMap;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @inheritdoc
     */
    protected $document = Document::class;

    const MAPPING_FIELDS = [
        'warehouse_id' => 'main_table.warehouse_id',
        'warehouse' => 'CONCAT(warehouse_name, " (",warehouse_code,")")',
        'total_sku'=> 'COUNT(wh_product.'. WarehouseProductInterface::WAREHOUSE_PRODUCT_ID .')',
        'total_qty' => 'SUM(IFNULL(wh_product.total_qty,0))',
        'store_ids' => 'GROUP_CONCAT(wh_store_view.store_id)'
    ];
    
    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'os_warehouse',
        $resourceModel = 'Magestore\InventorySuccess\Model\ResourceModel\Warehouse'
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['wh_product' => $this->getTable(WarehouseProductResource::MAIN_TABLE)],
                'main_table.warehouse_id = wh_product.' . WarehouseProductInterface::WAREHOUSE_ID,
                []
            )->joinLeft(
                ['wh_store_view' => $this->getTable(WarehouseStoreViewMap::MAIN_TABLE)],
                'main_table.warehouse_id = wh_store_view.warehouse_id',
                []
            )->columns(
                [
                    'warehouse' => new \Zend_Db_Expr(self::MAPPING_FIELDS['warehouse']),
                    'total_sku' => new \Zend_Db_Expr(self::MAPPING_FIELDS['total_sku']),
                    'total_qty' => new \Zend_Db_Expr(self::MAPPING_FIELDS['total_qty']),
                    'store_id' => new \Zend_Db_Expr(self::MAPPING_FIELDS['store_ids'])
                ]
            )->group('main_table.warehouse_id');
        return $this;
    }
    
    public function addFieldToFilter($field, $condition = null)
    {
        if(in_array($field, array_keys(self::MAPPING_FIELDS)))
            $field = new \Zend_Db_Expr(self::MAPPING_FIELDS[$field]);
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if(in_array($field, array_keys(self::MAPPING_FIELDS)))
            $field = new \Zend_Db_Expr(self::MAPPING_FIELDS[$field]);
        return parent::setOrder($field, $direction);
    }
}
