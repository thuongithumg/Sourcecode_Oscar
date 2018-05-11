<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Shift;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\Webpos\Model\Shift\Shift;


class SalesOrderAfterPlaceObserver implements ObserverInterface
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
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory

    )
    {
        $this->_shiftFactory = $shiftFactory;
        $this->_orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->_cashTransactionCollectionFactory = $cashTransactionCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_webposCurrencyHelper = $webposCurrencyHelper;
        $this->staffFactory = $staffFactory;
    }

    /**
     * create cash transaction for the current opening shift of the staff after an order is created.
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return $this;
        }

        $orderId = $order->getId();
        $this->_orderIncrementId = $order->getIncrementId();
        $shiftId = $order->getWebposShiftId();

        if (!$shiftId) {
            return $this;
        }

        $shiftModel = $this->_shiftFactory->create();
        $shiftModel->load($shiftId, "shift_id");
        if (!$shiftModel->getShiftId()) {
            return $this;
        }

        /** @var \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\Collection $cashTransactionCollection */
        $cashTransactionCollection = $this->_cashTransactionCollectionFactory->create();

        //get all cash transaction for the order with order_id=$oderId.
        $cashTransactionCollection->addFieldToFilter("order_id", $orderId);
        //check if we've created cash transaction for with order_id=$orderId or not.
        //if yes, we have not to create it again
        if ($cashTransactionCollection->getSize() > 0) {
            return $this;
        }
        /** @var \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\Collection $orderPaymentCollection */
        $orderPaymentCollection = $this->_orderPaymentCollectionFactory->create();
        //we check if there is no order payment record with order_id=$orderId
        //if no, we do nothing.
        $orderPaymentCollection->addFieldToFilter("order_id", $orderId);
        if ($orderPaymentCollection->getSize() == 0) {
            return $this;
        }

        //update shift information: total_sales, cash_added, cash_removed
        $returnData = $this->updateShiftWhenCreateOrder($orderPaymentCollection, $shiftModel);
        //create new transaction
        if ($returnData['orderPaymentData']) {
            $this->createCashTransactionWhenCreateOrder($returnData['orderPaymentData'], $returnData['shiftData']);
        }

        return $this;
    }

    /**
     * update shift information: total_sales, cash_added, cash_removed
     * @param $orderPaymentCollection
     * @param $shiftId
     * @return array
     */
    public function updateShiftWhenCreateOrder($orderPaymentCollection, $shiftModel)
    {
        $orderPaymentData = null;

        /** @var \Magestore\Webpos\Model\Shift\Shift $shiftModel */
        $cashSale = $shiftModel->getCashSale();
        $balance = $shiftModel->getBalance();
        $totalSales = $shiftModel->getTotalSales();

        foreach ($orderPaymentCollection as $item) {
            $itemData = $item->getData();
            if ($itemData['method'] == 'cashforpos') {
                $cashSale = $cashSale + $itemData['payment_amount'];
                $balance = $balance + $itemData['payment_amount'];
                $orderPaymentData = $itemData;
            }
            $totalSales = $totalSales + $itemData['payment_amount'];
        }

        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCashSale = $this->_webposCurrencyHelper->convertToBase($cashSale, $currentCurrencyCode);
        $baseBalance = $this->_webposCurrencyHelper->convertToBase($balance, $currentCurrencyCode);
        $baseTotalSales = $this->_webposCurrencyHelper->convertToBase($totalSales, $currentCurrencyCode);

        $shiftModel->setCashSale($cashSale);
        $shiftModel->setBaseCashSale($baseCashSale);
        $shiftModel->setBalance($balance);
        $shiftModel->setBaseBalance($baseBalance);
        $shiftModel->setTotalSales($totalSales);
        $shiftModel->setBaseTotalSales($baseTotalSales);
        $staffId = $shiftModel->getStaffId();
        $staffName = $this->staffFactory->create()->load($staffId)->getDisplayName();
        $shiftModel->setData('staff_name', $staffName);
        $shiftModel->save();


        $returnData = [];
        $returnData['orderPaymentData'] = $orderPaymentData;
        $returnData['shiftData'] = $shiftModel->getData();

        return $returnData;
    }

    /**
     * create a transaction on cash_transaction table
     * with type: order
     * value: payment_amount
     * base_value: base_payment_amount
     * @param $orderPaymentData
     * @param $shiftData
     */
    public function createCashTransactionWhenCreateOrder($orderPaymentData, $shiftData)
    {
        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();

        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $data['shift_id'] = $shiftData['shift_id'];
        $data['location_id'] = $shiftData['location_id'];
        $data['staff_name'] = $shiftData['staff_name'];
        $data['staff_id'] = $shiftData['staff_id'];
        $data['value'] = $orderPaymentData['payment_amount'];
        $data['base_value'] = $this->_webposCurrencyHelper->convertToBase($data['value'], $currentCurrencyCode);
        $data['balance'] = $shiftData['balance'];
        $data['base_balance'] = $this->_webposCurrencyHelper->convertToBase($data['balance'], $currentCurrencyCode);
        $data['note'] = "Add cash from order with id=" . $this->_orderIncrementId;
        $data['order_id'] = $orderPaymentData['order_id'];
        $data['type'] = "order";
        $data['base_currency_code'] = $baseCurrencyCode;
        $data['transaction_currency_code'] = $currentCurrencyCode;

        $cashTransactionModel->setData($data);
        $cashTransactionModel->save();
    }


}
