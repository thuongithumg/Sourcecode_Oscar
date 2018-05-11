<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;



class SalesOrderInvoicePay implements ObserverInterface
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
     *
     * @var \Magestore\Webpos\Model\Payment\OrderPaymentFactory 
     */
    protected $_orderPaymentFactory;
    
    /**
     * 
     * @param \Magento\Framework\Message\ManagerInterface 
     */
    protected $_messageManager;
    
    /**
     *
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $_permissionHelper;
    
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;    


    public function __construct(  
            \Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Framework\Module\Manager $moduleManager,
            \Magestore\Webpos\Model\Payment\OrderPaymentFactory $orderPaymentFactory,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magestore\Webpos\Helper\Permission $permissionHelper,
            \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
        $this->_orderPaymentFactory = $orderPaymentFactory;
        $this->_messageManager = $messageManager;
        $this->_permissionHelper = $permissionHelper;
        $this->_coreRegistry = $coreRegistry;
    }
    
    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        /* capture online payment */
        if($invoice->getRequestedCaptureCase() == Invoice::CAPTURE_ONLINE) {
            return $this;
        }
        
        $order = $invoice->getOrder();
        
        /* not WebPOS order */
        if(!$order->getWebposOrderId()) {
            if(!$this->_isPaidByWebPOSPayment($order)) {
                return $this;
            }
        }
        
        /* Paid all */
        if($order->getBaseTotalPaid() == $order->getBaseGrandTotal() 
                /* submit invoice from WebPOS */
                && $this->_coreRegistry->registry('currrent_webpos_staff')) {
            return $this; 
        }

        if($order->getPayment()->getMethod() == 'authorizenet_directpost') {
            return $this;
        }

        /* reset total_paid & base_total_paid in order */
        $order->setTotalPaid($order->getTotalPaid() - $invoice->getGrandTotal());
        $order->setBaseTotalPaid($order->getBaseTotalPaid() - $invoice->getBaseGrandTotal());



        /* calculate rewards and giftcard discount */
        $orderItems = $order->getAllItems();
        $itemsDiscount = [];
        if(count($orderItems) > 0){
            foreach ($orderItems as $item){
                $itemId = $item->getId();
                $itemsDiscount[$itemId]['point']['base'] = $item->getData('rewardpoints_base_discount');
                $itemsDiscount[$itemId]['point']['amount'] = $item->getData('rewardpoints_discount');
                $itemsDiscount[$itemId]['voucher']['base'] = $item->getData('base_gift_voucher_discount');
                $itemsDiscount[$itemId]['voucher']['amount'] = $item->getData('gift_voucher_discount');
            }
            $invoiceItems = $invoice->getAllItems();
            $totalPointDiscount = $baseTotalPointDiscount = 0;
            $totalVoucherDiscount = $baseTotalVoucherDiscount = 0;
            $totalDiscountAmount = $baseTotalDiscountAmount = 0;
            foreach ($invoiceItems as $item){
                $itemId = $item->getData('order_item_id');
                if(isset($itemsDiscount[$itemId])){
                    $totalPointDiscount += $itemsDiscount[$itemId]['point']['amount'];
                    $baseTotalPointDiscount += $itemsDiscount[$itemId]['point']['base'];
                    $totalVoucherDiscount += $itemsDiscount[$itemId]['voucher']['amount'];
                    $baseTotalVoucherDiscount += $itemsDiscount[$itemId]['voucher']['base'];
                    $totalDiscountAmount += $totalPointDiscount;
                    $totalDiscountAmount += $totalVoucherDiscount;
                    $baseTotalDiscountAmount += $baseTotalPointDiscount;
                    $baseTotalDiscountAmount += $baseTotalVoucherDiscount;
                }
            }
            $invoice->setData('rewardpoints_discount',$totalPointDiscount);
            $invoice->setData('rewardpoints_base_discount',$baseTotalPointDiscount);

            $invoice->setData('gift_voucher_discount',$totalVoucherDiscount);
            $invoice->setData('base_gift_voucher_discount',$baseTotalVoucherDiscount);

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);

        }

        /* valid total_paid & total_invoice */
        $baseTotalInvoiced = $order->getBaseTotalInvoiced()  ? $order->getBaseTotalInvoiced() : 0;
        $baseGrandTotalInvoice = $invoice->getBaseGrandTotal() >= 0.0001 ?  $invoice->getBaseGrandTotal() : 0;
        
        if(round($order->getBaseTotalPaid(), 2) < round($baseTotalInvoiced + $baseGrandTotalInvoice, 2)) {
            //$this->_messageManager->addWarning(__('You must take payment on this order from WebPOS to create invoice for more items.'));
            //throw new \Exception(__('You must take payment on this order from WebPOS to create invoice for more items.'));
        }
    }    
    
    /**
     * Check order is paid from WebPOS
     * 
     * @param \Magento_Sales_Model_Order $order
     * @return bool
     */
    protected function _isPaidByWebPOSPayment($order)
    {
        $orderPayment = $this->_orderPaymentFactory->create();
        $posPayments = $orderPayment->getCollection()->addFieldToFilter('order_id', $order->getId());
        if($posPayments->getSize()) {
            return true;
        }
        return false;
    }
}