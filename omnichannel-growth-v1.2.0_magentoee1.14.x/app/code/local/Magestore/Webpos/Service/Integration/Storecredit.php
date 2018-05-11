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

class Magestore_Webpos_Service_Integration_Storecredit extends Magestore_Webpos_Service_Abstract
{
    /**
     * @return mixed
     */
    public function getList(){
        $data = array();
        $message = array();
        if($this->_helper->isStoreCreditEnable()){
            $items = array();
            $collection = $this->_getResource('customer/customer_collection');
            $collection->addAttributeToFilter('credit_value', array('gt' => 0.00));
            $collection->load();
            if($collection->getSize() > 0){
                foreach ($collection as $customer){
                    $items[] = array(
                        'credit_id' => $customer->getId(),
                        'customer_id' => $customer->getId(),
                        'credit_balance' => $customer->getCreditValue()
                    );
                }
            }
            $data['items'] = $items;
            $data['total_count'] = count($items);
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
        }else{
            $message[] = $this->__('Customer Credit module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return (!empty($data))?$data:$this->getResponseData($data, $message, $status);
    }

    /**
     * @param $customerId
     * @return mixed
     */
    public function getBalance($customerId){
        $data = array();
        $message = array();
        if($this->_helper->isStoreCreditEnable()){
            if($customerId){
                $model = $this->_getModel('customer/customer');
                $model->load($customerId);
                if($model->getId() > 0){
                    $data['balance'] = floatval($model->getCreditValue());
                }else{
                    $data['balance'] = floatval(0);
                }
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
            }else{
                $data['message'] = $this->__('Please choose customer account');
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
            }
        }else{
            $message[] = $this->__('Customer Credit module has been disabled or have not been installed yet');
            $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        }
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param $customerId
     * @param $orderIncrementId
     * @param $amount
     * @return mixed
     */
    public function refundByCredit($customerId, $orderIncrementId, $amount){
        $data = array();
        $message = array();
        if($amount){
            if($this->_helper->isStoreCreditEnable()){
                if($customerId){
                    $transaction = $this->_getModel('customercredit/transaction');
                    $customercredit  = $this->_getModel('customercredit/customercredit');
                    $type_id = Magestore_Customercredit_Model_TransactionType::TYPE_REFUND_ORDER_INTO_CREDIT;
                    $transaction_detail = $this->__("Refund order") ." #". $orderIncrementId;
                    if ($transaction && $customercredit && !empty($amount)) {
                        $transaction->addTransactionHistory($customerId, $type_id, $transaction_detail , $orderIncrementId, $amount);
                        $customercredit->changeCustomerCredit($amount, $customerId);
                    }
                    $status = Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS;
                }else{
                    $data['message'] = $this->__('Please choose customer account');
                    $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
                }
            }else{
                $message[] = $this->__('Customer Credit module has been disabled or have not been installed yet');
                $status = Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
            }
        }
        return $this->getResponseData($data, $message, $status);
    }
}
