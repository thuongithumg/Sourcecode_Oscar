<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Framework\Exception\LocalizedException;
/**
 * class \Magestore\Webpos\Helper\Order
 * 
 * Web POS Order helper
 * Methods:
 *  _getItemQtys
 *  _prepareShipment
 *  createShipmentAndInvoice
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Order extends \Magestore\Webpos\Helper\Data
{
    /**
     * @var OrderSender
     */
    protected $_orderSender;
    
    /**
     * @var InvoiceSender
     */
    protected $_invoiceSender;

    /**
     * @var ShipmentSender
     */
    protected $_shipmentSender;

    /**
     * @var ShipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var InvoiceService
     */
    protected $_invoiceService;
    
    /**
     *
     * @var TrackFactory 
     */
    protected $_trackFactory;
    
    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Model\WebPosSession $webPosSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param Registry $registry
     * @param OrderSender $orderSender
     * @param InvoiceSender $invoiceSender
     * @param ShipmentSender $shipmentSender
     * @param ShipmentFactory $shipmentFactory
     * @param InvoiceService $invoiceService
     * @param TrackFactory $trackFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Model\WebPosSession $webPosSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        Registry $registry,
        OrderSender $orderSender,
        InvoiceSender $invoiceSender,
        ShipmentSender $shipmentSender,
        ShipmentFactory $shipmentFactory,
        InvoiceService $invoiceService,
        TrackFactory $trackFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    ) {
        $this->_registry = $registry;
        $this->_orderSender = $orderSender;
        $this->_invoiceSender = $invoiceSender;
        $this->_shipmentSender = $shipmentSender;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_invoiceService = $invoiceService;
        $this->_trackFactory = $trackFactory;
        parent::__construct($context,$productMetadata,$storeManager,$webPosSession,$checkoutSession, $customerSession,$localeDate,$dateTime,$storeRepository);
    }
    
    /**
     * 
     * @param int $orderId
     * @param \Magento\Sales\Model\Order $order
     * @param boolean $create_invoice
     * @param boolean $create_shipment
     * @param array $items_to_ship
     * @param \Magestore\Webpos\Model\Checkout\Data\ShippingTrack[] $tracks
     */
    public function createShipmentAndInvoice($orderId, $order, $create_invoice, $create_shipment, $items_to_ship = [], $tracks = []) {
        $invoice_error = $shipment_error = false;
        $invoice_message = $shipment_message = '';
        if (!$order->getId()) {
            $order = $this->getModel('Magento\Sales\Model\Order')->load($orderId);
        }
        if ($order->getId()) {
            if ($create_invoice) {
                try {
                    $invoiceItems = $this->_getItemQtys($order);
                    if ($order->canInvoice()) {
                        $invoice = $this->_invoiceService->prepareInvoice($order, $invoiceItems);
                        if ($invoice) {
                            if ($invoice->getTotalQty()) {
                                $this->_registry->register('current_invoice', $invoice);
                                $invoice->register();
                                $invoice->getOrder()->setCustomerNoteNotify(true);
                                $invoice->getOrder()->setIsInProcess(true);
                                $transactionSave = $this->getModel('Magento\Framework\DB\Transaction')
                                    ->addObject(
                                        $invoice
                                    )->addObject(
                                        $invoice->getOrder()
                                    );
                                $transactionSave->save();
                                if ($this->isEnableAutoSendEmail('invoice')) {
                                    $this->_invoiceSender->send($invoice);
                                }

                                $invoice_error = false;
                                $invoice_message = __('The invoice has been created.');
                            }
                        }else {
                            $invoice_error = true;
                            $invoice_message = __('The invoice is not exist');
                        }
                    }
                }catch (LocalizedException $e) {
                    $invoice_error = true;
                    $invoice_message = __('Unable to save the invoice.');
                }
            }

            if ($create_shipment) {
                try {
                    if(!count($items_to_ship)) {
                        $items_to_ship = $this->prepareItemShipment($order);
                    }
                    $shipment = $this->_prepareShipment($order, $items_to_ship);
                    if ($shipment) {
                        $shipment->setEmailSent(true);
                        $shipment->getOrder()->setCustomerNoteNotify(true);
                        if (count($tracks) > 0) {
                            foreach ($tracks as $track){
                                $shipment->addTrack(
                                    $this->_trackFactory->create()
                                        ->setNumber($track->getNumber())
                                        ->setCarrierCode($track->getCarrierCode())
                                        ->setTitle($track->getTitle())
                                );
                            }
                        }
                        $shipment->getOrder()->setIsInProcess(true);
                        $transaction = $this->getModel('Magento\Framework\DB\Transaction');
                        $transaction->addObject($shipment)
                            ->addObject($shipment->getOrder())
                            ->save();
                        
                        if ($this->isEnableAutoSendEmail('shipment')) {
                            $this->_shipmentSender->send($shipment);
                        }
                        $shipment_error = false;
                        $shipment_message = __('The shipment has been created.');
                    } else {
                        $shipment_error = true;
                        $shipment_message = __('An error occurred while creating shipment.');
                    }
                }catch (LocalizedException $e) {
                    $shipment_error = true;
                    $shipment_message = $e->getMessage();
                }
            }
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function prepareItemShipment($order) {
        $data = [];
        foreach ($order->getAllItems() as $item) {
            $data[$item->getId()] = $item->getQtyToShip();
        }
        return $data;
    }
    
    /**
     * 
     * @param \Magento\Sales\Model\Order $order
     * @param array $items
     * @param array $tracking
     * @return shipment object
     */
    protected function _prepareShipment($order, $items = array(),$tracking = null)
    {
        $shipment = $this->_shipmentFactory->create(
            $order,
            $items,
            $tracking
        );
        if (!$shipment->getTotalQty()) {
            return false;
        }
        return $shipment->register();
    }
    
    /**
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function _getItemQtys($order) {
        $savedQtys = array();
        $_order_items = $order->getAllItems();
        foreach ($_order_items as $_order_item) {
            $savedQtys[$_order_item->getId()] = $_order_item->getQtyOrdered();
        }
        return $savedQtys;
    }
    
    /**
     * 
     * @param string $type
     * @return string
     */
    public function isEnableAutoSendEmail($type) {
        $config = false;
        switch ($type) {
            case 'order':
                $config = $this->scopeConfig->getValue('webpos/email_configuration/auto_email_orders', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case 'invoice':
                $config = $this->scopeConfig->getValue('webpos/email_configuration/auto_email_invoice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case 'shipment':
                $config = $this->scopeConfig->getValue('webpos/email_configuration/auto_email_shipment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case 'creditmemo':
                $config = $this->scopeConfig->getValue('webpos/email_configuration/auto_email_creditmemo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
        }
        return $config;
    }
}