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

abstract class Magestore_Webpos_Model_Api2_Cart_Abstract extends Magestore_Webpos_Model_Api2_Abstract
{
    /**#@+
     * Actions code
     */
    const ACTION_ADD_PRODUCTS = 'add_products';
    /**#@- */

    /**#@+
     * Message
     */
    const MESSAGE_CART_SERVICE_NOT_FOUND = 'Cart service does not exist';
    /**#@- */


    /**
     * Magestore_Webpos_Model_Api2_Cart_Abstract constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('checkout_cart');
        $this->_helper = Mage::helper('webpos');
    }

    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::ACTION_ADD_PRODUCTS:
                $products = $this->getRequest()->getParam('products');
                $this->_render($this->addProducts($products));
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * @param $products
     * @return mixed
     */
    public function addProducts($products)
    {
        return $this->_service->addProducts($products);
    }
}
