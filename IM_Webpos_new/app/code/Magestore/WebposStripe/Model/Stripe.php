<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripe\Model;

/**
 * Class Stripe
 * @package Magestore\WebposStripe\Model
 */
class Stripe implements \Magestore\WebposStripe\Api\StripeInterface
{
    const PAYMENT_METHOD = 'stripe';

    /**
     * @var \Magestore\WebposStripe\Helper\Data
     */
    protected $helper;

    /**
     * Stripe constructor.
     * @param \Magestore\WebposStripe\Helper\Data $helper
     */
    public function __construct(
        \Magestore\WebposStripe\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @return bool
     */
    public function validateRequiredSDK(){
        return (class_exists("\\Stripe\\Stripe") && class_exists("\\Stripe\\Charge"))?true:false;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key = ''){
        $configs = $this->helper->getStripeConfig();
        return ($key)?$configs[$key]:$configs;
    }

    /**
     * @param string $token
     * @param string $amount
     *
     * @return string
     * @throws \Exception
     */
    public function completeAppPayment($token, $amount){
        $response = '';
        $transactionId = '';
        if($amount && $token) {
            $storeManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('\Magento\Store\Model\StoreManagerInterface');
            $currency = $storeManager->getStore()->getBaseCurrencyCode();
            $helper = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magestore\Webpos\Helper\Data');
            $secretKey = $helper->getStoreConfig('webpos/payment/stripe/api_key');
            $cents = 100;
            if ($this->helper->isZeroDecimal($currency)) {
                $cents = 1;
            }
            $amount = $amount * $cents;
            try {
                \Stripe\Stripe::setApiKey($secretKey);
                $response = \Stripe\Charge::create(array(
                    "amount" => $amount,
                    "currency" => $currency,
                    "source" => $token,
                    "description" => __('Charge for POS')
                ));
            }catch (\Exception $e) {
                throw new \Magento\Framework\Exception\StateException(
                    __($e->getMessage())
                );
            }
        }
        if($response) {
            if(isset($response['id'])) {
                $transactionId = $response['id'];
            }
        } else {
            throw new \Magento\Framework\Exception\StateException(
                __('Cannot payment')
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
        \Stripe\Stripe::setApiKey($apiKey);
        $connected = true;
        try{
            $this->testCreate();
        }catch (\Exception $e){
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

        \Stripe\Charge::create(
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
        \Stripe\Stripe::setApiKey($apiKey);
        try
        {
            $token = \Stripe\Token::create($params);

            if (empty($token['id']) || strpos($token['id'],'tok_') !== 0)
                throw new \Exception(__('Sorry, this payment method can not be used at the moment. Try again later.'));

            return $token['id'];
        }
        catch (\Stripe\Error\Card $e)
        {
            throw new \Exception(__($e->getMessage()));
        }
    }

    /**
     * @param $payment
     * @param $orderCreateModel
     */
    public function placeOrderStripeCard($additionalData, $amount)
    {
        $params = [
            "card" => [
                "name" => $additionalData['cc_owner'],
                "number" => $additionalData['cc_number'],
                "cvc" => $additionalData['cc_cid'],
                "exp_month" => $additionalData['cc_exp_month'],
                "exp_year" => $additionalData['cc_exp_year']
            ]
        ];

        $token = $this->createToken($params);
        if ($token) {
            return $this->completeAppPayment($token, $amount);
        } else {
            return null;
        }
    }
}
