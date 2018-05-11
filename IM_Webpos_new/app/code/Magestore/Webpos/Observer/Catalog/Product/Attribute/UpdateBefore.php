<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Catalog\Product\Attribute;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class UpdateBefore
 * @package Magestore\Webpos\Observer\Catalog\Product\Attribute
 */
class UpdateBefore implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * CatalogProdcutSaveAfter constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime
    )
    {
        $this->productResource = $productResource;
        $this->date = $date;
        $this->dateTime = $dateTime;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $productIds = $observer->getProductIds();
        if (empty($productIds))
            return $this;
        $connection = $this->productResource->getConnection();
        try {
            $productTable = $connection->getTableName('catalog_product_entity');
            $updatedTime = $this->dateTime->formatDate($this->date->gmtTimestamp());
            $connection->beginTransaction();
            $connection->update(
                $productTable,
                ['updated_at' => $updatedTime],
                ['entity_id IN (?)' => $productIds]
            );
            $connection->commit();
        }catch (\Exception $e){
            $connection->rollBack();
        }
    }
}