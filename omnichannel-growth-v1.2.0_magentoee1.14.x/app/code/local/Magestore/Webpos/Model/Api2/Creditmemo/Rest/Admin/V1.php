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
 * API2 for creditmemo
 *
 * @author     Magestore Core Team
 */
class Magestore_Webpos_Model_Api2_Creditmemo_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_CREATE_CREDITMEMO = 'save';


    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_CREATE_CREDITMEMO:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->saveCreditMemo($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_GET_ORDER_TAKEPAYMENT:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->takePayment($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @param array $param
     * @return array
     */
    protected function saveCreditMemo($param) {
        $dif = array();
        $qtyEx = explode('$refund$', $param['entity']['qty']);
        $stockEx = explode('$refund$', $param['entity']['stock']);
        $helper = Mage::helper('webpos/order');
        for ($i = 0; $i < count($qtyEx); $i++) {
            $anItemQty = explode('/', $qtyEx[$i]);
            $anItemStock = explode('/', $stockEx[$i]);
            if (isset($anItemQty[1]))
                $dif[$i]['qty'] = $anItemQty[1];
            if (isset($anItemQty[0]) && $anItemQty[0] != '')
                $dif[$i]['order_item_id'] = $anItemQty[0];
            if (isset($anItemStock[1]))
                $dif[$i]['back_to_stock'] = $anItemStock[1];
        }
        $orderId = $param['entity']['orderId'];
        $orderIncrementId = $param['entity']['increment_id'];
        $info = array();
        $info['order_increment_id'] = $orderIncrementId;
        $other = $this->addOtherInfo($orderId, $param);
        $result = array();
        try {
            $creditmemo = $this->_prepareCreditmemo($dif, $info, $other, $param['entity']['emailSent']);
            if(isset($param['entity']['refund_by_cash']) && $param['entity']['refund_by_cash'] == 1){
                Mage::dispatchEvent('webpos_refund_by_cash_after', array(
                    'creditmemo' => $creditmemo
                ));
            }
            $order = Mage::getModel('sales/order')->load($orderId);
            $result = $helper->getAllOrderInfo($order);
            if (!$result['error'])
                $result['success'] = 'The credit memo has been created successfully!';
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param int $orderId
     * @param array $param
     * @return array
     */
    public function addOtherInfo($orderId, $param) {
        $invoiceId = isset($param['entity']['invoice_id'])?$param['entity']['invoice_id']:'';
        $ajustFee = isset($param['entity']['adjustmentNegative'])?$param['entity']['adjustmentNegative']:'';
        $ajustRf = isset($param['entity']['adjustmentPositive'])?$param['entity']['adjustmentPositive']:'';
        $shipRefunded = isset($param['entity']['shippingAmount'])?$param['entity']['shippingAmount']:0;
        $shipRefunded = round($shipRefunded, 2);



        $other = array(
            "do_offline" => "1",
            "shipping_amount" => $shipRefunded,
            "adjustment_positive" => (float) $ajustRf,
            "adjustment_negative" => (float) $ajustFee,
            "invoice_id" => $invoiceId
        );

        $comments = empty($param['entity']['comments'])?array():$param['entity']['comments'];

        if(count($comments) && $comment = $comments[0]){
            $other['comment_text'] = $comment['comment'];
        }

        if (isset($param['entity']['customercredit_discount'])) {
            $customercredit_discount = $param['entity']['customercredit_discount'];
            if ($customercredit_discount != '' && $customercredit_discount != null) {
                $order = Mage::getModel('sales/order')->load($orderId);
                $order->addStatusToHistory('', $this->__('Added ' . $customercredit_discount . ' credit to customer account'))->save();
                if($order->getData('customercredit_discount') > 0){
                    $customercredit_discount += floatval($order->getData('customercredit_discount'));
                }
                $other['customercredit_discount'] = $customercredit_discount;
                $other['comment_text'] = $other['comment_text'] . ' ' . $this->__('Added ' . $customercredit_discount . ' credit to customer account');
            }
        }
        return $other;
    }

    /**
     * @param array $dif
     * @param array $info
     * @param array $other
     * @param boolean $emailSent
     * @return object
     */
    protected function _prepareCreditmemo($dif, $info, $other, $emailSent) {
        $qtys = array();
        $backToStock = array();
        foreach ($dif as $item) {
            if (isset($item['qty'])) {
                $qtys[$item['order_item_id']] = array("qty" => $item['qty']);
            }
            if (isset($item['back_to_stock']) && $item['back_to_stock'] == 'true') {
                $backToStock[$item['order_item_id']] = array("back_to_stock" => true);
            }
        }
        $data = array(
            "items" => $qtys,
            'back_to_stock' => $backToStock
        );
        $data = array_merge($data, $other);
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        $creditmemo = $this->_initCreditmemo($data, $info);
        if ($creditmemo) {
            if (isset($other['customercredit_discount'])) {
                $creditmemo->setData('customercredit_discount', $other['customercredit_discount']);
            }
            if (!empty($data['comment_text'])) {
                $creditmemo->addComment(
                    $data['comment_text'], isset($data['comment_customer_notify']), isset($data['is_visible_on_front'])
                );
            }
            $creditmemo->register();
            $this->_saveCreditmemo($creditmemo);
            if ($emailSent) {
               $this->sendEmail($creditmemo);
            }
            return $creditmemo;
        }
    }

    /**
     * @param object $creditmemo
     */
    public function sendEmail($creditmemo) {
        $creditmemo->sendEmail();
        $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
            ->getUnnotifiedForInstance($creditmemo, Mage_Sales_Model_Order_Creditmemo::HISTORY_ENTITY_NAME);
        if ($historyItem) {
            $historyItem->setIsCustomerNotified(1);
            $historyItem->save();
        }
    }

    /**
     * @param array $data
     * @param array $info
     * @return object
     */
    protected function _initCreditmemo($data, $info, $update = false) {
        $creditmemo = false;
        $invoice = false;
        $orderId = $info['order_increment_id']; //$this->getRequest()->getParam('order_id');
        $invoiceId = $data['invoice_id'];
        if ($orderId) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            if ($invoiceId) {
                $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)->setOrder($order);
            }
            if (!$order->canCreditmemo()) {
                return false;
            }
            $savedData = array();
            $qtys = array();
            $backToStock = array();
            if (isset($data['items'])) {
                $savedData = $data['items'];
                foreach ($savedData as $orderItemId => $itemData) {
                    if (isset($itemData['qty'])) {
                        $qtys[$orderItemId] = $itemData['qty'];
                    }
                }
            }
            if (isset($data['back_to_stock'])) {
                $savedData = $data['back_to_stock'];
                foreach ($savedData as $orderItemId => $itemData) {
                    if (isset($itemData['back_to_stock'])) {
                        $backToStock[$orderItemId] = $itemData['back_to_stock'];
                    }
                }
            }
            $data['qtys'] = $qtys;
            $data['back_to_stock'] = $backToStock;
            $creditmemo = $this->prepareCreditmemo($invoice, $order, $data);
             // Process back to stock flags
            $this->processCreditmemoItem($creditmemo, $backToStock, $savedData);
            Mage::register('current_creditmemo', $creditmemo);
        }
        return $creditmemo;
    }

    /**
     * @param object $invoice
     * @param object $order
     * @param array $data
     * @return object
     */
    public function prepareCreditmemo($invoice, $order, $data) {
        $service = Mage::getModel('sales/service_order', $order);
        if ($invoice) {
            if ($invoice->getWebposCash()) {
                $order->setWebposCash(0)->setWebposBaseCash(0);
                $invoice->setWebposCash(0)->setWebposBaseCash(0);
            }
            $creditmemo = $service->prepareCreditmemo($data);
//            $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
        } else {
            $creditmemo = $service->prepareCreditmemo($data);
        }
        return $creditmemo;
    }

    /**
     * @param object $creditmemo
     * @param array $backToStock
     * @param array $savedData
     */
    public function processCreditmemoItem($creditmemo, $backToStock, $savedData) {
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            $parentId = $orderItem->getParentItemId();
            if (isset($backToStock[$orderItem->getId()])) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } elseif (empty($savedData)) {
                $creditmemoItem->setBackToStock(Mage::helper('cataloginventory')->isAutoReturnEnabled());
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }
    }

    /**
     * @param object $creditmemo
     * @return object
     */
    protected function _saveCreditmemo($creditmemo) {
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder());
//        if ($creditmemo->getInvoice()) {
//            $transactionSave->addObject($creditmemo->getInvoice());
//        }
        $transactionSave->save();
        return $this;
    }

}
