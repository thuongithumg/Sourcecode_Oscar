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

class Magestore_Webpos_Model_Api2_Integration_Storecredit_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract implements Magestore_Webpos_Api_Integration_StorecreditInterface
{
    /**
     * Magestore_Webpos_Model_Api2_Integration_Storecredit_Rest_Admin_V1 constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('integration_storecredit');
        $this->_helper = Mage::helper('webpos');
    }

    /**
     * @param array $params
     * @return array
     */
    public function refundByCredit($params) {
        $enableStoreCredit = $this->getHelper()->isStoreCreditEnable();
        $data = array();
        if($enableStoreCredit && $params && isset($params['amount']) && !empty($params['amount'])){
            $customerId = $params['customer_id'];
            $orderIncrementId = $params['increment_id'];
            $amount = $params['amount'];
            if($customerId){
                $transaction = Mage::getModel('customercredit/transaction');
                $customercredit = Mage::getModel('customercredit/customercredit');
                $type_id = Magestore_Customercredit_Model_TransactionType::TYPE_REFUND_ORDER_INTO_CREDIT;
                $transaction_detail = $this->getHelper()->__("Refund order") ." #". $orderIncrementId;
                if ($transaction && $customercredit && !empty($amount)) {
                    $transaction->addTransactionHistory($customerId, $type_id, $transaction_detail , $orderIncrementId, $amount);
                    $customercredit->changeCustomerCredit($amount, $customerId);
                }
                $data['success'] = true;
            }else{
                $data['message'] = $this->getHelper()->__('Customer account not found');
                $data['error'] = true;
            }
        }

        return $data;
    }

    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::ACTION_GET_LIST:
                $result = $this->_service->getList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_GET_BALANCE:
                $customerId = $this->_processRequestParams(self::CUSTOMER_ID);
                $result = $this->_service->getBalance($customerId);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_REFUND_BY_CREDIT:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->refundByCredit($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }
}
