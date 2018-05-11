<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class InstallManagement extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Store\Model\StoreRepository
     */
    protected $storeRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Webapi\ServiceOutputProcessor
     */
    protected $serviceOutputProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var Catalog\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writerInterface;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    protected $configCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Inventory\Stock\Item
     */
    protected $resourceStockItem;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $entityAttribute;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var array
     */
    protected $sychronizationModel = [];

    /**
     * InstallManagement constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreRepository $storeRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param Catalog\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Inventory\Stock\Item $resourceStockItem
     * @param \Magento\Eav\Model\Entity\Attribute $entityAttribute
     * @param \Magento\Framework\App\State $appState
     * @param array $sychronizationModel
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreRepository $storeRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magestore\Webpos\Model\ResourceModel\Catalog\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $resourceStockItem,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Framework\App\State $appState,
        array $sychronizationModel = [],
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->writerInterface = $writerInterface;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->storeManager = $storeManager;
        $this->resourceStockItem = $resourceStockItem;
        $this->entityAttribute = $entityAttribute;
        $this->appState = $appState;
        $this->sychronizationModel = $sychronizationModel;
    }

    public function _construct()
    {

    }

    public function convertSaleItemsData()
    {
        $connection = $this->_getConnection('read');
        $connection->beginTransaction();
        $select = $connection->select()->from(array('main_table' => $this->getTable('sales_order')), array(
            'webpos_delivery_date',
            'entity_id'
        ));
        $query = $connection->query($select);
        $conditions = array();
        $itemIdsArray = array();
        while ($row = $query->fetch()) {
            if (!$row['webpos_delivery_date']) {
                continue;
            }
            $itemIdsArray[] = $row['entity_id'];
            $case = $connection->quoteInto('?', $row['entity_id']);
            $deliveryDate = $row['webpos_delivery_date'] ? $row['webpos_delivery_date'] : '';
            $conditions['convert'][$case] = "'$deliveryDate'";
        }
        if (!$conditions) {
            $connection->commit();
            return $this;
        }
        $values = array(
            'webpos_delivery_date' => $connection->getCaseSql('entity_id', $conditions['convert']),
        );
        $where = array('entity_id IN (?)' => $itemIdsArray);
        /* query to update webpos_delivery_date */
        $connection->update(
            $this->getTable('sales_order_grid'),
            $values,
            $where
        );
        $connection->commit();
    }

    public function createIndexTable($type)
    {
        if (isset($this->sychronizationModel[$type])) {
            $this->sychronizationModel[$type]->createIndexTable();
        }
    }

    public function addIndexTableData($type)
    {
        if (isset($this->sychronizationModel[$type])) {
            $this->sychronizationModel[$type]->addIndexTableData();
        }
    }
}