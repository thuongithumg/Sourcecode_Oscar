<?php

class Magestore_Webpos_Directpost_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get iframe block instance
     *
     * @return Mage_Authorizenet_Block_Directpost_Iframe
     */
    protected function _getIframeBlock()
    {
        return $this->getLayout()->createBlock('directpost/iframe');
    }

    /**
     * @return mixed
     */
    protected function _getDirectPostSession(){
        return Mage::getSingleton('authorizenet/directpost_session');
    }

    /**
     * Retrieve redirect iframe url
     * @param $params
     * @param $storeId
     * @return string
     */
    public function getRedirectIframeUrl($params, $storeId = 0)
    {
        if($storeId){
            return Mage::app()->getStore($storeId)->getUrl('webpos/directpost_payment/redirect', $params);
        }
        return Mage::getUrl('webpos/directpost_payment/redirect', $params);
    }

    /**
     * Response action.
     * Action for Authorize.net SIM Relay Request.
     */
    public function responseAction()
    {
        $data = $this->getRequest()->getPost();
        unset($data['redirect_parent']);
        unset($data['redirect']);
        /* @var $paymentMethod Mage_Authorizenet_Model_DirectPost */
        $paymentMethod = Mage::getModel('authorizenet/directpost');

        $result = array();
        if (!empty($data['x_invoice_num'])) {
            $result['x_invoice_num'] = $data['x_invoice_num'];
        }

        try {
            if (!empty($data['store_id'])) {
                $paymentMethod->setStore($data['store_id']);
            }
            $paymentMethod->process($data);
            $result['success'] = 1;
        }
        catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = $e->getMessage();
        }
        catch (Exception $e) {
            Mage::logException($e);
            $result['success'] = 0;
            $result['error_msg'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }

        if (!empty($data['controller_action_name'])) {
            if (!empty($data['key'])) {
                $result['key'] = $data['key'];
            }
            $storeId = 0;
            if (!empty($data['store_id'])) {
                $storeId = $data['store_id'];
            }
            $result['controller_action_name'] = $data['controller_action_name'];
            $result['is_secure'] = isset($data['is_secure']) ? $data['is_secure'] : false;
            $result['redirect'] = $this->getRedirectIframeUrl($result, $storeId);
        }
        $block = $this->_getIframeBlock()->setParams($result);
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Retrieve params and put javascript into iframe
     *
     */
    public function redirectAction()
    {
        $redirectParams = $this->getRequest()->getParams();
        unset($redirectParams['redirect']);
        $params = array();
        if (!empty($redirectParams['success'])
            && isset($redirectParams['x_invoice_num'])
            && isset($redirectParams['controller_action_name'])
        ) {
            $this->_getDirectPostSession()->unsetData('quote_id');
        }
        if (!empty($redirectParams['error_msg'])) {
            $cancelOrder = empty($redirectParams['x_invoice_num']);
            $this->_returnCustomerQuote($cancelOrder, $redirectParams['error_msg']);
        }
        $block = $this->_getIframeBlock()->setParams(array_merge($params, $redirectParams));
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Return customer quote
     *
     * @param bool $cancelOrder
     * @param string $errorMsg
     */
    protected function _returnCustomerQuote($cancelOrder = false, $errorMsg = '')
    {
        $incrementId = $this->_getDirectPostSession()->getLastOrderIncrementId();
        if ($incrementId &&
            $this->_getDirectPostSession()
                ->isCheckoutOrderIncrementIdExist($incrementId)
        ) {
            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                $quote = Mage::getModel('sales/quote')
                    ->load($order->getQuoteId());
                if ($quote->getId()) {
                    $quote->setIsActive(1)
                        ->setReservedOrderId(NULL)
                        ->save();
                    $this->_getCheckout()->replaceQuote($quote);
                }
                $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($incrementId);
                $this->_getDirectPostSession()->unsetData('quote_id');
                if ($cancelOrder) {
                    $order->registerCancellation($errorMsg)->save();
                }
            }
        }
    }
}
