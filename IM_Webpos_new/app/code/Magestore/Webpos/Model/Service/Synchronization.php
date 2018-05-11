<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Service;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

/**
 * Class Synchronization
 * @package Magestore\Webpos\Model\Service
 */
class Synchronization implements \Magestore\Webpos\Api\Synchronization\SynchronizationInterface
{
    const SYNCHRONIZATION_TYPE = '';

    const SYNCHRONIZATION_CONFIG_LINK = '';

    const SYNCHRONIZATION_CONFIG_UPDATE = '';

    const SYNCHRONIZATION_CONFIG_USE = '';

    const SYNCHRONIZATION_TABLE = '';

    const PAGESIZE = 500;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleDirReader;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $parser;

    /**
     * @var \Magento\Store\Model\StoreRepository
     */
    protected $storeRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writerInterface;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $entityAttribute;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    protected $configCollectionFactory;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Catalog\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected $tableInformation = [];

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
    protected $arrayColumn = [];

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Magento\Framework\Xml\Parser $parser,
        \Magento\Store\Model\StoreRepository $storeRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory,
        \Magento\Framework\App\State $appState,
        \Magestore\Webpos\Model\ResourceModel\Catalog\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->objectManager = $objectManager;
        $this->moduleDirReader = $moduleDirReader;
        $this->parser = $parser;
        $this->storeRepository = $storeRepository;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->writerInterface = $writerInterface;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->entityAttribute = $entityAttribute;
        $this->productMetadata = $productMetadata;
        $this->scopeConfig = $scopeConfig;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->appState = $appState;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->customerMetadata = $customerMetadata;
        $this->eventManager = $eventManager;
    }

    /**
     * Check enable config use index table to synchronization
     *
     * @param string
     * @return boolean
     */
    public function isUseIndexTable()
    {
        return $this->scopeConfig->getValue(static::SYNCHRONIZATION_CONFIG_USE);
    }

    /**
     * @return array
     */
    public function getXmlConfigFile()
    {
        $filePath = $this->moduleDirReader->getModuleDir('etc', 'Magestore_Webpos')
            . '/synchronization.xml';
        $parsedArray = $this->parser->load($filePath)->xmlToArray();
        return $parsedArray['config']['_value']['ms_tmp_table'][static::SYNCHRONIZATION_TYPE];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isTableExists($name)
    {
        return $this->connection->isTableExists($this->resource->getTableName($name));
    }

    /**
     * @param string $name
     */
    public function removeTable($name)
    {
        if ($this->isTableExists($name)) {
            $this->connection->dropTable($this->resource->getTableName($name));
        }
    }


    /**
     * @param null|int|string $storeId
     */
    public function createIndexTable($storeId = null)
    {
        $tableInformation = $this->getTableInformation();
        if (static::SYNCHRONIZATION_TABLE && $tableInformation) {
            $tableName = $this->getTableName($storeId);
            // drop table if exist
            if ($this->isTableExists($tableName)) {
                return;
            }
            // create new table
            $newTmpTable = $this->connection->newTable(
                $this->resource->getTableName($tableName)
            );
            foreach ($tableInformation as $colName => $info) {
                $newTmpTable->addColumn(
                    $colName,
                    $info['type'],
                    $info['size'],
                    $info['option'],
                    $info['comment']
                );
            }
            $this->connection->createTable($newTmpTable);
        }
    }

    /**
     * @return array
     */
    protected function getTableInformation()
    {
        if (!($this->tableInformation)) {
            $this->initTableInformation();
        }
        if (isset($this->tableInformation)) {
            return $this->tableInformation;
        } else {
            return [];
        }
    }

    /**
     * Get data from source table and update to index table
     *
     * @param int|null $storeId
     * @return $this
     */
    public function addIndexTableData($storeId = null)
    {
        $this->setAreaCode();

        if (!$this->canSynchronization()) {
            return $this;
        }

        $this->startSynchronization();

        $updatedTime = $this->getUpdatedTime($storeId);

        $this->setUpdatedTime($storeId);

        $maxPage = ceil($this->getTotalUpdatedData($updatedTime, $storeId) / static::PAGESIZE);

        for ($index = 1; $index <= $maxPage; $index++) {
            $data = $this->prepareSynchronizationData($updatedTime, $storeId, $index);

            if (!empty($data)) {
                $this->importDataToTmpTable($data, $storeId);
            }
        }

        $this->stopSynchronization();
    }


    public function setAreaCode()
    {
        $version = $this->productMetadata->getVersion();
        try {
            if (version_compare($version, '2.2.0', '>=')) {
                $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } else {
                $this->appState->setAreaCode('admin');
            }
        } catch (\Exception $e) {
            $this->appState->getAreaCode();
        }
    }

    /**
     * Check synchronization will be processed by synchronization flag config
     *
     * @return bool
     */
    protected function canSynchronization()
    {
        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configCollection */
        $configCollection = $this->configCollectionFactory->create();
        $configCollection->addFieldToFilter('scope', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $configCollection->addFieldToFilter('scope_id', 0);
        $configCollection->addFieldToFilter('path', ['like' => static::SYNCHRONIZATION_CONFIG_UPDATE]);

        $value = $configCollection->getFirstItem()->getValue();
        if ($value == 1) {
            return false;
        }
        return true;
    }

    /**
     * Set flag config to start synchronization
     *
     * @return $this
     */
    protected function startSynchronization()
    {
        $this->writerInterface->save(
            static::SYNCHRONIZATION_CONFIG_UPDATE,
            1,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        return $this;
    }

    /**
     * Set flag config to stop synchronization
     *
     * @return $this
     */
    protected function stopSynchronization()
    {
        $this->writerInterface->save(
            static::SYNCHRONIZATION_CONFIG_UPDATE,
            0,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * Get last synchronization updated time
     *
     * @return $this
     */
    protected function getUpdatedTime($storeId = null)
    {
        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configCollection */
        $configCollection = $this->configCollectionFactory->create();
        if ($storeId !== null) {
            $configCollection->addFieldToFilter('scope', \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
            $configCollection->addFieldToFilter('scope_id', $storeId);
            $configCollection->addFieldToFilter('path', ['like' => static::SYNCHRONIZATION_CONFIG_LINK]);
        } else {
            $configCollection->addFieldToFilter('scope', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
            $configCollection->addFieldToFilter('scope_id', 0);
            $configCollection->addFieldToFilter('path', ['like' => static::SYNCHRONIZATION_CONFIG_LINK]);
        }
        return $configCollection->getFirstItem()->getValue();
    }

    protected function setUpdatedTime($storeId = null)
    {
        // set new update time
        $new = new \DateTime();

        if ($storeId !== null) {
            $this->writerInterface->save(
                static::SYNCHRONIZATION_CONFIG_LINK,
                $new->format('Y-m-d H:i:s'),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                $storeId
            );
            return $this;
        }

        $this->writerInterface->save(
            static::SYNCHRONIZATION_CONFIG_LINK,
            $new->format('Y-m-d H:i:s'),
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function getTotalUpdatedData($updatedTime, $storeId = null)
    {
        return 1;
    }

    /**
     * Prepare synchronization data to update in synchronization table
     *
     * @param $updatedTime
     * @param null $storeId
     * @return array
     */
    public function prepareSynchronizationData($updatedTime, $storeId = null, $curentPage = 1)
    {
        return [];
    }

    /**
     * Convert associative array into proper data object.
     *
     * @param array $data
     * @param string $type
     * @return array|object
     */
    public function convertValue($data, $type)
    {
        if (is_array($data)) {
            $result = [];
            $arrayElementType = substr($type, 0, -2);
            foreach ($data as $datum) {
                if (is_object($datum)) {
                    $datum = $this->processDataObject(
                        $this->dataObjectProcessor->buildOutputDataArray($datum, $arrayElementType)
                    );
                }
                $result[] = $datum;
            }
            return $result;
        } elseif (is_object($data)) {
            return $this->processDataObject(
                $this->dataObjectProcessor->buildOutputDataArray($data, $type)
            );
        } elseif ($data === null) {
            return [];
        } else {
            /** No processing is required for scalar types */
            return $data;
        }
    }

    /**
     * Convert data object to array and process available custom attributes
     *
     * @param array $dataObjectArray
     * @return array
     */
    protected function processDataObject($dataObjectArray)
    {
        if (isset($dataObjectArray[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY])) {
            $dataObjectArray = ExtensibleDataObjectConverter::convertCustomAttributesToSequentialArray(
                $dataObjectArray
            );
        }
        //Check for nested custom_attributes
        foreach ($dataObjectArray as $key => $value) {
            if (is_array($value)) {
                $dataObjectArray[$key] = $this->processDataObject($value);
            }
        }
        return $dataObjectArray;
    }

    /**
     * @param array $data
     * @param null|int|string $storeId
     */
    public function importDataToTmpTable($inserData, $storeId = null)
    {
        $tableName = $this->getTableName($storeId);
        if(!$this->isTableExists($tableName)) {
            $this->createIndexTable($storeId);
        }
        if (static::SYNCHRONIZATION_TYPE && $this->isTableExists($tableName)) {
            foreach ($inserData as $data) {
                $this->verifyBeforeInsert($data);

                if ($this->arrayColumn) {
                    foreach ($data as $key => $val) {
                        if (in_array($key, $this->arrayColumn) && is_array($val)) {
                            $data[$key] = json_encode($val);
                        }
                    }
                }

                $this->connection->insertOnDuplicate(
                    $this->resource->getTableName($tableName),
                    $data
                );
            }
        }
    }

    protected function verifyBeforeInsert(&$product)
    {

    }

    /**
     * Return valid table name with store id
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTableName($storeId = null)
    {
        $tableName = static::SYNCHRONIZATION_TABLE;
        if ($storeId !== null) {
            $tableName .= '_' . $storeId;
        }
        return $tableName;
    }

    /**
     * @param null|int|string $storeId
     * @return bool|string
     */
    public function getAllDataFromTable($storeId = null)
    {
        $tableName = $this->getTableName($storeId);
        if (static::SYNCHRONIZATION_TABLE && $this->isTableExists($tableName)) {
            $tableName = $this->resource->getTableName($tableName);
            $sql = "SELECT e.* FROM " . $tableName . " AS e";
            return $sql;
        }
        return false;
    }

    /**
     * @param int $curPage
     * @param int $pageSize
     * @param null|int|string $storeId
     * @return array
     */
    public function getDataFromTmpTable($curPage, $pageSize, $storeId = null)
    {
        $tableName = $this->getTableName($storeId);
        if (static::SYNCHRONIZATION_TABLE && $this->isTableExists($tableName)) {
            $tableName = $this->resource->getTableName($tableName);
            $sql = "Select * FROM " . $tableName .
                " LIMIT " . (int)$pageSize .
                " OFFSET " . (int)$pageSize * ($curPage - 1);
            $result = $this->connection->fetchAll($sql);
            $result = $this->prepareGetData($result);
            return $result;
        }
        return [];
    }

    /**
     * @param null|int|string $storeId
     * @return int|void
     */
    public function getTotalItemTmpTable($storeId = null)
    {
        $tableName = $this->getTableName($storeId);
        if (static::SYNCHRONIZATION_TABLE && $this->isTableExists($tableName)) {
            $tableName = $this->resource->getTableName($tableName);
            $sql = "SELECT COUNT(*) FROM " . $tableName;
            $result = $this->connection->fetchCol($sql);
            return $result[0];
        }
        return 0;
    }

    protected function initTableInformation()
    {
        $configData = $this->getXmlConfigFile();
        if (is_array($configData)) {
            $this->tableInformation = $configData;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareGetData($data)
    {
        if ($this->arrayColumn) {
            $colArray = $this->arrayColumn;
            foreach ($data as &$product) {
                foreach ($product as $key => $val) {
                    if (in_array($key, $colArray) && is_string($val)) {
                        $product[$key] = json_decode($val);
                    }
                }
            }
        }
        if ($this->getNumberColumns()) {
            $colArray = $this->getNumberColumns();
            foreach ($data as &$product) {
                foreach ($product as $key => $val) {
                    if (in_array($key, $colArray) && is_string($val)) {
                        $product[$key] = (float)($val);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getNumberColumns()
    {
        $config = $this->getXmlConfigFile();
        $colsNumber = [];
        foreach ($config as $key => $data) {
            if (in_array($data['type'], ['integer', 'float', 'decimal', 'double'])) {
                $colsNumber[] = $key;
            }
        }
        return array_values($colsNumber);
    }

    protected function getListStoreIds()
    {
        $stores = $this->storeRepository->getList();
        $storeIds = [];
        foreach ($stores as $store) {
            $storeIds[] = $store["store_id"];
        }
        asort($storeIds);
        return $storeIds;
    }

    public function isMagentoEnterprise()
    {
        $edition = $this->productMetadata->getEdition();
        if ($edition == 'Enterprise') {
            return true;
        } else {
            return false;
        }
    }
}