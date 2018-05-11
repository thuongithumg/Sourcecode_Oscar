<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\InventorySuccess\Ui\DataProvider\TransferStock;

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magestore\InventorySuccess\Model\ResourceModel\TransferStock;
use Magestore\InventorySuccess\Model\TransferStock as TransferStockModel;

/**
 * Class Generate
 * @package Magestore\InventorySuccess\Ui\DataProvider\AdjustStock\Form
 */
class WarehouseProductStockList extends ProductDataProvider
{

    /**
     * @var \Magestore\InventorySuccess\Model\Warehouse\WarehouseManagement
     */
    protected $warehouseManagement;


    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * GlobalStock collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /** @var  \Magestore\InventorySuccess\Model\TransferStockFactory */
    protected $transferStockFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magestore\InventorySuccess\Model\Warehouse\WarehouseManagement $warehouseManagement
     * @param \Magento\Framework\App\RequestInterface request
     * @param \Magestore\InventorySuccess\Model\ResourceModel\AdjustStock $adjustStockResource
     * @param \Magestore\InventorySuccess\Model\AdjustStockFactory $adjustStockFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magestore\InventorySuccess\Model\Warehouse\WarehouseManagement $warehouseManagement,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\InventorySuccess\Model\TransferStockFactory $transferStockFactory,
        \Magestore\InventorySuccess\Model\Locator\LocatorFactory $locatorFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->request = $request;
        $this->warehouseManagement = $warehouseManagement;
        $this->transferStockFactory = $transferStockFactory;
        $this->collection = $this->getProductCollection();

    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $path = $storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        foreach ($items as &$item) {
            if(isset($item['image'])) {
                $item['image_url'] = $path.'catalog/product'.$item['image'];
            }
        }

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCollection()
    {
        $warehouseId = $this->getWarehouseId();
        if ($warehouseId) {
            $collection = $this->warehouseManagement->getListProduct($warehouseId);
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $collection = $objectManager->create('Magestore\InventorySuccess\Model\ResourceModel\TransferStock\GlobalStock\Collection');
        }
        return $collection;
    }

    /**
     * Get current Adjustment
     *
     * @return Adjustment
     * @throws NoSuchEntityException
     */
    public function getWarehouseId()
    {
        $transferstockId = $this->request->getParam('transferstock_id');
        $warehouseId = 0;
        if ($transferstockId) {
            $transferStock = $this->transferStockFactory->create()->load($transferstockId);
            $warehouseId = $transferStock->getSourceWarehouseId();
            if ($transferStock->getType() == TransferStockModel::TYPE_FROM_EXTERNAL) {
                $warehouseId = 0;
            }
        }
        return $warehouseId;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (in_array($filter->getField(), ['category'])) {
            $value = $filter->getValue();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $collection1 = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                ->joinField(
                    'category_id',
                    'catalog_category_product',
                    'category_id',
                    'product_id=entity_id',
                    'category_id=' . $value,
                    'left'
                );
            $collection1->getSelect()->where('category_id = ?', $value);
            $productIds = $collection1->getColumnValues('entity_id');
            $this->getCollection()->getSelect()->where('entity_id in (?)', $productIds);
        } else {
            return parent::addFilter($filter);
        }
    }

}