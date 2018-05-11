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

/**
 * Webposauthorizenet Model
 * 
 * @category    Magestore
 * @package     Magestore_Webposauthorizenet
 * @author      Magestore Developer
 */

require Mage::getBaseDir() . '/vendor/webpos_authorizenet.php';

use net\authorize\api\contract\v1 AS apiContract;
use net\authorize\api\controller AS apiController;
use net\authorize\api\constants AS apiConstants;

class Magestore_Webposauthorizenet_Model_Webposauthorizenet extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webposauthorizenet/webposauthorizenet');
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
        $config = Mage::helper('webposauthorizenet')->getAuthorizenetConfig($key);
        return $config;
    }

    /**
     * get quote by id
     * @param string $quoteId
     * @return mixed
     */
    public function getQuoteById($quoteId)
    {
        $quote = Mage::getModel('sales/quote')->load($quoteId);
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
        if(!$this->validateRequiredSDK()) {
            throw new Exception(Mage::helper('webpos')->__('Transaction Failed'));
        }
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
        $order->setDescription(Mage::helper('webpos')->__('Payment for POS'));

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
        if($this->getConfig('payment_action') == 'authorize_capture') {
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
                    throw new Exception(Mage::helper('webpos')->__('Transaction Failed'));
                }
                // Or, print errors if the API request wasn't successful
            } else {
                throw new Exception(Mage::helper('webpos')->__('Transaction Failed'));
            }
        } else {
            throw new Exception(Mage::helper('webpos')->__('Transaction Failed'));
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
        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        $merchantAuthentication = new apiContract\MerchantAuthenticationType();
        $merchantAuthentication->setName('5pAk2L3g');
        $merchantAuthentication->setTransactionKey('6j3LNtQz3639RpKK');

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
        $transactionRequestType->setTransactionType( "authOnlyTransaction");
        $transactionRequestType->setAmount(100);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $request = new apiContract\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setTransactionRequest( $transactionRequestType);
//        Zend_Debug::dump($request);die;
        $controller = new apiController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse( apiConstants\ANetEnvironment::SANDBOX);
        if ($response != null)
        {
            if($response->getMessages()->getResultCode() == 'Ok')
            {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }
}