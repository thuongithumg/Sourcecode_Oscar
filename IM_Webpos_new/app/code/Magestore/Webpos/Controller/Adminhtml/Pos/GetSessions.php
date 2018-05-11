<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

use \Magestore\Webpos\Api\Data\Shift\ShiftInterface as ShiftInterface;

/**
 * Class GetSessions
 * @package Magestore\Webpos\Controller\Adminhtml\Pos
 */
class GetSessions extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractPos
{
    /**
     * @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
     */
    protected $shiftRepository;

    /**
     * @var \Magestore\Webpos\Api\Shift\CashTransactionRepositoryInterface
     */
    protected $cashTransactionRepository;

    /**
     * GetSessions constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magestore\Webpos\Model\Pos\PosRepository $posRepository
     * @param \Magestore\Webpos\Model\Pos\PosFactory $posFactory
     * @param \Magento\Backend\Helper\Js $backendJsHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository
     * @param \Magestore\Webpos\Api\Shift\CashTransactionRepositoryInterface $cashTransactionRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magestore\Webpos\Model\Pos\PosRepository $posRepository,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magento\Backend\Helper\Js $backendJsHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository,
        \Magestore\Webpos\Api\Shift\CashTransactionRepositoryInterface $cashTransactionRepository
    ) {
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $posRepository, $posFactory, $backendJsHelper, $resultJsonFactory);
        $this->shiftRepository = $shiftRepository;
        $this->cashTransactionRepository = $cashTransactionRepository;
    }

    /**
     * JSON
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($this->getPosData());
        return $resultJson;
    }

    /**
     * @return array
     */
    public function getPosData(){
        $response = [];
        $response['sessions'] = $this->getCurrentSessions();
        $response['denominations'] = $this->getCurrentDenominations();
        return $response;
    }

    /**
     * @return array
     */
    public function getCurrentSessions(){
        $posId = $this->getBodyParams('pos_id');
        $sessionsData = [];
        $sessions = ($posId)?$this->shiftRepository->getOpenSession($posId):[];
        if(!empty($sessions)){
            foreach ($sessions as $session){
                $sessionData = $session->getData();
                $sessionData[ShiftInterface::STAFF_NAME] =  $session->getStaffName();
                $sessionData[ShiftInterface::SALE_SUMMARY] =  $session->getSaleSummary();
                $sessionData[ShiftInterface::CASH_TRANSACTION] =  $session->getCashTransaction();
                $sessionData[ShiftInterface::ZREPORT_SALES_SUMMARY] =  $session->getZreportSalesSummary();
                $sessionData['print_url'] =  $this->getUrl('webposadmin/zreport/prints', ['id' => $session->getEntityId(), 'onlyprint' => 1]);
                $sessionsData[] = $sessionData;
            }
        }
        return $sessionsData;
    }

    /**
     * @return array
     */
    public function getCurrentDenominations(){
        $posId = $this->getBodyParams('pos_id');
        $denominations = [];
        $pos = ($posId)?$this->posRepository->get($posId):false;
        if($pos){
            $denominations = $pos->getDenominations();
        }
        return $denominations;
    }
}
