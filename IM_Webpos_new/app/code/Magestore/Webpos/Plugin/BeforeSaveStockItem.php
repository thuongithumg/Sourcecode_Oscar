<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin;

use \Magento\CatalogInventory\Api\Data\StockItemInterface;
use \Magento\CatalogInventory\Model\Stock\StockItemRepository;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class BeforeSaveStockItem {

    /**
     * @var DateTime
     */
    protected $dateTime;
    
    
    public function __construct(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }
    
    public function beforeSave(StockItemRepository $stockItemRepository, StockItemInterface $stockItem) 
    {
        $stockItem->setUpdatedTime($this->dateTime->gmtDate());
    }

}
