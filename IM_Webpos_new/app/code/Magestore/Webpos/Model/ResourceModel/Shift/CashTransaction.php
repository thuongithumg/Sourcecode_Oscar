<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:50
 */

namespace Magestore\Webpos\Model\ResourceModel\Shift;


class CashTransaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('webpos_cash_transaction', 'transaction_id');
    }
}