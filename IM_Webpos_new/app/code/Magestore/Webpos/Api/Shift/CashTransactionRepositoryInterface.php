<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:19
 */

namespace Magestore\Webpos\Api\Shift;
use Magestore\Webpos\Api\Data\Shift\CashTransactionInterface;


interface CashTransactionRepositoryInterface
{

    /**
     * @param  \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface $cashTransaction
     * @return  \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface $cashTransactionInterface
     */
    public function save(CashTransactionInterface $cashTransaction);


}