<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Model;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\OrderRepository;
use Paynl\Error\Error;

class PaynlService implements \Magestore\WebposPaynl\Api\PaynlServiceInterface
{
    /**
     * @var \Magestore\WebposPaynl\Api\PaynlInterface
     */
    protected $paynl;

    /**
     * @var \Magestore\WebposPaynl\Api\Data\InvoiceInterface
     */
    protected $invoiceData;

    /**
     * @var \Magestore\WebposPaynl\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\WebposPaynl\Model\Source\PhoneCode
     */
    protected $phoneSource;

    /**
     * @var string
     */
    protected $successUrl;

    /**
     * @var string
     */
    protected $cancelUrl;


    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /** @var \Magento\Sales\Api\Data\OrderInterface $order **/

    protected $order;

    /**
     * @var \Paynl\Payment\Model\Config
     */
    protected $config;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;


    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var
     */
    protected $paynlPayment;

    /**
     * PaynlService constructor.
     * @param \Magestore\WebposPaynl\Api\PaynlInterface $paynl
     * @param \Magestore\WebposPaynl\Helper\Data $helper
     * @param Source\PhoneCode $phoneSource
     * @param \Magestore\WebposPaynl\Api\Data\InvoiceInterface $invoiceData
     */
    public function __construct(
        \Magestore\WebposPaynl\Api\PaynlInterface $paynl,
        \Magestore\WebposPaynl\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magestore\WebposPaynl\Model\Config $config,
        PaymentHelper $paymentHelper,
        QuoteRepository $quoteRepository,
        OrderRepository $orderRepository,
        \Magestore\WebposPaynl\Model\Instore $paynlPayment
    ) {
        $this->paynl = $paynl;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->config          = $config;
        $this->paymentHelper   = $paymentHelper;
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->paynlPayment = $paynlPayment;
        $this->initDefaultData();
    }

    /**
     * Init default data
     * @return \Magestore\WebposPaynl\Api\PaynlServiceInterface
     */
    public function initDefaultData(){
        $successUrl = $this->helper->getUrl('webpospaynl/payment/success');
        $cancelUrl = $this->helper->getUrl('webpospaynl/payment/cancel');
        $this->setSuccessUrl($successUrl);
        $this->setCancelUrl($cancelUrl);
        return $this;
    }

    /**
     * Get success url
     *
     * @api
     * @return string|null
     */
    public function getSuccessUrl(){
        return $this->successUrl;
    }

    /**
     * Set success url
     *
     * @api
     * @param string $url
     * @return $this
     */
    public function setSuccessUrl($url){
        $this->successUrl = $url;
    }

    /**
     * Get cancel url
     *
     * @api
     * @return string|null
     */
    public function getCancelUrl(){
        return $this->cancelUrl;
    }

    /**
     * Set cancel url
     *
     * @api
     * @param string $url
     * @return $this
     */
    public function setCancelUrl($url){
        $this->cancelUrl = $url;
    }

    /**
     * @return bool
     */
    public function isEnable(){
        $hasSDK = $this->paynl->validateRequiredSDK();
        $configs = $this->paynl->getConfig();
        return ($hasSDK && $configs['enable'] && !empty($configs['client_id']) && !empty($configs['client_secret']))?true:false;
    }

    /**
     * @return string
     */
    public function getConfigurationError(){
        $message = '';
        $hasSDK = $this->paynl->validateRequiredSDK();
        $configs = $this->paynl->getConfig();
        if(!$hasSDK){
            $message = __('Paynl SDK not found, please go to the configuration to get the instruction to install the SDK');
        }else{
            if($configs['enable']){
                if(empty($configs['client_id']) || empty($configs['client_secret'])){
                    $message = __('Paynl application client id and client secret are required');
                }
            }else{
                $message = __('Paynl integration is disabled');
            }
        }
        return $message;
    }

    /**
     * Validate configruation
     * @return \Magestore\WebposPaynl\Api\PaynlServiceInterface
     */
    public function validate(){
        $isEnable = $this->isEnable();
        if (!$isEnable) {
            $message = $this->getConfigurationError();
            throw new \Magento\Framework\Exception\LocalizedException(
                __($message)
            );
        }
        return $this;
    }

    /**
     * @param \Magestore\WebposPaynl\Api\Data\TransactionInterface $transaction
     * @return string
     */
    public function startPayment($transaction){
        $this->validate();
        $total = $transaction->getTotal();
        $quoteId = $transaction->getQuoteId();
        $currency = $transaction->getCurrency();
        $bankId = $transaction->getBankId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $options = $objectManager->create('Magento\Quote\Model\Quote');
        $quote = $options->load($quoteId);
        $redirectUrl = $this->paynlPayment->startTransaction($quote, $total, $currency, $bankId);
        $quote->setIsActive(true);
        $this->quoteRepository->save($quote);
        return $redirectUrl;
    }

    /**
     * Return checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * @param string $paymentId
     * @param string $payerId
     * @return string
     */
    public function finishPayment($paymentId, $payerId){
        return $this->paynl->completePayment($paymentId, $payerId);
    }

    /**
     * @param string $paymentId
     * @return string
     */
    public function finishAppPayment($paymentId){
        return $this->paynl->completeAppPayment($paymentId);
    }

    /**
     * @return bool
     */
    public function canConnectToApi(){
        return $this->paynl->canConnectToApi();
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\AddressInterface $billingAddress
     * @param \Magestore\Webpos\Api\Data\Checkout\AddressInterface $shippingAddress
     * @param \Magestore\WebposPaynl\Api\Data\ItemInterface[] $items
     * @param \Magestore\WebposPaynl\Api\Data\TotalInterface[] $totals
     * @param string $totalPaid
     * @param string $currencyCode
     * @param string $note
     * @return \Magestore\WebposPaynl\Api\Data\InvoiceInterface
     */
    public function createAndSendInvoiceToCustomer($billing, $shipping, $items, $totals, $totalPaid, $currencyCode, $note){
        // validate SDK installed and some configuration, thow exception if error
        $this->validate();
        $enable = $this->helper->isAllowCustomerPayWithEmail();
        if($enable){
            // prepare merchant info object
            $merchantInfoConfig = $this->helper->getMerchantInfo();
            $merchantPhoneCode = $this->phoneSource->getPhoneCodeByCountry($merchantInfoConfig['country_id']);
            $merchantPhone = $this->paynl->createPhone($merchantPhoneCode, $merchantInfoConfig['phone']);
            $merchantAddress = $this->paynl->createAddress(
                $merchantInfoConfig['street'],
                '',
                $merchantInfoConfig['city'],
                $merchantInfoConfig['state'],
                $merchantInfoConfig['postal_code'],
                $merchantInfoConfig['country_id']
            );
            $merchantInfo = $this->paynl->createMerchantInfo(
                $merchantInfoConfig['email'],
                $merchantInfoConfig['firstname'],
                $merchantInfoConfig['lastname'],
                $merchantInfoConfig['buisiness_name'],
                $merchantPhone,
                $merchantAddress
            );

            // prepare billing info data
            $billingAddtionalInfo = '';
            $billingStreet = $billing->getStreet();
            $billingAddress = $this->paynl->createInvoiceAddress(
                (isset($billingStreet[0]))?$billingStreet[0]:'',
                (isset($billingStreet[1]))?$billingStreet[1]:'',
                ($billing->getPostcode())?$billing->getCity():__("None"),
                ($billing->getRegion()->getRegion())?$billing->getRegion()->getRegion():__("None"),
                ($billing->getPostcode())?$billing->getPostcode():__("NaN"),
                $billing->getCountryId()
            );
            $billingPhoneCode = $this->phoneSource->getPhoneCodeByCountry($billing->getCountryId());
            $billingPhone = $this->paynl->createPhone($billingPhoneCode, $billing->getTelephone());
            $billingName = $billing->getFirstname().' '.$billing->getLastname();

            //prepare shipping info data
            $shippingStreet = $shipping->getStreet();
            $shippingAddress = $this->paynl->createInvoiceAddress(
                (isset($shippingStreet[0]))?$shippingStreet[0]:'',
                (isset($shippingStreet[1]))?$shippingStreet[1]:'',
                ($shipping->getPostcode())?$shipping->getCity():__("None"),
                ($shipping->getRegion()->getRegion())?$shipping->getRegion()->getRegion():__("None"),
                ($shipping->getPostcode())?$shipping->getPostcode():__("NaN"),
                $shipping->getCountryId()
            );
            $shippingPhoneCode = $this->phoneSource->getPhoneCodeByCountry($shipping->getCountryId());
            $shippingPhone = $this->paynl->createPhone($shippingPhoneCode, $shipping->getTelephone());
            $shippingName = $shipping->getFirstname().' '.$shipping->getLastname();

            // create required objects to create invoice
            $billingInfo = $this->paynl->createBillingInfo($billing->getEmail(), $billing->getFirstname(), $billing->getLastname(), $billingName, $billingPhone, $billingAddtionalInfo, $billingAddress);
            $shippingInfo = $this->paynl->createShippingInfo($shipping->getFirstname(), $shipping->getLastname(), $shippingName, $shippingPhone, $shippingAddress);
            $paymentTerm = $this->paynl->createPaymentTerm("NET_45");
            $invoiceItems = [];
            foreach ($items as $item) {
                $unitPrice = $this->paynl->createCurrency($currencyCode, $item->getUnitPrice());
                $invoiceItem = $this->paynl->createInvoiceItem($item->getName(), $item->getQty(), $unitPrice);
                if($item->getTaxPercent() > 0){
                    $itemTax = $this->paynl->createPercentTax($item->getTaxPercent(), __('Tax'));
                    $invoiceItem->setTax($itemTax);
                }
                $invoiceItems[] = $invoiceItem;
            }

            // create invoiceo object
            $invoice = $this->paynl->createInvoiceObject($merchantInfo, $billingInfo, $shippingInfo, $paymentTerm, $invoiceItems, $note);

            // set some totals data
            if(!empty($totals)){
                $discount = 0;
                foreach ($totals as $total){
                    $amount = $total->getAmount();
                    $code = $total->getCode();
                    if($code == 'grandtotal'){
                        $grandTotal = $this->paynl->createCurrency($currencyCode, $amount);
                        $invoice->setTotalAmount($grandTotal);
                    }
                    if($code == 'shipping' && $amount > 0){
                        $shippingTaxCurrency = $this->paynl->createCurrency($currencyCode, 0);
                        $shippingTax = $this->paynl->createFixedTax($shippingTaxCurrency, __('Tax'));
                        $shippingCurrency = $this->paynl->createCurrency($currencyCode, $amount);
                        $shippingCost = $this->paynl->createShippingCost($shippingCurrency, $shippingTax);
                        $invoice->setShippingCost($shippingCost);
                    }
                    if($amount && $amount < 0){
                        $discount -= floatval($amount);
                    }
                }
                if($discount > 0){
                    $discountCurrency = $this->paynl->createCurrency($currencyCode, $discount);
                    $discountCost = $this->paynl->createFixedCost($discountCurrency);
                    $invoice->setDiscount($discountCost);
                }
            }

            // set tax configuration
            $taxCalculatedAfterDiscount = $this->helper->isTaxCalculatedAfterDiscount();
            $invoice->setTaxCalculatedAfterDiscount($taxCalculatedAfterDiscount);

            // sync invoice object to server and send email to payer
            $invoice = $this->paynl->createInvoice($invoice);

            // set total paid for invoice
            if(!empty($totalPaid)){
                $totalPaid = $this->paynl->createCurrency($currencyCode, $totalPaid);
                $paymentDetail = $this->paynl->createPaymentDetail($totalPaid, __("Paid via Magento POS system"));
                $this->paynl->recordPaymentForInvoice($invoice, $paymentDetail);
            }

            $this->paynl->sendInvoice($invoice);
            return $this->getInvoiceData($invoice);
        }
        return $this->getInvoiceData(false);
    }

    /**
     * @param $invoice
     */
    protected function getInvoiceData($invoice){
        $data = $this->invoiceData;
        if($invoice instanceof \PayPal\Api\Invoice){
            $data->setId($invoice->getId());
            $data->setNumber($invoice->getNumber());
            $data->setQrCode($this->paynl->getInvoiceQrCode($invoice));
        }else{
            $data->setId('');
            $data->setNumber('');
            $data->setQrCode('');
        }
        return $data;
    }

    /**
     * @param string $invoiceId
     * @return \PayPal\Api\Invoice
     * @throws \Exception
     */
    public function getInvoice($invoiceId){
        return $this->paynl->getInvoice($invoiceId);
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return bool
     */
    public function isInvoicePaid($invoice){
        $paidStatus = ['PAID', 'MARKED_AS_PAID', 'PARTIALLY_PAID'];
        $status = $invoice->getStatus();
        return (in_array($status, $paidStatus))?true:false;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return bool
     */
    public function isInvoiceCancelled($invoice){
        $cancelledStatus = ['CANCELLED'];
        $status = $invoice->getStatus();
        return (in_array($status, $cancelledStatus))?true:false;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return bool
     */
    public function isInvoiceRefunded($invoice){
        $refundedStatus = ['REFUNDED', 'PARTIALLY_REFUNDED', 'MARKED_AS_REFUNDED'];
        $status = $invoice->getStatus();
        return (in_array($status, $refundedStatus))?true:false;
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return \PayPal\Api\PaymentSummary
     */
    public function getInvoicePaidAmount($invoice){
        return $invoice->getPaidAmount();
    }

    /**
     * @param \PayPal\Api\Invoice $invoice
     * @return \PayPal\Api\PaymentSummary
     */
    public function getInvoiceRefundedAmount($invoice){
        return $invoice->getRefundedAmount();
    }

    /**
     * @return string
     */
    public function getAccessToken(){
        return $this->paynl->getAccessToken();
    }
}
