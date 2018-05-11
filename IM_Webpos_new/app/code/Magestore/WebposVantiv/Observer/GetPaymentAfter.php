<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposVantiv\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposVantiv\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $vantivHelper = \Magento\Framework\App\ObjectManager::getInstance()
                         ->create('Magestore\WebposVantiv\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isVantivEnable = $vantivHelper->isEnableVantiv();
        if($isVantivEnable) {
            $vantivPayment = $this->addWebposVantiv();
            $paymentList[] = $vantivPayment->getData();
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
    public function addWebposVantiv()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Payment');
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('webpos/payment/vantiv/is_sandbox');
        $accountId = $helper->getStoreConfig('webpos/payment/vantiv/account_id');
        $applicationId = $helper->getStoreConfig('webpos/payment/vantiv/application_id');
        $acceptorId = $helper->getStoreConfig('webpos/payment/vantiv/acceptor_id');
        $isDefault = ('vantiv_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('vantiv_integration');
        $paymentModel->setIconClass('vantiv_integration');
        $paymentModel->setTitle(_('Web POS - Vantiv'));
        $paymentModel->setInformation('');
        $paymentModel->setType('2');
        $paymentModel->setTypeId('2');
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(1);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setAccountId($accountId);
        $paymentModel->setApplicationId($applicationId);
        $paymentModel->setAcceptorId($acceptorId);
        $paymentModel->setIsSandbox($isSandbox);
        $accessToken = $helper->getStoreConfig('webpos/payment/vantiv/account_token');
        if($accessToken) {
            $paymentModel->setAccessToken($accessToken);
        }
        return $paymentModel;
    }
}