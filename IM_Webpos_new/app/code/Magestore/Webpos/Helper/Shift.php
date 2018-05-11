<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;


class Shift extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Webpos\Model\Staff\WebPosSessionFactory
     */
    protected $_webposSessionFactory;
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $_staffFactory;

    /** @var  \Magestore\Webpos\Helper\Permission */
    protected $_permissionHelper;

    /** @var $shiftFactory  \Magestore\Webpos\Model\Shift\ShiftFactory */

    protected $_shiftFactory;

    /** @var $_saleSummaryFactory  \Magestore\Webpos\Model\Shift\SaleSummaryFactory */
    protected $_saleSummaryFactory;

    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\TransactionFactory */
    protected $_cashTransactionFactory;

    /** @var  $posRepository \Magestore\Webpos\Model\Pos\PosRepository */
    protected $posRepository;


    /**
     * Shift constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Model\Staff\WebPosSessionFactory $sessionFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param Permission $permissionHelper
     * @param \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Model\Shift\SaleSummaryFactory $saleSummaryFactory
     * @param \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory
     * @param \Magestore\Webpos\Model\Pos\PosRepository $posRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Model\Staff\WebPosSessionFactory $sessionFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magestore\Webpos\Helper\Permission $permissionHelper,     
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Model\Shift\SaleSummaryFactory $saleSummaryFactory,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
        \Magestore\Webpos\Model\Pos\PosRepository $posRepository
    ) {
        $this->_objectManager = $objectManager;
        $this->_webposSessionFactory = $sessionFactory;
        $this->_cookieManager = $cookieManager;
        $this->_staffFactory = $staffFactory;
        $this->_permissionHelper = $permissionHelper;
        $this->_shiftFactory = $shiftFactory;
        $this->_saleSummaryFactory = $saleSummaryFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->posRepository = $posRepository;

        parent::__construct($context);
    }

    /**
     * @return int
     */
    public function getCurrentShiftId()
    {
        $staffId = $this->_permissionHelper->getCurrentUser();
        $staffModel = $this->_staffFactory->create()->load($staffId);
        $locationId = $staffModel->getLocationId();
        $shiftModel = $this->_shiftFactory->create();
        $shiftId = $shiftModel->getCurrentShiftId($staffId);

        return $shiftId;
    }

    /**
     * @param $shiftId
     * @return array
     */
    public function prepareOfflineShiftData($shiftId){
        $shiftModel = $this->_shiftFactory->create();
        $shiftModel->load($shiftId, "shift_id");
        $shiftData = $shiftModel->getData();
        $shiftData = $shiftModel->updateShiftDataCurrency($shiftData);

        /** @var \Magestore\Webpos\Model\Shift\SaleSummary $saleSummaryModel */
        $saleSummaryModel = $this->_saleSummaryFactory->create();
        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();
        //get all sale summary data of the shift with id=$itemData['shift_id']
        $saleSummaryData = $saleSummaryModel->getSaleSummary($shiftId);
        //get all cash transaction data of the shift with id=$itemData['shift_id']
        $transactionData = $cashTransactionModel->getByShiftId($shiftId);
        //get data for zreport
        $zReportSalesSummary = $saleSummaryModel->getZReportSalesSummary($shiftId);

        $shiftData["sale_summary"] = $saleSummaryData;
        $shiftData["cash_transaction"] = $transactionData;
        $shiftData["zreport_sales_summary"] = $zReportSalesSummary;
        if(isset($shiftData['pos_id']) && $shiftData['pos_id']) {
            $shiftData["pos_name"] = $this->posRepository->get($shiftData['pos_id'])->getPosName();
        }

        $shiftModel->updateTotalSales($zReportSalesSummary['grand_total']);
        
        return $shiftData;
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


}
