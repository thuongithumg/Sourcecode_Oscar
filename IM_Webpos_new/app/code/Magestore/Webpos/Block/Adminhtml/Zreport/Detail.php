<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Zreport;

use \Magestore\Webpos\Api\Data\Shift\ShiftInterface as ShiftInterface;

/**
 * Class Detail
 * @package Magestore\Webpos\Block\Adminhtml\Zreport
 */
class Detail extends \Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab\Detail {

    /**
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface
     */
    public function getCurrentSession(){
        $sessionId = $this->getRequest()->getParam('id');
        $session = $this->shiftRepository->get($sessionId);
        if($session->getStatus() != 1){
            throw new \Magento\Framework\Exception\StateException(__('Please close your session first'));
        }
        return $session;
    }

    /**
     * @return string
     */
    public function getCurrentPosId(){
        return $this->getCurrentSession()->getPosId();
    }

    /**
     * @return array
     */
    public function getCurrentSessions(){
        $sessionsData = [];
        $session = $this->getCurrentSession();
        if($session){
            $sessionData = $session->getData();
            $sessionData[ShiftInterface::STAFF_NAME] =  $session->getStaffName();
            $sessionData[ShiftInterface::SALE_SUMMARY] =  $session->getSaleSummary();
            $sessionData[ShiftInterface::CASH_TRANSACTION] =  $session->getCashTransaction();
            $sessionData[ShiftInterface::ZREPORT_SALES_SUMMARY] =  $session->getZreportSalesSummary();
            $sessionData['print_url'] =  $this->getUrl('webposadmin/zreport/prints', ['id' => $session->getEntityId(), 'onlyprint' => 1]);
            $sessionsData[] = $sessionData;
        }
        return $sessionsData;
    }

}