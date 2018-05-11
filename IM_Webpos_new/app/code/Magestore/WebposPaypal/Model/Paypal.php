<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaypal\Model;

use Magento\Framework\Exception\StateException;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

use PayPal\Api\Address;
use PayPal\Api\BillingInfo;
use PayPal\Api\Cost;
use PayPal\Api\Currency;
use PayPal\Api\Invoice;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\PaymentTerm;
use PayPal\Api\Phone;
use PayPal\Api\Tax;
use PayPal\Api\ShippingInfo;
use PayPal\Api\ShippingCost;
use PayPal\Api\PaymentSummary;
use PayPal\Api\PaymentDetail;
use PayPal\Api\OpenIdTokeninfo;

class Paypal implements \Magestore\WebposPaypal\Api\PaypalInterface
{
    const PAYMENT_METHOD = 'paypal';
    const INTENT = 'sale';

    /**
     * @var \Magestore\WebposPaypal\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig
     */
    protected $resourceConfig;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    protected $cacheTypeList;

    /**
     * Paypal constructor.
     * @param \Magestore\WebposPaypal\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magestore\WebposPaypal\Helper\Data $helper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->helper = $helper;
        $this->url = $url;
        $this->resourceConfig = $resourceConfig;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * @return bool
     */
    public function validateRequiredSDK(){
        return (class_exists("\\PayPal\\Rest\\ApiContext") && class_exists("\\PayPal\\Auth\\OAuthTokenCredential"))?true:false;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key = ''){
        $configs = $this->helper->getPaypalConfig();
        return ($key)?$configs[$key]:$configs;
    }

    /**
     * @return \PayPal\Rest\ApiContext
     */
    public function getApiContext(){
        $clientId = $this->getConfig('client_id');
        $clientSecret = $this->getConfig('client_secret');
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
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
     * @param string $successUrl
     * @param string $cancelUrl
     * @param \PayPal\Api\Transaction[] $transactions
     * @return string
     * @throws \Exception
     */
    public function createPayment($successUrl, $cancelUrl, $transactions){
        $apiContext = $this->getApiContext();

        $payer = new Payer();
        $payer->setPaymentMethod(self::PAYMENT_METHOD);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($successUrl)
            ->setCancelUrl($cancelUrl);

        $payment = new Payment();
        $payment->setIntent(self::INTENT)
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions($transactions);

        $url = '';
        try {
            $payment->create($apiContext);
            $approvalUrl = $payment->getApprovalLink();
            if($approvalUrl){
                $url = $approvalUrl;
            }
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
        return $url;
    }

    /**
     * @param string $subtotal
     * @param string $shipping
     * @param string $tax
     * @param string $total
     * @param string $currencyCode
     * @param string $description
     * @return \PayPal\Api\Transaction
     */
    public function createTransaction($subtotal, $shipping, $tax, $total, $currencyCode, $description = ''){
        $amount = new Amount();
        $amount->setCurrency($currencyCode)
            ->setTotal($total);

        if($subtotal > 0 || $shipping > 0 || $tax > 0){
            $details = new Details();
            $details->setShipping($shipping)
                ->setTax($tax)
                ->setSubtotal($subtotal);
            $amount->setDetails($details);
        }

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription($description);
        return $transaction;
    }

    /**
     * @param string $paymentId
     * @param string $payerId
     * @return string
     * @throws \Exception
     */
    public function completePayment($paymentId, $payerId){
        $apiContext = $this->getApiContext();
        $payment = Payment::get($paymentId, $apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        $transactionId = '';
        try {
            $payment->execute($execution, $apiContext);
            $transactions = $payment->getTransactions();
            if(!empty($transactions) && isset($transactions[0])){
                $relatedResources = $transactions[0]->getRelatedResources();
                if(!empty($relatedResources) && isset($relatedResources[0])){
                    $sale = $relatedResources[0]->getSale();
                    $transactionId = $sale->getId();
                }
            }
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
        return $transactionId;
    }

    /**
     * @param string $paymentId
     * @return string
     * @throws \Exception
     */
    public function completeAppPayment($paymentId){
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
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
        return $transactionId;
    }

    /**
     * @return bool
     */
    public function canConnectToApi(){
        $context = $this->getApiContext();
        $params = array('count' => 1, 'start_index' => 0);
        $connected = true;
        try{
            Payment::all($params, $context);
        }catch (\Exception $e){
            $connected = false;
        }
        return $connected;
    }

    /**
     * @param \PayPal\Api\MerchantInfo $merchantInfo
     * @param \PayPal\Api\BillingInfo $billingInfo
     * @param \PayPal\Api\ShippingInfo $shippingInfo
     * @param \PayPal\Api\PaymentTerm $paymentTerm
     * @param \PayPal\Api\InvoiceItem[] $items
     * @param string $note
     * @return \PayPal\Api\Invoice
     * @throws \Exception
     */
    public function createInvoiceObject($merchantInfo, $billingInfo, $shippingInfo, $paymentTerm, $items, $note = ''){
        $logo = $this->helper->getLogoUrl();
        $invoice = new Invoice();
        $invoice->setMerchantInfo($merchantInfo)
            ->setBillingInfo(array($billingInfo))
            ->setNote($note)
            ->setPaymentTerm($paymentTerm)
            ->setShippingInfo($shippingInfo)
            ->setItems($items);
        if($logo){
            $invoice->setLogoUrl($logo);
        }
        return $invoice;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return mixed
     * @throws \Exception
     */
    public function createInvoice($invoice){
        $apiContext = $this->getApiContext();
        try {
            $invoice->create($apiContext);
        } catch (\Exception $e) {
            throw $e;
        }
        return $invoice;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return \PayPal\Api\Invoice
     * @throws \Exception
     */
    public function createInvoiceAndSend($invoice){
        $apiContext = $this->getApiContext();
        try {
            $invoice->create($apiContext);
            $invoice->send($apiContext);
        } catch (\Exception $e) {
            throw $e;
        }
        return $invoice;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return \Magestore\WebposPaypal\Model\Paypal
     * @throws \Exception
     */
    public function sendInvoice($invoice){
        try {
            $apiContext = $this->getApiContext();
            $invoice->send($apiContext);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * @param string $invoiceId
     * @return \Magestore\WebposPaypal\Model\Paypal
     * @throws \Exception
     */
    public function sendInvoiceById($invoiceId){
        try {
            $apiContext = $this->getApiContext();
            $invoice = Invoice::get($invoiceId, $apiContext);
            $invoice->send($apiContext);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return string
     * @throws \Exception
     */
    public function getInvoiceQrCode($invoice){
        try {
            $apiContext = $this->getApiContext();
            $image = $invoice->qrCode($invoice->getId(), array(), $apiContext);
            $qrCode =  $image->getImage();
        } catch (\Exception $e) {
            throw $e;
        }
        return $qrCode;
    }

    /**
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @param string $businessName
     * @param \PayPal\Api\Phone $phone
     * @param \PayPal\Api\Address $address
     * @return \PayPal\Api\MerchantInfo
     */
    public function createMerchantInfo($email, $firstname, $lastname, $businessName, $phone, $address){
        $merchantInfo = new MerchantInfo();
        $merchantInfo->setEmail($email)
            ->setFirstName($firstname)
            ->setLastName($lastname)
            ->setBusinessName($businessName)
            ->setPhone($phone)
            ->setAddress($address);
        return $merchantInfo;
    }

    /**
     * @param string $countryCode
     * @param string $number
     * @return \PayPal\Api\Phone
     */
    public function createPhone($countryCode, $number){
        $phone = new Phone();
        $phone->setCountryCode($countryCode)
            ->setNationalNumber($number);
        return $phone;
    }

    /**
     * @param string $line1
     * @param string $line2
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $countryCode
     * @return \PayPal\Api\Address
     */
    public function createAddress($line1, $line2, $city, $state, $postalCode, $countryCode){
        $address = new Address();
        $address->setLine1($line1)->setLine2($line2)
            ->setCity($city)
            ->setState($state)
            ->setPostalCode($postalCode)
            ->setCountryCode($countryCode);
        return $address;
    }

    /**
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @param string $businessName
     * @param \PayPal\Api\Phone $phone
     * @param string $addtionalInfo
     * @param \PayPal\Api\InvoiceAddress $invoiceAddress
     * @return \PayPal\Api\BillingInfo
     */
    public function createBillingInfo($email, $firstname, $lastname, $businessName, $phone, $addtionalInfo = '', $invoiceAddress){
        $billing = new BillingInfo();
        $billing->setEmail($email);
        $billing->setFirstName($firstname);
        $billing->setLastName($lastname);
        $billing->setPhone($phone);

        $billing->setBusinessName($businessName)
            ->setAdditionalInfo($addtionalInfo)
            ->setAddress($invoiceAddress);

        return $billing;
    }

    /**
     * @param string $line1
     * @param string $line2
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $countryCode
     * @return \PayPal\Api\InvoiceAddress
     */
    public function createInvoiceAddress($line1, $line2, $city, $state, $postalCode, $countryCode){
        $address = new InvoiceAddress();
        $address->setLine1($line1)->setLine2($line2)
            ->setCity($city)
            ->setState($state)
            ->setPostalCode($postalCode)
            ->setCountryCode($countryCode);
        return $address;
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $businessName
     * @param \PayPal\Api\Phone $phone
     * @param \PayPal\Api\InvoiceAddress $invoiceAddress
     * @return \PayPal\Api\ShippingInfo
     */
    public function createShippingInfo($firstname, $lastname, $businessName, $phone, $invoiceAddress){
        $shipping = new ShippingInfo();
        $shipping->setFirstName($firstname)
            ->setLastName($lastname)
            ->setBusinessName($businessName)
            ->setPhone($phone)
            ->setAddress($invoiceAddress);
        return $shipping;
    }

    /**
     * @param string $termType
     * @param string $dueDate
     * @return \PayPal\Api\PaymentTerm
     */
    public function createPaymentTerm($termType, $dueDate = ''){
        $paymentTerm = new PaymentTerm();
        if($termType){
            $paymentTerm->setTermType($termType);
        }else{
            if($dueDate){
                $paymentTerm->setDueDate($dueDate);
            }
        }
        return $paymentTerm;
    }

    /**
     * @param string $percent
     * @return \PayPal\Api\Cost
     */
    public function createPercentCost($percent){
        $cost = new Cost();
        $cost->setPercent($percent);
        return $cost;
    }

    /**
     * @param \PayPal\Api\Currency $amount
     * @return \PayPal\Api\Cost
     */
    public function createFixedCost($amount){
        $cost = new Cost();
        $cost->setAmount($amount);
        return $cost;
    }

    /**
     * @param string $currencyCode
     * @param string|float $value
     * @return \PayPal\Api\Currency
     */
    public function createCurrency($currencyCode, $value){
        $currency = new Currency();
        $currency->setCurrency($currencyCode);
        $currency->setValue($value);
        return $currency;
    }

    /**
     * @param string $percent
     * @param string $name
     * @return \PayPal\Api\Tax
     */
    public function createPercentTax($percent, $name = ''){
        $tax = new Tax();
        $tax->setPercent($percent)->setName($name);
        return $tax;
    }

    /**
     * @param \PayPal\Api\Currency $amount
     * @param string $name
     * @return \PayPal\Api\Tax
     */
    public function createFixedTax($amount, $name = ''){
        $tax = new Tax();
        $tax->setAmount($amount)->setName($name);
        return $tax;
    }

    /**
     * @param string $name
     * @param string $qty
     * @param \PayPal\Api\Currency $unitPrice
     * @return \PayPal\Api\InvoiceItem
     */
    public function createInvoiceItem($name, $qty, $unitPrice){
        $item = new InvoiceItem();
        $item->setName($name)
            ->setQuantity($qty)
            ->setUnitPrice($unitPrice);
        return $item;
    }

    /**
     * @param \PayPal\Api\Currency $other
     * @return \PayPal\Api\PaymentSummary
     */
    public function createPaymentSummary($other){
        $paymentSummary = new PaymentSummary();
        $paymentSummary->setOther($other);
        return $paymentSummary;
    }

    /**
     * @param \PayPal\Api\Currency $amount
     * @param \PayPal\Api\Tax $tax
     * @return \PayPal\Api\ShippingCost
     */
    public function createShippingCost($amount, $tax){
        $shippingCost = new ShippingCost();
        $shippingCost->setAmount($amount);
        $shippingCost->setTax($tax);
        return $shippingCost;
    }

    /**
     * @param string $invoiceId
     * @return \PayPal\Api\Invoice
     * @throws \Exception
     */
    public function getInvoice($invoiceId){
        try {
            $apiContext = $this->getApiContext();
            $invoice = Invoice::get($invoiceId, $apiContext);
        } catch (\Exception $e) {
            throw $e;
        }
        return $invoice;
    }

    /**
     * @param \PayPal\Api\Currency $amount
     * @param string $note
     * @param string $method
     * @return \PayPal\Api\PaymentDetail
     */
    public function createPaymentDetail($amount, $note = "", $method = "OTHER"){
        $paymentDetail = new PaymentDetail();
        $paymentDetail->setMethod($method);//["BANK_TRANSFER", "CASH", "CHECK", "CREDIT_CARD", "DEBIT_CARD", "PAYPAL", "WIRE_TRANSFER", "OTHER"]
        $paymentDetail->setNote($note);
        $paymentDetail->setAmount($amount);
        return $paymentDetail;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @param \PayPal\Api\PaymentDetail $paymentDetail
     * @return $this
     * @throws \Exception
     */
    public function recordPaymentForInvoice($invoice, $paymentDetail){
        try {
            $apiContext = $this->getApiContext();
            $invoice->recordPayment($paymentDetail,$apiContext);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * get token info
     *
     * @return string
     * @throws \Magento\Framework\Exception\StateException
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

    /**
     * get access token
     *
     * @return string
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getAccessToken(){
        $apiContext = $this->getApiContext();
        $clientId = $this->getConfig('client_id');
        $clientSecret = $this->getConfig('client_secret');
        $refreshToken = $this->getConfig('refresh_token');
        if(!$refreshToken) {
            throw new StateException(_('Refresh token was missing'));
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
                $this->resourceConfig->saveConfig(
                    'webpos/payment/paypal/access_token',
                    $accessToken,
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID
                );
                $this->cacheTypeList->cleanType('config');
                return $accessToken;
            }
        }
        throw new StateException(_('Cannot generate new access token'));
    }
}
