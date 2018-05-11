<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Inventory\Stock;


/**
 * Class StockItemRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Item extends \Magento\CatalogInventory\Model\Stock\Item
    implements \Magestore\Webpos\Api\Data\Inventory\StockItemInterface
{
    public function getName() {
        return $this->getData('name');
    }

    public function getSku() {
        return $this->getData('sku');
    }

    public function setName($name) {
        $this->setData('name', $name);
        return $this;
    }

    public function setSku($sku) {
        $this->setData('sku', $sku);
        return $this;        
    }

    public function getUpdatedTime() {
        return $this->getData('updated_time');
    }

    public function setUpdatedTime($updatedTime) {
        $this->setData('updated_time', $updatedTime);
        return $this;        
    }
}
