<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Model;

use Magento\Framework\Exception\StateException;
use \net\authorize\api\contract\v1 AS apiContract;
use \net\authorize\api\controller AS apiController;
use \net\authorize\api\constants AS apiConstants;

/**
 * Class Authorizenet
 * @package Magestore\WebposAuthorizenet\Model
 */
class Authorizenet implements \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface
{
    const PAYMENT_METHOD = 'authorizenet';

    /**
     * @var \Magestore\WebposAuthorizenet\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * Authorizenet constructor.
     * @param \Magestore\WebposAuthorizenet\Helper\Data $helper
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magestore\WebposAuthorizenet\Helper\Data $helper,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return bool
     */
    public function validateRequiredSDK(){
        return (class_exists("\\net\\authorize\\api\\contract\\v1\\MerchantAuthenticationType"))?true:false;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key = ''){
        $configs = $this->helper->getAuthorizenetConfig();
        return ($key)?$configs[$key]:$configs;
    }

    /**
     * get quote by id
     * @param string $quoteId
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getQuoteById($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        return $quote;
    }

    /**
     * @param string $quoteId
     * @param string $token
     * @param string $amount
     *
     * @return string
     * @throws \Exception
     */
    public function completePayment($quoteId, $token, $amount){
        $quote = $this->getQuoteById($quoteId);
        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        $merchantAuthentication = new apiContract\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->getConfig('api_login'));
        $merchantAuthentication->setTransactionKey($this->getConfig('transaction_key'));

        // Set the transaction's refId
        $refId = 'ref' . time();
        // Create the payment object for a payment nonce
        $opaqueData = new apiContract\OpaqueDataType();
        $opaqueData->setDataDescriptor("COMMON.ACCEPT.INAPP.PAYMENT");
        $opaqueData->setDataValue($token);

        // Add the payment data to a paymentType object
        $paymentOne = new apiContract\PaymentType();
        $paymentOne->setOpaqueData($opaqueData);
        // Create order information
        $order = new apiContract\OrderType();
        $order->setInvoiceNumber($quote->getId());
        $order->setDescription(__('Payment for POS'));

        $billingAddress = $quote->getBillingAddress();
        $region = $billingAddress->getRegionCode() ? $billingAddress->getRegionCode() : $billingAddress->getRegion();
        // Set the customer's Bill To address
        $customerAddress = new apiContract\CustomerAddressType();
        $customerAddress->setFirstName($billingAddress->getFirstname());
        $customerAddress->setLastName($billingAddress->getLastname());
        $customerAddress->setCompany($billingAddress->getCompany());
        $customerAddress->setAddress($billingAddress->getStreet()[0]);
        $customerAddress->setCity($billingAddress->getCity());
        $customerAddress->setState($region);
        $customerAddress->setZip($billingAddress->getPostcode());
        $customerAddress->setCountry($billingAddress->getCountryId());

        // Set the customer's identifying information
        $customerData = new apiContract\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId(time());
        $customerData->setEmail($quote->getCustomerEmail());

        //Add values for transaction settings
        $duplicateWindowSetting = new apiContract\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("600");
        // Create a transactionRequestType object and add the previous objects to it
        $type = 'authOnlyTransaction';
        if($this->getConfig('payment_action') == \Magento\Authorizenet\Model\Authorizenet::ACTION_AUTHORIZE_CAPTURE) {
            $type = 'authCaptureTransaction';
        }
        $transactionRequestType = new apiContract\TransactionRequestType();
        $transactionRequestType->setTransactionType($type);
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        // Assemble the complete transaction request
        $request = new apiContract\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        // Create the controller and get the response
        $controller = new apiController\CreateTransactionController($request);
        if($this->getConfig('is_sandbox')) {
            $apiUrl = apiConstants\ANetEnvironment::SANDBOX;
        } else {
            $apiUrl = apiConstants\ANetEnvironment::PRODUCTION;
        }
        $response = $controller->executeWithApiResponse($apiUrl);
        $result = '';
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $result = $tresponse->getTransId();
                } else {
                    throw new StateException(__('Transaction Failed'));
                }
                // Or, print errors if the API request wasn't successful
            } else {
                throw new StateException(__('Transaction Failed'));
            }
        } else {
            throw new StateException(__('Transaction Failed'));
        }
        return $result;
    }

    /**
     * test connect authorizenet API
     *
     * @return bool
     */
    public function canConnectToApi(){
        if($this->testCreate()) {
            return true;
        }
        return false;
    }

    /**
     * test create authorizenet payment
     *
     */
    public function testCreate()
    {
        try {
            /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
            $merchantAuthentication = new apiContract\MerchantAuthenticationType();
            $merchantAuthentication->setName('5KP3u95bQpv');
            $merchantAuthentication->setTransactionKey('346HZ32z3fP4hTG2');

            // Set the transaction's refId
            $refId = 'ref' . time();
            // Create the payment data for a credit card
            $creditCard = new apiContract\CreditCardType();
            $creditCard->setCardNumber("4111111111111111");
            $creditCard->setExpirationDate("1226");
            $creditCard->setCardCode("123");
            $paymentOne = new apiContract\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            $order = new apiContract\OrderType();
            $order->setDescription("New Item");
            // Set the customer's Bill To address
            $customerAddress = new apiContract\CustomerAddressType();
            $customerAddress->setFirstName("Ellen");
            $customerAddress->setLastName("Johnson");
            $customerAddress->setCompany("Souveniropolis");
            $customerAddress->setAddress("14 Main Street");
            $customerAddress->setCity("Pecan Springs");
            $customerAddress->setState("TX");
            $customerAddress->setZip("44628");
            $customerAddress->setCountry("USA");
            // Set the customer's identifying information
            $customerData = new apiContract\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setId(time());
            $customerData->setEmail("EllenJohnson@example.com");
            //Add values for transaction settings
            $duplicateWindowSetting = new apiContract\SettingType();
            $duplicateWindowSetting->setSettingName("duplicateWindow");
            $duplicateWindowSetting->setSettingValue("600");
            // Create a TransactionRequestType object
            $transactionRequestType = new apiContract\TransactionRequestType();
            $transactionRequestType->setTransactionType("authOnlyTransaction");
            $transactionRequestType->setAmount(100);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
            $request = new apiContract\CreateTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId($refId);
            $request->setTransactionRequest($transactionRequestType);
            $controller = new apiController\CreateTransactionController($request);
            $response = $controller->executeWithApiResponse(apiConstants\ANetEnvironment::SANDBOX);
            if ($response != null) {
                if ($response->getMessages()->getResultCode() == 'Ok') {
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            return false;
        }catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
