<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Sales\Order;

use Magestore\Webpos\Api\Data\Sales\OrderSearchResultInterface;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection implements OrderSearchResultInterface
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Sales\Order', 'Magento\Sales\Model\ResourceModel\Order');
        $this->addFilterToMap(
            'entity_id',
            'main_table.entity_id'
        )->addFilterToMap(
            'customer_id',
            'main_table.customer_id'
        )->addFilterToMap(
            'quote_address_id',
            'main_table.quote_address_id'
        );
    }
}
