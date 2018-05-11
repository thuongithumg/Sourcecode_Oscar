<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\SearchResultCustomers;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{

    public function _construct()
    {
        $this->_init('Magestore\Webpos\Model\SearchResultCustomers', 'Magestore\Webpos\Model\ResourceModel\SearchResultCustomers');
    }
}