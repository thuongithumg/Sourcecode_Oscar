<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales\Order;

use Magento\Sales\Model\ResourceModel\Metadata as Metadata;
use Magento\Sales\Api\Data\ShipmentSearchResultInterfaceFactory as SearchResultFactory;

class ShipmentRepository extends \Magento\Sales\Model\Order\ShipmentRepository
    implements \Magestore\Webpos\Api\Sales\ShipmentRepositoryInterface
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;/**
 *
 * @var \Magento\Framework\DB\Transaction
 */
    protected $dbTransaction;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * ShipmentRepository constructor.
     * @param Metadata $metadata
     * @param SearchResultFactory $searchResultFactory
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Framework\DB\TransactionFactory $dbTransaction
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Metadata $metadata,
        SearchResultFactory $searchResultFactory,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Framework\DB\TransactionFactory $dbTransaction,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository
    ){
        parent::__construct($metadata, $searchResultFactory);
        $this->shipmentLoader = $shipmentLoader;
        $this->dbTransaction = $dbTransaction;
        $this->shipmentSender = $shipmentSender;
        $this->orderRepository = $orderRepository;
    }


    /**
     * Performs persist operations for a specified shipment.
     *
     * @param \Magento\Sales\Api\Data\ShipmentInterface $entity
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws CouldNotSaveException
     */
    public function saveShipment(\Magento\Sales\Api\Data\ShipmentInterface $entity){
        $data = $this->_prepareShipment($entity);
        $this->shipmentLoader->setOrderId($data['order_id']);
        $this->shipmentLoader->setShipment($data['shipment']);
        if(isset($data['tracking']) && is_array($data['tracking'])) {
            foreach ($data['tracking'] as $key => $item) {
                $data['tracking'][$key]['title'] = 'custom';
            }
        }
        $this->shipmentLoader->setTracking($data['tracking']);
        $shipment = $this->shipmentLoader->load();
        if (!empty($data['shipment']['comment_text'])) {
            $shipment->addComment(
                $data['shipment']['comment_text'],
                isset($data['shipment']['comment_customer_notify']),
                isset($data['shipment']['is_visible_on_front'])
            );

            $shipment->setCustomerNote($data['shipment']['comment_text']);
            $shipment->setCustomerNoteNotify(isset($data['shipment']['comment_customer_notify']));
        }
        $shipment->register();
        $shipment->getOrder()->setCustomerNoteNotify(!empty($data['shipment']['send_email']));
        $this->_saveShipment($shipment);
        if (!empty($data['shipment']['send_email'])) {
            $this->shipmentSender->send($shipment);
        }
        return $this->orderRepository->get($data['order_id']);
    }

    /**
     * Save shipment and order in one transaction
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $this->dbTransaction->create()->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }

    protected function _prepareShipment(\Magento\Sales\Api\Data\ShipmentInterface $entity){
        $data = [];
        $items = $entity->getItems();
        $orderId = $entity->getOrderId();
        if(count($items>0) && $orderId){
            $data['order_id'] = $orderId;
            $tracks = $entity->getTracks();
            $data['tracking'] = null;
            if(count($tracks) && $track = $tracks[0]){
                $trackData = [];
                $trackData['carrier_code'] = 'custom';
                $trackData['number'] = $track->getTrackNumber();
                $data['tracking'][] = $trackData;
            }
            $shipment = [];
            foreach ($items as $item){
                $shipment['items'][$item->getOrderItemId()] = $item->getQty();
            }
            $shipment['send_email'] = $entity->getEmailSent();
            $comments = $entity->getComments();
            if(count($comments) && $comment = $comments[0]){
                $shipment['comment_text'] = $comment->getComment();
                if($shipment['send_email'])
                    $shipment['comment_customer_notify'] = 1;
            }
            $data['shipment'] = $shipment;
            return $data;
        }
        return null;
    }
}