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
 * @package     Magestore_Webposauthorizenet
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
class Magestore_Webposauthorizenet_Model_Api2_Authorizenet_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_FINISH_PAYMENT = 'update';

    /**
     * @throws Exception
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::OPERATION_FINISH_PAYMENT:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->finishPayment($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function finishPayment($params)
    {
        $quoteId = $params['payment_id'];
        $token = $params['token'];
        $amount = $params['amount'];
        return Mage::getModel('webposauthorizenet/webposauthorizenet')->completePayment($quoteId, $token, $amount);
    }

}
