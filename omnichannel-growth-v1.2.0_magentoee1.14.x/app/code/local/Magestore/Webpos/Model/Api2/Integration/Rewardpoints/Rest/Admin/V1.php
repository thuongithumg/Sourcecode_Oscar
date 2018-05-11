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

class Magestore_Webpos_Model_Api2_Integration_Rewardpoints_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract implements Magestore_Webpos_Api_Integration_RewardpointsInterface
{
    /**
     * Magestore_Webpos_Model_Api2_Integration_Rewardpoints_Rest_Admin_V1 constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('integration_rewardpoints');
        $this->_helper = Mage::helper('webpos');
    }

    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::ACTION_GET_RATES:
                $result = $this->_service->getRates();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_GET_BALANCE:
                $customerId = $this->_processRequestParams(self::CUSTOMER_ID);
                $result = $this->_service->getBalance($customerId);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_GET_LIST:
                $result = $this->_service->getList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SPEND_POINT:
                $quoteData = $this->_getQuoteInitData();
                $data = $this->_processRequestParams(self::DATA);
                $result = $this->_service->spendPoint($quoteData, $data);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    protected function _getQuoteInitData(){
        $quoteData = $this->_processRequestParams(array(
            Magestore_Webpos_Api_Cart_QuoteDataInitInterface::QUOTE_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CUSTOMER_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::STORE_ID,Magestore_Webpos_Api_Cart_QuoteDataInitInterface::CURRENCY_ID
        ));
        $quoteDataModel = $this->_getDataModel(self::SCOPE_CART, self::DATA_QUOTE_INIT);
        $quoteDataModel->setData($quoteData);
        return $quoteData;
    }
}
