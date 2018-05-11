<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposBambora\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposBambora\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $bamboraHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\WebposBambora\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isStripeEnable = $bamboraHelper->isEnableBambora();
        if($isStripeEnable) {
            $bamboraPayment = $this->addWebposBambora();
            $paymentList[] = $bamboraPayment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposBambora()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Helper\Payment');

        $isDefault = ('bambora_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('bambora_integration');
        $paymentModel->setIconClass('bambora_integration');
        $paymentModel->setTitle(_('Bambora'));
        $paymentModel->setInformation('');
        $paymentModel->setType('0');
        $paymentModel->setTypeId('0');
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(1);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        return $paymentModel;
    }
}