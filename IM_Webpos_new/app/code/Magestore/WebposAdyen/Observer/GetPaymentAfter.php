<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAdyen\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposAdyen\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $adyenHelper = \Magento\Framework\App\ObjectManager::getInstance()
                         ->create('Magestore\WebposAdyen\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isAdyenEnable = $adyenHelper->isEnableAdyen();
        if($isAdyenEnable) {
            $adyenPayment = $this->addWebposAdyen();
            $paymentList[] = $adyenPayment->getData();
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
    public function addWebposAdyen()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Payment');
        $isDefault = ('adyen_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('adyen_integration');
        $paymentModel->setIconClass('adyen_integration');
        $paymentModel->setTitle(_('Web POS - Adyen'));
        $paymentModel->setInformation('');
        $paymentModel->setType('2');
        $paymentModel->setTypeId('2');
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(1);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        return $paymentModel;
    }
}