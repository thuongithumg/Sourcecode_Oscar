<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Transaction;

/**
 * class \Magestore\Webpos\Model\ResourceModel\Transaction\Collection
 * 
 * Web POS Transaction Collection resource model
 * Methods:
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     * @var string 
     */
    protected $_idFieldName = 'transaction_id';
    
    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Transaction', 'Magestore\Webpos\Model\ResourceModel\Transaction');
    }
}