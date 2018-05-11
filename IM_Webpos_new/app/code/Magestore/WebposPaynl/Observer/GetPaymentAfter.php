<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposPaynl\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    const PAYMENT_TITLE = 'Pinnen';
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $paynlHelper = \Magento\Framework\App\ObjectManager::getInstance()
                         ->create('Magestore\WebposPaynl\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isPaynlEnable = $paynlHelper->isEnablePaynl();
        if($isPaynlEnable) {
            $paynlPayment = $this->addWebposPaynl();
            $paymentList[] = $paynlPayment->getData();
        }
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Payment');
        if(!$paymentHelper->isRetailerPos()) {
            $payments->setList($paymentList);
            return $this;
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposPaynl()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Helper\Payment');
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
                        ->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('webpos/payment/paynl/is_sandbox');
        $clientId = $helper->getStoreConfig('webpos/payment/paynl/client_id');
        $isDefault = ('paynl_payment_instore' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('paynl_payment_instore');
        $paymentModel->setIconClass('paynl_payment_instore');
        $paymentModel->setTitle(_(self::PAYMENT_TITLE));
        $paymentModel->setInformation('');
        $paymentModel->setType(2);
        $paymentModel->setTypeId(2);
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setClientId($clientId);
        $paymentModel->setIsSandbox($isSandbox);
        return $paymentModel;
    }

}