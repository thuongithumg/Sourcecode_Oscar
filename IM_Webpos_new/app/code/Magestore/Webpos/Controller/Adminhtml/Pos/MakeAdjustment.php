<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * Class MakeAdjustment
 * @package Magestore\Webpos\Controller\Adminhtml\Pos
 */
class MakeAdjustment extends \Magestore\Webpos\Controller\Adminhtml\Pos\GetSessions
{
    /**
     * JSON
     */
    public function execute()
    {
        $transactionData = $this->getBodyParams('transaction');
        $transaction = $this->cashTransactionRepository->createTransaction($transactionData);
        $this->cashTransactionRepository->save($transaction);
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($this->getPosData());
        return $resultJson;
    }
}
