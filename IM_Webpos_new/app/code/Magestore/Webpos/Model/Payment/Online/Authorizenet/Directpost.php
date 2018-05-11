<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Payment\Online\Authorizenet;

/**
 * class \Magestore\Webpos\Model\Payment\Online\Authorizenet\Directpost
 *
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Directpost extends \Magento\Authorizenet\Observer\AddFieldsToResponseObserver
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $adminUrl;

    /**
     * Directpost constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Authorizenet\Model\Directpost $payment
     * @param \Magento\Authorizenet\Model\Directpost\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\UrlInterface $adminUrl
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Authorizenet\Model\Directpost $payment,
        \Magento\Authorizenet\Model\Directpost\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\UrlInterface $adminUrl
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->adminUrl = $adminUrl;
        parent::__construct($coreRegistry, $payment, $session, $storeManager);
    }

    /**
     * get authorize request information
     *
     * @param  $order
     * @return array
     */
    public function getRequestInformation($order)
    {
        if (!$order || !$order->getId()) {
            return false;
        }
        $payment = $order->getPayment();
        if (!$payment || $payment->getMethod() != $this->payment->getCode()) {
            return $this;
        }
        $result = array();
        $this->session->addCheckoutOrderIncrementId($order->getIncrementId());
        $this->session->setLastOrderIncrementId($order->getIncrementId());
        $requestToAuthorizenet = $payment->getMethodInstance()
            ->generateRequestFromOrder($order);
        $requestToAuthorizenet->setControllerActionName('webpos');
        $requestToAuthorizenet->setIsSecure(
            (string)$this->storeManager->getStore()
                ->isCurrentlySecure()
        );
        $requestToAuthorizenet->setOrderSendConfirmation(false);
        $requestToAuthorizenet->setStoreId($order->getStoreId());
        if($requestToAuthorizenet->getData('x_amount')){
            $amount = floatval($requestToAuthorizenet->getData('x_amount'));
            if($amount != $order->getBaseGrandTotal()){
                $requestToAuthorizenet->setData('x_amount', $order->getBaseGrandTotal());
                $requestToAuthorizenet->signRequestData();
            }
        }
        if ($this->adminUrl->useSecretKey()) {
            $requestToAuthorizenet->setKey(
                $this->adminUrl->getSecretKey('adminhtml', 'authorizenet_directpost_payment', 'redirect')
            );
        }
        $url = $payment->getMethodInstance()->getCgiUrl();
//        $result['url'] = 'https://test.authorize.net/gateway/transact.dll';
        $result['url'] = $url;
        $result['params'] = $requestToAuthorizenet->getData();
        if(is_array($result['params'])){
            $result['params']['is_webpos'] = 'webpos';
            $result['params']['controller_action_name'] = 'webpos';
            $result['params']['x_relay_url'] = $this->urlBuilder->getUrl('webpos/directpost_payment/response');
        }
        return $result;
    }

}