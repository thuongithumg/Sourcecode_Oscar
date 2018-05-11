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
 * @package     Magestore_Webpospaypal
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webpospaypal Model
 * 
 * @category    Magestore
 * @package     Magestore_Webpospaypal
 * @author      Magestore Developer
 */

require Mage::getBaseDir() . '/vendor/webpos_paypal.php';

use PayPal\Api\Payment;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\ExceptionPayPalConnectionException;
use PayPal\Api\OpenIdTokeninfo;

class Magestore_Webpospaypal_Model_Webpospaypal extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webpospaypal/webpospaypal');
    }

    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key = ''){
        $config = Mage::helper('webpospaypal')->getPaypalConfig($key);
        return $config;
    }

    /**
     * @return bool
     */
    public function validateRequiredSDK(){
        return class_exists("\\PayPal\\Rest\\ApiContext")?true:false;
    }

    /**
     * @return ApiContext
     */
    public function getApiContext(){
        $clientId = $this->getConfig('client_id');
        $clientSecret = $this->getConfig('client_secret');
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );
        $environment = 'live';
        if($this->getConfig('is_sandbox')) {
            $environment = 'sandbox';
        }
        $apiContext->setConfig(array(
            'mode' => $environment
        ));
        $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', 'Magestore_POS');
        return $apiContext;
    }

    /**
     * @param string $paymentId
     * @return string
     * @throws Exception
     */
    public function completePayment($paymentId){
        if(!$this->validateRequiredSDK()) {
            throw new Exception(Mage::helper('webpos')->__('Transaction Failed'));
        }
        $apiContext = $this->getApiContext();
        $payment = Payment::get($paymentId, $apiContext);

        $transactionId = '';
        try {
            $payment->get($paymentId, $apiContext);
            $transactions = $payment->getTransactions();
            if(!empty($transactions) && isset($transactions[0])){
                $relatedResources = $transactions[0]->getRelatedResources();
                if(!empty($relatedResources) && isset($relatedResources[0])){
                    $sale = $relatedResources[0]->getSale();
                    $transactionId = $sale->getId();
                }
            }
        } catch (ExceptionPayPalConnectionException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
        return $transactionId;
    }

    /**
     * get access token
     *
     * @return string
     * @throws Exception
     */
    public function getAccessToken(){
        $apiContext = $this->getApiContext();
        $clientId = $this->getConfig('client_id');
        $clientSecret = $this->getConfig('client_secret');
        $refreshToken = $this->getConfig('refresh_token');
        if(!$refreshToken) {
            throw new Exception(Mage::helper('webpos')->__('Refresh token was missing'));
        }
        $params = array(
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken
        );
        $openIdTokenInfo = new OpenIdTokeninfo();
        $tokenInfo = $openIdTokenInfo->createFromRefreshToken($params, $apiContext);
        if($tokenInfo) {
            $accessToken = $tokenInfo->access_token;
            if ($accessToken) {
                Mage::getConfig()->saveConfig('webpos/payment/paypal_access_token',
                    $accessToken,
                    'default',
                    0
                );
                Mage::app()->getCacheInstance()->cleanType('config');
                return $accessToken;
            }
        }
        throw new Exception(Mage::helper('webpos')->__('Cannot generate new access token'));
    }

    /**
     * get token info
     *
     * @return string
     * @throws Exception
     */
    public function getTokenInfo($authCode)
    {
        $apiContext = $this->getApiContext();
        $clientId = $this->getConfig('client_id');
        $clientSecret = $this->getConfig('client_secret');
        $params = array(
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $authCode
        );
        $tokenInfo = OpenIdTokeninfo::createFromAuthorizationCode($params, null, null, $apiContext);
        return $tokenInfo;
    }
}