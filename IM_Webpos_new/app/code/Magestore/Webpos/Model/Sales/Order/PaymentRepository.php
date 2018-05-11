<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales\Order;

use Magestore\Webpos\Api\Sales\PaymentRepositoryInterface;
use Magestore\Webpos\Model\Checkout\Data\PaymentItem;
/**
 * Class PaymentRepository
 * @package Magestore\Webpos\Model\Sales\Order
 */
class PaymentRepository implements PaymentRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Model\Payment\OrderPaymentFactory
     */
    protected $_orderPaymentFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * PaymentRepository constructor.
     * @param \Magestore\Webpos\Model\Payment\OrderPaymentFactory $orderPaymentFactory
     * @param \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magestore\Webpos\Model\Payment\OrderPaymentFactory $orderPaymentFactory,
        \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->_orderPaymentFactory = $orderPaymentFactory;
        $this->orderRepository = $orderRepository;
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
    }

    /**
     * Add payment for order
     *
     * @param int $id The invoice ID.
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface WebposOrder interface.
     */
    public function takePayment($id, \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment)
    {
        $order = $this->orderRepository->get($id);
        $additional_information = [];
        if($payment){
            $methodData = $payment->getMethodData();
            foreach ($methodData as $item){
                $orderPayment = $this->_orderPaymentFactory->create();
                $orderPayment->setData([
                    'order_id' => $order->getId(),
                    'real_amount' => $item[PaymentItem::KEY_REAL_AMOUNT],
                    'base_real_amount' => $item[PaymentItem::KEY_BASE_REAL_AMOUNT],
                    'payment_amount' => $item[PaymentItem::KEY_AMOUNT],
                    'base_payment_amount' => $item[PaymentItem::KEY_BASE_AMOUNT],
                    'method' => $item[PaymentItem::KEY_CODE],
                    'method_title' => $item[PaymentItem::KEY_TITLE],
                    'shift_id' => $item[PaymentItem::KEY_SHIFT_ID],
                    'reference_number' => $item[PaymentItem::KEY_REFERENCE_NUMBER],
                    'card_type' => $item[PaymentItem::KEY_CARD_TYPE]
                ]);
                $baseTotalPaid = $order->getBaseTotalPaid() + $item[PaymentItem::KEY_BASE_AMOUNT];
                $totalPaid = $order->getTotalPaid() + $item[PaymentItem::KEY_AMOUNT];
                $order->setBaseTotalPaid($baseTotalPaid);
                $order->setTotalPaid($totalPaid);
                $additional_information[] = $item->getAmount().' : '.$item->getTitle();
                try {
                    $orderPayment->save();
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
            
        }
        try {
            $order->getPayment()
                //->setData($payment[PaymentItem::KEY_CODE].'_ref_no',$payment[PaymentItem::KEY_REAL_AMOUNT])
                ->setData('additional_information',$additional_information)
                ->setData('method','multipaymentforpos')
                ->save();
            if($order->getBaseTotalPaid()-$order->getBaseGrandTotal()>0){
                $order->setWebposBaseChange($order->getBaseTotalPaid()-$order->getBaseGrandTotal());
                $order->setWebposChange($order->getTotalPaid()-$order->getGrandTotal());
            }
            $order->save();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
        return $this->orderRepository->get($id);
    }

}
