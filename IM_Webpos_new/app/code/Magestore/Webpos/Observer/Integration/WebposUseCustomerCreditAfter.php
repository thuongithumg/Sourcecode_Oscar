<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Integration;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;



class WebposUseCustomerCreditAfter implements ObserverInterface
{

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
            if (!$this->_moduleManager->isEnabled('Magestore_Customercredit')) {
                return $this;
            }
            $order = $observer->getEvent()->getOrder();
            $data = $observer->getEvent()->getExtensionData();
            if(isset($order) && $order->getId() && !empty($data) && isset($data['base_customercredit_discount'])){
                $amount = $data['base_customercredit_discount'];
                $customerId = $order->getCustomerId();
                $transaction = $this->_objectManager->create('\Magestore\Customercredit\Model\Transaction');
                $customercredit = $this->_objectManager->create('\Magestore\Customercredit\Model\Customercredit');
                if ($transaction && $customercredit && !empty($amount)) {
                    $transaction->addTransactionHistory($customerId, \Magestore\Customercredit\Model\TransactionType::TYPE_CHECK_OUT_BY_CREDIT, __('check out by credit for order #') . $order->getIncrementId(), $order->getId(), -$amount);
                    $customercredit->changeCustomerCredit(-$amount, $customerId);
                }
            }
        }catch(\Exception $e){
            $this->helper->addLog($e->getMessage());
        }
    }
}