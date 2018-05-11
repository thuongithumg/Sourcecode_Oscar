<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * API2 for sales_order
 *
 * @author     Magestore Core Team
 */
class Magestore_Webpos_Model_Api2_Order_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_GET_ORDER_LIST = 'get';

    /**
     *
     */
    const OPERATION_GET_ORDER_TAKEPAYMENT = 'save';

    /**
     *
     */
    const OPERATION_GET_ORDER_CANCEL = 'cancel';

    /**
     *
     */
    const OPERATION_GET_ORDER_COMMENT = 'comment';

    /**
     *
     */
    const OPERATION_GET_ORDER_EMAIL = 'email';

    /**
     *
     */
    const OPERATION_GET_ORDER_IS_WAIT_PAYNL_RESPONSE = 'isWaitPayNlResponse';

    const TYPE_ADD = 'add';
    const TYPE_ORDER = 'order';

    /* @var Magestore_Webpos_Helper_Product $productHelper */
    private $productHelper;

    /* @var Magestore_Webpos_Helper_Payment $paymentHelper */
    private $paymentHelper;

    /* @var Magestore_Webpos_Helper_Order $orderHelper */
    private $orderHelper;

    /**
     * Magestore_Webpos_Model_Api2_Order_Rest_Admin_V1 constructor.
     */
    public function __construct()
    {
        $this->productHelper = Mage::helper('webpos/product');
        $this->paymentHelper = Mage::helper('webpos/payment');
        $this->orderHelper   = Mage::helper('webpos/order');
    }

    /**
     * @return array
     */
    protected function getListOrder()
    {
        $helper = Mage::helper('webpos/order');
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_collection');
        $webpossession = Mage::getModel('webpos/user_webpossession');

        if ($storeId = $webpossession->getData('current_store_id')) {
            $collection->addFieldToSelect('store_id', $storeId);
        }

        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if ($pageSize) {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }

        $orderField = $this->getRequest()->getOrderField();
        if (null !== $orderField) {
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $this->_applyFilterOrder($collection);
        $this->_applyFilterTo($collection);
        $numberOfOrder = $collection->getSize();
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        $ordersData = array();
        $n = 0;
        // Get nesscessary information of order
        foreach ($collection as $order) {
            $i = 0;
            $orderedItems = $order->getAllVisibleItems();
            $orderedProductIds = array();
            foreach ($orderedItems as $item) {
                $orderedProductIds[$i]['item_id'] = $item->getData('item_id');
                $orderedProductIds[$i]['name'] = $item->getData('name');
                $orderedProductIds[$i]['created_at'] = $item->getData('created_at');
                $orderedProductIds[$i]['amount_refunded'] = (float)$item->getData('amount_refunded');
                $orderedProductIds[$i]['base_amount_refunded'] = (float)$item->getData('base_amount_refunded');
                $orderedProductIds[$i]['base_discount_amount'] = (float)$item->getData('base_discount_amount');
                $orderedProductIds[$i]['base_gift_voucher_discount'] = (float)$item->getData('base_gift_voucher_discount');
                $orderedProductIds[$i]['gift_voucher_discount'] = (float)$item->getData('gift_voucher_discount');
                $orderedProductIds[$i]['discount_amount'] = (float)$item->getData('discount_amount');
                $orderedProductIds[$i]['base_discount_invoiced'] = (float)$item->getData('base_discount_invoiced');
                $orderedProductIds[$i]['base_price'] = (float)$item->getData('base_price');
                $orderedProductIds[$i]['base_price_incl_tax'] = (float)$item->getData('base_price_incl_tax');
                $orderedProductIds[$i]['base_row_invoiced'] = (float)$item->getData('base_row_invoiced');
                $orderedProductIds[$i]['base_row_total'] = (float)$item->getData('base_row_total');
                $orderedProductIds[$i]['base_row_total_incl_tax'] = (float)$item->getData('base_row_total_incl_tax');
                $orderedProductIds[$i]['base_tax_amount'] = (float)$item->getData('base_tax_amount');
                $orderedProductIds[$i]['tax_amount'] = (float)$item->getData('tax_amount');
                $orderedProductIds[$i]['base_tax_invoiced'] = (float)$item->getData('base_tax_invoiced');
                $orderedProductIds[$i]['discount_invoiced'] = (float)$item->getData('discount_invoiced');
                $orderedProductIds[$i]['discount_percent'] = (float)$item->getData('discount_percent');
                $orderedProductIds[$i]['discount_invoiced'] = (float)$item->getData('discount_invoiced');
                $orderedProductIds[$i]['rewardpoints_base_discount'] = (float)$item->getData('rewardpoints_base_discount');
                $orderedProductIds[$i]['free_shipping'] = $item->getData('free_shipping');
                $orderedProductIds[$i]['is_qty_decimal'] = $item->getData('is_qty_decimal');
                $orderedProductIds[$i]['is_virtual'] = $item->getData('is_virtual');
                $orderedProductIds[$i]['original_price'] = (float)$item->getData('original_price');
                $orderedProductIds[$i]['base_original_price'] = (float)$item->getData('base_original_price');
                $orderedProductIds[$i]['price'] = (float)$item->getData('price');
                $orderedProductIds[$i]['price_incl_tax'] = (float)$item->getData('price_incl_tax');
                $orderedProductIds[$i]['product_id'] = $item->getData('product_id');
                $orderedProductIds[$i]['product_type'] = $item->getData('product_type');
                $orderedProductIds[$i]['qty_canceled'] = (float)$item->getData('qty_canceled');
                $orderedProductIds[$i]['qty_invoiced'] = (float)$item->getData('qty_invoiced');
                $orderedProductIds[$i]['qty_ordered'] = (float)$item->getData('qty_ordered');
                $orderedProductIds[$i]['qty_refunded'] = (float)$item->getData('qty_refunded');
                $orderedProductIds[$i]['qty_shipped'] = (float)$item->getData('qty_shipped');
                $orderedProductIds[$i]['quote_item_id'] = $item->getData('quote_item_id');
                $orderedProductIds[$i]['row_invoiced'] = $item->getData('row_invoiced');
                $orderedProductIds[$i]['row_total'] = (float)$item->getData('row_total');
                $orderedProductIds[$i]['row_total_incl_tax'] = (float)$item->getData('row_total_incl_tax');
                $orderedProductIds[$i]['row_weight'] = $item->getData('row_weight');
                $orderedProductIds[$i]['sku'] = $item->getData('sku');
                $orderedProductIds[$i]['store_id'] = $item->getData('store_id');
                $orderedProductIds[$i]['tax_invoiced'] = (float)$item->getData('tax_invoiced');
                $orderedProductIds[$i]['tax_percent'] =(float) $item->getData('tax_percent');
                $orderedProductIds[$i]['updated_at'] = $item->getData('updated_at');
                $orderedProductIds[$i]['order_id'] = $order->getId();

                if ($item->getProductType() == 'giftvoucher') {
                    $orderedProductIds[$i]['giftvoucher_info'] = $this->getProductHelper()
                        ->getGiftVoucherInfoFromOrderItem($item);
                }
                $orderedProductIds[$i]['ordered_warehouse_id'] =  (int) $item->getData('ordered_warehouse_id');
                $i++;
            }
            $billingAddress = $order->getBillingAddress();
            $shippingAddress = $order->getShippingAddress();
            $payment = $helper->getPayment($order);
            $itemInfoBuy = $helper->getItemsInfoBuy($order);
            $commentsHistory = $order->getStatusHistoryCollection()->addAttributeToSort('created_at', 'DESC');
            $comments = array();
            $j = 0;
            foreach ($commentsHistory as $comment) {
                $comments[$j]['comment'] = $comment->getComment();
                $comments[$j]['created_at'] = $comment->getCreatedAt();
                $j++;
            }
            $ordersData[$n] = $helper->getOrderData($order);
            $ordersData[$n]['items'] = $orderedProductIds;
            $ordersData[$n]['status_histories'] = $comments;    // Comments history
            $ordersData[$n]['items_info_buy']['items'] = $itemInfoBuy;  // Info items to reorder
            $ordersData[$n]['billing_address'] = $billingAddress->getData();
            $ordersData[$n]['payment'] = $payment;
            // Shipping address - output rest api
            if ($shippingAddress)
                $ordersData[$n]['extension_attributes']['shipping_assignments'][]['shipping']['address'] = $shippingAddress->getData();
            $n++;
        }

        if ($pageNumber <= ($numberOfOrder/$pageSize+1)) {
            $result['items'] = $ordersData;
            $result['total_count'] = $numberOfOrder;
        } else {
            $result = array(
                'items' => array(),

            );
        }
        return $result;
    }

    /**
     * @param array $params
     * @return object
     */
    public function takePayment($params) {
        $result = array();
        $order_id = $params['order_id'];
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($order_id);
        if(isset($params['payment'])) {
            $payment = $params['payment'];
            $methodData = $payment['method_data'];
            foreach ($methodData as $item){
                if ( $order->getTotalPaid() >= $order->getGrandTotal() ) {
                    break;
                }

                $orderPayment = Mage::getModel('webpos/payment_orderPayment');
                $code = $item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::CODE];
                $amount = $item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::REAL_AMOUNT];
                $baseAmount = $item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::BASE_REAL_AMOUNT];

                if ($this->paymentHelper->isPayNlPayment($item['code'])) {
                    $eventData = array(
                        'method_data' => $item,
                        'method' => $code,
                        'amount' => $amount,
                        'base_amount' => $baseAmount,
                        'order' => $order
                    );

                    Mage::dispatchEvent(Magestore_Webpos_Model_Payment_Method_Multipayment::EVENT_WEBPOS_START_PROCESS_PAYMENT, $eventData);
                }

                $orderPayment->setData(array(
                    'order_id' => $order->getId(),
                    'real_amount' => $item['real_amount'],
                    'base_real_amount' => $item['base_real_amount'],
                    'payment_amount' => $amount,
                    'base_payment_amount' => $baseAmount,
                    'method' => $code,
                    'method_title' => $item['title'],
                    'shift_id' => $item['shift_id'],
                    'reference_number' => empty($item['reference_number'])?'':$item['reference_number'],
                    'till_id' => $order->getWebposTillId()
                ));

                if (!$this->paymentHelper->isPayNlPayment($item['code'])) {
                    $order->setBaseTotalPaid(round($order->getBaseTotalPaid() + $baseAmount,2));
                    $order->setTotalPaid(round($order->getTotalPaid() + $amount, 2));
                }

                $additional_information[] = $amount.' : '.$item['title'];
                try {
                    $orderPayment->save();
                    $this->addTransaction($item, $order, $orderPayment);
                } catch (Exception $e) {
                    $result['error'] = $e->getMessage();
                }
            }
        }

        try {
            $order->getPayment()
                ->setData('additional_information',$additional_information)
                ->setData('method','multipaymentforpos')
                ->save();
            $order->save();
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }
        $helper = Mage::helper('webpos/order');
        $result = $helper->getAllOrderInfo($order);
        return $result;
    }

    /**
     * @param object $item
     * @param object $order
     */
    public function addTransaction($item, $order, $orderPayment, $params = array()) {
        $config = Mage::helper('webpos/config');
        if(($item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::CODE] == Magestore_Webpos_Helper_Payment::CASH_PAYMENT_CODE) && $config->isEnableCashDrawer()){
            $transaction = Mage::getModel('webpos/shift_cashtransaction');
            $currentShiftId = Mage::helper('webpos/shift')->getCurrentShiftId();
            $staffId = $order->getData('webpos_staff_id');
            $staffModel = Mage::getModel('webpos/user')->load($staffId);
            if ($staffModel->getId()) {
                $staffName = $staffModel->getDisplayName();
            } else {
                $staffName = '';
            }
            $transaction->setData(array(
                Magestore_Webpos_Api_TransactionInterface::STAFF_ID => $staffId,
                Magestore_Webpos_Api_TransactionInterface::STAFf_NAME => $staffName,
                Magestore_Webpos_Api_TransactionInterface::SHIFT_ID => $currentShiftId,
                Magestore_Webpos_Api_TransactionInterface::ORDER_INCREMENT_ID => $order->getData('increment_id'),
                Magestore_Webpos_Api_TransactionInterface::TRANSACTION_CURRENCY_CODE => $order->getData('order_currency_code'),
                Magestore_Webpos_Api_TransactionInterface::BASE_CURRENCY_CODE => $order->getData('base_currency_code'),
                Magestore_Webpos_Api_TransactionInterface::VALUE => $item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::REAL_AMOUNT],
                Magestore_Webpos_Api_TransactionInterface::BALANCE => $item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::BASE_REAL_AMOUNT],
                Magestore_Webpos_Api_TransactionInterface::BASE_VALUE => $item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::REAL_AMOUNT],
                Magestore_Webpos_Api_TransactionInterface::BASE_BALANCE =>$item[Magestore_Webpos_Api_Checkout_PaymentItemInterface::BASE_REAL_AMOUNT],
                Magestore_Webpos_Api_TransactionInterface::TYPE => self::TYPE_ORDER,
                Magestore_Webpos_Api_TransactionInterface::NOTE => Mage::helper('webpos')->__('Add cash from order with id = %s', $order->getIncrementId())
            ));
            $transaction->save();
        }
        Mage::helper('webpos/shift')->updateShiftWhenCreateOrder($orderPayment, $currentShiftId);
    }

    /**
     * @param array $params
     * @return array
     */
    public function cancelOrder($params) {
        $helper = Mage::helper('webpos/order');
        $result = array();
        try {
            $orderId = '';
            $comment = '';
            if (isset($params['id']))
                $orderId = $params['id'];
            if ($orderId) {
                if (isset($params['comment']) && isset($params['comment']['comment']))
                    $comment = $params['comment']['comment'];
                $orderModel = Mage::getModel('sales/order');
                $orderModel->load($orderId);
                if ($orderModel->canCancel()) {
                    $orderModel->cancel();
                    $orderModel->setStatus('canceled');
                    if ($comment)
                        $orderModel->addStatusHistoryComment($comment, false);
                    $orderModel->save();
                    $result = $helper->getAllOrderInfo($orderModel);
                }
                $result['success'] = true;
            }
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }
       return $result;
    }

    /**
     * @param array $params
     * @return array
     */
    public function sendEmail($params) {
        if (isset($params['email'])) {
            $email = $params['email'];
            $orderId = $params['id'];
            $response = array();
            if ($orderId) {
                $template_order = Mage::helper('webpos/config')->getWebposEmailTemplate('order');
                if (isset($template_order['guest']) && $template_order['guest'] != '') {
                    Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $template_order['guest']);
                }
                if (isset($template_order['customer']) && $template_order['customer'] != '') {
                    Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $template_order['customer']);
                }
                $order = Mage::getModel('sales/order')->load($orderId);
                $order->setEmailSent(false);
                if($email) {
                    $order->setCustomerEmail($email);
                }
                $order->sendNewOrderEmail();
                if ($order && $order->getId()) {
                    $error = false;
                    $message = Mage::helper('adminhtml')->__('The order #%s has been sent to the customer %s',
                        $order->getIncrementId(), $order->getCustomerEmail());
                } else {
                    $error = true;
                    $message = Mage::helper('adminhtml')->__('The order #%s cannot be sent to the customer %s',
                        $order->getIncrementId(), $order->getCustomerEmail());
                }

            } else {
                $error = true;
                $message = Mage::helper('adminhtml')->__('Cannot send the order');
            }
            $response['error'] = $error;
        } else {
            $message = Mage::helper('adminhtml')->__('Please enter email address!');
        }
        $response['message'] = $message;
        return $response;
    }

    /**
     * @param array $params
     * @return array
     */
    public function addComment($params) {
        if (isset($params['id'])) {
            $helper = Mage::helper('webpos/order');
            $orderId = $params['id'];
            $order = Mage::getModel('sales/order')->load($orderId);
            $statusHistory = $params['comment']['statusHistory'];
            $history = $order->addStatusHistoryComment($statusHistory['comment'], false);
            $history->setIsVisibleOnFront(true);
            $history->setIsCustomerNotified(true);
            $history->save();
            $order->save();
            $result = $helper->getAllOrderInfo($order);
            return $result;
        }
    }

    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_GET_ORDER_LIST:
                $result = $this->getListOrder();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_ORDER_TAKEPAYMENT:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->takePayment($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_ORDER_CANCEL:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->cancelOrder($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_ORDER_COMMENT:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->addComment($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::OPERATION_GET_ORDER_EMAIL:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->sendEmail($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_ORDER_IS_WAIT_PAYNL_RESPONSE:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->isWaitPayNlResponse($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @return Magestore_Webpos_Helper_Product
     */
    public function getProductHelper()
    {
        return $this->productHelper;
    }

    /**
     * @param Magestore_Webpos_Helper_Product $productHelper
     */
    public function setProductHelper($productHelper)
    {
        $this->productHelper = $productHelper;
    }


    public function isWaitPayNlResponse($params) {
        if (empty($params['order_id'])) {
            return array(
                'error' => true,
                'message' => 'Required order'
            );
        }

        $orderId = $params['order_id'];
        $my_file = Magestore_Webpos_Helper_Payment::NL_PAY_TRANSACTION_LOG_PATH . $orderId . '.txt';

        /* @var Mage_Sales_Model_Order $order   */
        $order = Mage::getModel('sales/order')->load($orderId);

        /* todo: remove all webpos payment if it is been canceled */
        if ($order->isCanceled()) {
            $this->paymentHelper->cancelOrder($order);
        }

        $orderData = $this->orderHelper->getOrderData($order);
        $orderData['items'] = $this->orderHelper->getItemsInfo($order);

        $orderData['is_waiting'] = !file_exists($my_file);

        if (file_exists($my_file)) {
            unlink($my_file);
        }

        return array(
            'items' => array($orderData)
        );
    }
}
