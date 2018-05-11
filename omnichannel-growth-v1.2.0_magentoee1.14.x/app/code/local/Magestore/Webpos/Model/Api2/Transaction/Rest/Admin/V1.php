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

class Magestore_Webpos_Model_Api2_Transaction_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract implements Magestore_Webpos_Api_TransactionInterface
{
    /**
     * Magestore_Webpos_Model_Api2_Transaction_Rest_Admin_V1 constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('transaction_transaction');
        $this->_helper = Mage::helper('webpos');
    }
    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();

        switch ($this->getActionType()) {
            case self::ACTION_SAVE_TRANSACTION:
                $transactionData = $this->_processRequestParams(self::DATA);
                $result = $this->_service->saveTransaction($transactionData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_APP_SAVE_TRANSACTION:
                $transactionData = $this->_processRequestParams(self::DATA);
                if (!$transactionData)
                    $transactionData = $this->getRequest()->getBodyParams();
                $result = $this->_service->saveAppTransaction($transactionData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_GET_LIST_TRANSACTION:
                $cashDrawerId = $this->_processRequestParams(self::TILL_ID);
                $result = $this->_service->getList($cashDrawerId);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }
}
