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
 * @package     Magestore_Webposstripe
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Webposstripe Model
 * 
 * @category    Magestore
 * @package     Magestore_Webposstripe
 * @author      Magestore Developer
 */
require Mage::getBaseDir() . '/vendor/webpos_stripe.php';

use Stripe\Stripe;
use Stripe\Charge;

class Magestore_Webposstripe_Model_Webposstripe extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webposstripe/webposstripe');
    }

    /**
     * @return bool
     */
    public function validateRequiredSDK(){
        return (class_exists("\\Stripe\\Stripe") && class_exists("\\Stripe\\Charge"))?true:false;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getConfig($key = ''){
        $config = Mage::helper('webposstripe')->getStripeConfig($key);
        return $config;
    }

    /**
     * @param string $token
     * @param string $amount
     *
     * @return string
     * @throws Exception
     */
    public function completePayment($token, $amount){
        if(!$this->validateRequiredSDK()) {
            throw new Exception(Mage::helper('webpos')->__('Transaction Failed'));
        }
        $response = '';
        $transactionId = '';
        if($amount && $token) {
            $currency = Mage::app()->getStore()->getBaseCurrencyCode();
            $secretKey = $this->getConfig('api_key');
            $cents = 100;
            if (Mage::helper('webposstripe')->isZeroDecimal($currency)) {
                $cents = 1;
            }
            $amount = $amount * $cents;
            try {
                Stripe::setApiKey($secretKey);
                $response = Charge::create(array(
                    "amount" => $amount,
                    "currency" => $currency,
                    "source" => $token,
                    "description" =>  Mage::helper('webpos')->__('Charge for POS')
                ));
            }catch (Exception $e) {
                throw new Exception(
                    Mage::helper('webpos')->__($e->getMessage())
                );
            }
        }
        if($response) {
            if(isset($response['id'])) {
                $transactionId = $response['id'];
            }
        } else {
            throw new Exception(
                Mage::helper('webpos')->__('Transaction Failed')
            );
        }
        return $transactionId;
    }

    /**
     * test connect stripe API
     *
     * @return bool
     */
    public function canConnectToApi(){
        $apiKey = $this->getConfig('api_key');
        Stripe::setApiKey($apiKey);
        $connected = true;
        try{
            $this->testCreate();
        }catch (Exception $e){
            $connected = false;
        }
        return $connected;
    }

    /**
     * test create stripe payment
     *
     */
    public function testCreate()
    {
        $card = array(
            'number' => '4242424242424242',
            'exp_month' => 5,
            'exp_year' => date('Y') + 1
        );

        Charge::create(
            array(
                'amount' => 100,
                'currency' => 'usd',
                'card' => $card
            )
        );
    }
    /**
     * @param $params
     * @return mixed|null|string
     * @throws Exception
     */
    public function createToken($params)
    {
        $apiKey = $this->getConfig('api_key');
        Stripe::setApiKey($apiKey);
        try
        {
            $token = \Stripe\Token::create($params);

            if (empty($token['id']) || strpos($token['id'],'tok_') !== 0)
                throw new \Exception(Mage::helper('webpos')->__('Sorry, this payment method can not be used at the moment. Try again later.'));

            return $token['id'];
        }
        catch (\Stripe\Error\Card $e)
        {
            throw new \Exception(Mage::helper('webpos')->__($e->getMessage()));
        }
    }

}