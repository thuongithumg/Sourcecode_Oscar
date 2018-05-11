<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Zreport;

class Prints extends \Magestore\Webpos\Block\Adminhtml\AbstractBlock
{
    protected $authSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ){
        $this->authSession = $authSession;
        parent::__construct($context, $objectManager, $messageManager, $data);
    }

    public function getZreportData(){
        /** @var \Magestore\Webpos\Model\Shift\ShiftRepository $shiftRepository */
        $shiftRepository = $this->_objectManager->create('Magestore\Webpos\Model\Shift\ShiftRepository');
        $id = $this->getRequest()->getParam('id');
        $data = $shiftRepository->getInfo($id);
        $onlyprint = $this->getRequest()->getParam('onlyprint');
        $difference = $this->getRequest()->getParam('difference');
        $theoreticalClosingBalance = $this->getRequest()->getParam('theoretical_closing_balance');
        $realClosingBalance = $this->getRequest()->getParam('real_closing_balance');
        $closedAt = $this->getRequest()->getParam('closed_at');
        $data['onlyprint'] = ($onlyprint == 1)?true:false;
        if($data['status'] != 1){
            if(!empty($realClosingBalance)){
                $data['closed_amount'] = $realClosingBalance;
            }
            if(!empty($closedAt)){
                $data['closed_at'] = $closedAt;
            }
        }
        if(empty($theoreticalClosingBalance)){
            $theoreticalClosingBalance = floatval($data['cash_sale']) + floatval($data['cash_added']) - floatval($data['cash_removed']) + floatval($data['float_amount']) - floatval($data['cash_refunded']);// + floatval($data['closed_amount']);
        }
        if(empty($difference)){
            $difference = floatval($data['closed_amount']) - floatval($theoreticalClosingBalance);
        }
        $data['theoretical_closing_balance'] = $theoreticalClosingBalance;
        $data['difference'] = $difference;
        $data['admin_name'] = $this->authSession->getUser()->getName();
        return $data;
    }

    public function formatReportPrice($price){
        $helper = $this->_objectManager->get('Magestore\Webpos\Helper\Data');
        return $helper->formatPrice($price);
    }

    public function formatReportDate($date){
        $helper = $this->_objectManager->get('Magestore\Webpos\Helper\Data');
        return $helper->formatDate($date);
    }
}