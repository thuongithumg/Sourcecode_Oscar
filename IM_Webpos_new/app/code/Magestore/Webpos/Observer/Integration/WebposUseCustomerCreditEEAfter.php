<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Integration;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class WebposUseCustomerCreditEEAfter implements ObserverInterface
{

    const ACTION_REFUNDED = 4;
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * WebposUseCustomerCreditAfter constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Webpos\Helper\Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        try{
            $creditmemo = $observer->getData('creditmeno');
            if ($creditmemo) {
                $order = $creditmemo->getOrder();
                if ($order->getCustomerBalanceAmount()) {
                    $customer = $this->_objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($order->getCustomerId());
                    $balance = $this->_objectManager->create('\Magento\CustomerBalance\Model\BalanceFactory')->create()->getCollection()
                        ->addFieldToFilter('customer_id', $order->getCustomerId())->getFirstItem();
                    $balanceData = $this->_objectManager->create('\Magento\CustomerBalance\Model\BalanceFactory')->create();
                    $balance->setAmount($balance->getAmount() + $order->getCustomerBalanceAmount())->save();
                    $balanceData->setBalanceId($balance->getId())
                        ->setAmountDelta($order->getCustomerBalanceAmount())
                        ->setHistoryAction(self::ACTION_REFUNDED)
                        ->setOrder($order)
                        ->setCustomer($customer)
                        ->setCreditMemo($creditmemo)->loadByCustomer();
                    $this->_objectManager->create('\Magento\CustomerBalance\Model\Balance\HistoryFactory')
                        ->create()->setBalanceModel($balanceData)->save();
                }
            }
        }catch(\Exception $e){
            $this->helper->addLog($e->getMessage());
        }
    }
}