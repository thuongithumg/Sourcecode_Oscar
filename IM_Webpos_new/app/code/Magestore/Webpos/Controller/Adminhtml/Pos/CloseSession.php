<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * Class CloseSession
 * @package Magestore\Webpos\Controller\Adminhtml\Pos
 */
class CloseSession extends \Magestore\Webpos\Controller\Adminhtml\Pos\GetSessions
{
    /**
     * JSON
     */
    public function execute()
    {
        $sessionId = $this->getBodyParams('session_id');
        $realClosingBalance = $this->getBodyParams('real_closing_balance');
        $baseRealClosingBalance = $this->getBodyParams('base_real_closing_balance');
        $profitLossReason = $this->getBodyParams('profit_loss_reason');
        $closedAt = $this->getBodyParams('closed_at');
        if($sessionId){
            $messages = [
                'success' => [],
                'errors' => []
            ];
            try{
                $realClosingBalance = ($realClosingBalance)?floatval($realClosingBalance):0;
                $baseRealClosingBalance = ($baseRealClosingBalance)?floatval($baseRealClosingBalance):0;
                $session = $this->shiftRepository->get($sessionId);
                $session->setStatus(1);
                $session->setBalance(0);
                $session->setBaseBalance(0);
                $session->setClosedAmount($realClosingBalance);
                $session->setBaseClosedAmount($baseRealClosingBalance);
                $session->setCashRemoved($session->getCashRemoved() + $realClosingBalance);
                $session->setBaseCashRemoved($session->getBaseCashRemoved() + $baseRealClosingBalance);
                $session->setClosedAt($closedAt);
                $session->setProfitLossReason($profitLossReason);
                $this->shiftRepository->save($session);
                $messages['success'][] = __('Session has been closed successfully');
            }catch (\Exception $e){
                $messages['errors'][] = $e->getMessage();
            }
            $this->_processResponseMessages($messages);
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($this->getPosData());
        return $resultJson;
    }
}
