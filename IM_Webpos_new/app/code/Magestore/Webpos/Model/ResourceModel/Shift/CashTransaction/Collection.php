<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 14:06
 */

namespace Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'transaction_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Shift\CashTransaction', 'Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction');
    }

}