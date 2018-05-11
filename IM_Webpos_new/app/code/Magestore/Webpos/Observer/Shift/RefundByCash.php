<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Shift;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\Webpos\Model\Shift\Shift;


class RefundByCash implements ObserverInterface
{
    /** @var \Magestore\Webpos\Model\Shift\ShiftFactory */
    protected $_shiftFactory;

    /** @var  \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory */
    protected $_orderPaymentCollectionFactory;

    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\TransactionFactory */
    protected $_cashTransactionFactory;

    /** @var  \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\CollectionFactory */
    protected $_cashTransactionCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /** @var  \Magestore\Webpos\Helper\Currency */
    protected $_webposCurrencyHelper;

    protected $_orderIncrementId;
    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $staffFactory;

    /**
     * @var \Magestore\Webpos\Helper\Shift
     */
    protected $_shiftHelper;

    /**
     * SalesOrderAfterPlaceObserver constructor.
     * @param \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory $orderPaymentCollectionFactory
     * @param \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\CollectionFactory $cashTransactionCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magestore\Webpos\Helper\Currency $webposCurrencyHelper
     */
    public function __construct(
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory $orderPaymentCollectionFactory,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
        \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\CollectionFactory $cashTransactionCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Helper\Currency $webposCurrencyHelper,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magestore\Webpos\Helper\Shift $shiftHelper
    )
    {
        $this->_shiftFactory = $shiftFactory;
        $this->_orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->_cashTransactionCollectionFactory = $cashTransactionCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_webposCurrencyHelper = $webposCurrencyHelper;
        $this->staffFactory = $staffFactory;
        $this->_shiftHelper = $shiftHelper;
    }

    /**
     * create cash transaction for the current opening shift of the staff after an order is created.
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $creditmemo = $observer->getData('creditmeno');
        if (!$creditmemo) {
            return $this;
        }
        $order = $creditmemo->getOrder();

        $this->_orderIncrementId = $creditmemo->getIncrementId();
        $shiftId = $this->_shiftHelper->getCurrentShiftId();

        if (!$shiftId) {
            return $this;
        }

        $shiftModel = $this->_shiftFactory->create();
        $shiftModel->load($shiftId, "shift_id");
        if (!$shiftModel->getShiftId()) {
            return $this;
        }

        //create new transaction
        $this->createCashTransactionWhenCreateCreditMemo($creditmemo, $shiftModel);
        //update shift information: total_sales, cash_added, cash_removed
        $this->updateShiftWhenCreateCreditMemo($creditmemo, $shiftModel);
        return $this;
    }

    /**
     * update shift information: total_sales, cash_added, cash_removed
     * @param $creditmemo
     * @param $shiftId
     * @return array
     */
    public function updateShiftWhenCreateCreditMemo($creditmemo, $shiftModel)
    {
        $orderPaymentData = null;

        /** @var \Magestore\Webpos\Model\Shift\Shift $shiftModel */
//        $cashSale = $shiftModel->getCashSale();
        $cashRefunded = $shiftModel->getCashRefunded();
        $balance = $shiftModel->getBalance();
//        $totalSales = $shiftModel->getTotalSales();

//        $cashSale = $cashSale - $creditmemo->getGrandTotal();
        $cashRefunded += $creditmemo->getGrandTotal();
        $balance = $balance - $creditmemo->getGrandTotal();
//        $totalSales = $totalSales - $creditmemo->getGrandTotal();

        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
//        $baseCashSale = $this->_webposCurrencyHelper->convertToBase($cashSale, $currentCurrencyCode);
        $baseCashRefunded = $this->_webposCurrencyHelper->convertToBase($cashRefunded, $currentCurrencyCode);
        $baseBalance = $this->_webposCurrencyHelper->convertToBase($balance, $currentCurrencyCode);
//        $baseTotalSales = $this->_webposCurrencyHelper->convertToBase($totalSales, $currentCurrencyCode);

//        $shiftModel->setCashSale($cashSale);
//        $shiftModel->setBaseCashSale($baseCashSale);
        $shiftModel->setCashRefunded($cashRefunded);
        $shiftModel->setBaseCashRefunded($baseCashRefunded);
        $shiftModel->setBalance($balance);
        $shiftModel->setBaseBalance($baseBalance);
//        $shiftModel->setTotalSales($totalSales);
//        $shiftModel->setBaseTotalSales($baseTotalSales);
        $staffId = $shiftModel->getStaffId();
        $staffName = $this->staffFactory->create()->load($staffId)->getDisplayName();
        $shiftModel->setData('staff_name', $staffName);
        $shiftModel->save();
        return $this;
    }

    /**
     * create a transaction on cash_transaction table
     * with type: order
     * value: payment_amount
     * base_value: base_payment_amount
     * @param $orderPaymentData
     * @param $shiftData
     */
    public function createCashTransactionWhenCreateCreditMemo($creditmemo, $shiftData)
    {
        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();

        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $data['shift_id'] = $shiftData['shift_id'];
        $data['location_id'] = $shiftData['location_id'];
        $data['staff_name'] = $shiftData['staff_name'];
        $data['staff_id'] = $shiftData['staff_id'];
        $data['value'] = $creditmemo['grand_total'];
        $data['base_value'] = $this->_webposCurrencyHelper->convertToBase($data['value'], $currentCurrencyCode);
        $data['balance'] = $shiftData['balance'];
        $data['base_balance'] = $this->_webposCurrencyHelper->convertToBase($data['balance'], $currentCurrencyCode);
        $data['note'] = "Refund by cash order with id=" . $this->_orderIncrementId;
        $data['order_id'] = $creditmemo['order_id'];
        $data['type'] = "refund";
        $data['base_currency_code'] = $baseCurrencyCode;
        $data['transaction_currency_code'] = $currentCurrencyCode;

        $cashTransactionModel->setData($data);
        $cashTransactionModel->save();
    }


}
