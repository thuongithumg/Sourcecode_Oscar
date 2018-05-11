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

class Magestore_Webpos_Model_Api2_ShoppingCart_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Checkout_Abstract implements Magestore_Webpos_Api_ShoppingCartInterface
{

    /**
     * Magestore_Webpos_Model_Api2_ShoppingCart_Rest_Admin_V1 constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('checkout_shoppingCart');
        $this->_helper = Mage::helper('webpos');
    }

    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::ACTION_GET_ITEMS:
                $quoteData = $this->_getQuoteInitData();
                $result = $this->_service->getItems($quoteData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;

            case self::ACTION_UPDATE_ITEMS:
                $quoteData = $this->_getQuoteInitData();
                $removeIds = $this->_processRequestParams(self::REMOVE_IDS);
                $moveIds = $this->_processRequestParams(self::MOVE_IDS);
                $result = $this->_service->updateItems($quoteData, $removeIds, $moveIds);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }
}
