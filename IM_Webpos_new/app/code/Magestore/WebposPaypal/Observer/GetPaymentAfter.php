<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaypal\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposPaypal\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $paypalHelper = \Magento\Framework\App\ObjectManager::getInstance()
                         ->create('Magestore\WebposPaypal\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isPaypalEnable = $paypalHelper->isEnablePaypal();
        if($isPaypalEnable) {
            $paypalPayment = $this->addWebposPaypal();
            $paymentList[] = $paypalPayment->getData();
        }
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Payment');

        // add payflowpro to webpos
        $corePaymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magento\Payment\Helper\Data');
        $payflowproIntegrationPayment = $corePaymentHelper->getMethodInstance('payflowpro_integration');
        if($payflowproIntegrationPayment->isActiveWebpos() && $paymentHelper->isAllowOnWebPOS('payflowpro_integration')) {
            $payflowproPayment = $this->addPayflowproIntegration();
            $paymentList[] = $payflowproPayment->getData();
        }

        if(!$paymentHelper->isRetailerPos()) {
            $payments->setList($paymentList);
            return $this;
        }
        $isAllowPaypalHere = $paypalHelper->isAllowPaypalHere();
        if($isAllowPaypalHere) {
            $paypalPayment = $this->addWebposPaypalHere();
            $paymentList[] = $paypalPayment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addPayflowproIntegration()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Payment');
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('payment/payflowpro/is_sandbox');
        $clientId = $helper->getStoreConfig('payment/payflowpro/client_id');
        $isDefault = ('payflowpro_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Payment\Payment');
        $code = 'payflowpro_integration';
        $title = _('Paypal Pro Credit Card');
        $ccTypes = 1;
        $useCvv = 1;
        $paymentModel->setCode($code);
        $paymentModel->setIconClass($code);
        $paymentModel->setTitle($title);
        $paymentModel->setInformation('');
        $paymentModel->setType($ccTypes);
        $paymentModel->setTypeId($ccTypes);
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setClientId($clientId);
        $paymentModel->setIsSandbox($isSandbox);
        $paymentModel->setUsecvv($useCvv);
        return $paymentModel;
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposPaypal()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Helper\Payment');
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
                        ->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('webpos/payment/paypal/is_sandbox');
        $clientId = $helper->getStoreConfig('webpos/payment/paypal/client_id');
        $isDefault = ('paypal_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('paypal_integration');
        $paymentModel->setIconClass('paypal_integration');
        $paymentModel->setTitle(_('Web POS - Paypal Integration'));
        $paymentModel->setInformation('');
        $paymentModel->setType('2');
        $paymentModel->setTypeId('2');
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setClientId($clientId);
        $paymentModel->setIsSandbox($isSandbox);
        return $paymentModel;
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposPaypalHere()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Payment');
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('webpos/payment/paypal/is_sandbox');
        $clientId = $helper->getStoreConfig('webpos/payment/paypal/client_id');
        $isDefault = ('paypal_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('paypal_here');
        $paymentModel->setIconClass('paypal_here');
        $paymentModel->setTitle(_('Web POS - Paypal Here'));
        $paymentModel->setInformation('');
        $paymentModel->setType('2');
        $paymentModel->setTypeId('2');
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(1);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setClientId($clientId);
        $paymentModel->setIsSandbox($isSandbox);
        $accessToken = $helper->getStoreConfig('webpos/payment/paypal/access_token');
        if($accessToken) {
            $paymentModel->setAccessToken($accessToken);
        }
        return $paymentModel;
    }
}