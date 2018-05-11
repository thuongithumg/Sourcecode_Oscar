<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Checkout;

use Magento\Catalog\Model\Product\Exception;
use Magento\Framework\Exception\StateException;
use Magestore\Webpos\Model\Checkout\Data\Payment;
use Magestore\Webpos\Model\Checkout\Data\PaymentItem;
use Magestore\Webpos\Model\Checkout\Data\CartItem;
use Magestore\Webpos\Model\Checkout\Data\ExtensionData;
use Magestore\Webpos\Model\Checkout\Data\SessionData;

/**
 * Order create model
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Create extends \Magento\Sales\Model\AdminOrder\Create implements \Magestore\Webpos\Api\Checkout\CartInterface
{

    /**
     * @var array
     */
    protected $quoteInitData = array();

    /**
     * @var \Magento\Quote\Api\GuestShipmentEstimationInterface
     */
    protected $guestShipmentEstimationInterface = array();

    /**
     * Add multiple products to current order quote
     *
     * @param array $products
     * @return $this
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productConfig) {
            $productConfig['qty'] = isset($productConfig['qty']) ? (double)$productConfig['qty'] : 1;
            try {
                $this->addProduct($productConfig[CartItem::KEY_ID], $productConfig);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        }

        return $this;
    }

    /**
     * Add product to current order quote
     * $product can be either product id or product model
     * $config can be either buyRequest config, or just qty
     *
     * @param int|\Magento\Catalog\Model\Product $webposProduct
     * @param array|float|int|\Magento\Framework\DataObject $productConfig
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addProduct($webposProduct, $productConfig = 1)
    {
        if (!is_array($productConfig) && !$productConfig instanceof \Magento\Framework\DataObject) {
            $productConfig = ['qty' => $productConfig];
        }


        $productConfig = new \Magento\Framework\DataObject($productConfig);

        if (!$webposProduct instanceof \Magento\Catalog\Model\Product) {
            $productId = $webposProduct;
            $webposProduct = $this->_objectManager->create(
                'Magento\Catalog\Model\Product'
            )->setStore(
                $this->getSession()->getStore()
            )->setStoreId(
                $this->getSession()->getStoreId()
            )->load(
                $webposProduct
            );
            if (!$webposProduct->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We could not add a product to cart by the ID "%1".', $productId)
                );
            }
        }

        $webposProduct->setData('salable', true);


        $item = $this->quoteInitializer->init($this->getQuote(), $webposProduct, $productConfig);

        if (is_string($item)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item));
        }

        $item->checkData();

        if ($productConfig->getData(CartItem::KEY_CUSTOM_PRICE) || $productConfig->getData(CartItem::KEY_CUSTOM_PRICE) == 0) {
            $customPrice = $productConfig->getData(CartItem::KEY_CUSTOM_PRICE);
            $item->setCustomPrice($customPrice);
            $item->setOriginalCustomPrice($customPrice);
            $item->getProduct()->setIsSuperMode(true);
        }
        if ($productConfig->getData(CartItem::KEY_IS_CUSTOM_SALE)) {
            $options = $productConfig->getData(CartItem::KEY_CUSTOM_OPTION);
            if (isset($options['name'])) {
                $item->setName($options['name']);
            }
            if (isset($options['tax_class_id'])) {
                $item->getProduct()->setTaxClassId($options['tax_class_id']);
            }
        }
        if ($productConfig->getData(CartItem::KEY_QTY_TO_SHIP) > 0) {
            $this->_addItemToShip($item->getItemId(), $productConfig->getData(CartItem::KEY_QTY_TO_SHIP));
        }
        $checkPromotion = $this->getSession()->getData('checking_promotion');
        if (!$checkPromotion) {
            $item->setNoDiscount(true);
        }
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Retrieve quote object model
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return parent::getQuote();
    }

    /**
     * Set quote object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        return parent::setQuote($quote);
    }

    /**
     * Quote saving
     *
     * @return $this
     */
    public function saveQuote()
    {
        return parent::saveQuote();
    }


    /**
     * Validate quote data before order creation
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _validate()
    {
        return $this;
    }

    /**
     *
     * @param string $shippingMethod
     * @return \Magestore\Webpos\Model\Checkout\Create
     */
    protected function _saveShippingMethod($shippingMethod)
    {
        $quote = $this->getQuote();
        if (!$quote->isVirtual() && $this->getShippingAddress()->getSameAsBilling()) {
            $this->setShippingAsBilling(1);
        }
        $this->setShippingMethod($shippingMethod);
        $this->collectShippingRates();
        return $this;
    }

    /**
     *
     * @param \Magento\Quote\Api\Data\PaymentInterface $payment
     * @return \Magestore\Webpos\Model\Checkout\Create
     */
    public function _savePaymentData($payment)
    {
        $quote = $this->getQuote();
        $data = [];

        if (isset($payment[Payment::KEY_METHOD])) {
            $data['method'] = $payment[Payment::KEY_METHOD];
            $quote->getPayment()->addData($data);
            $quote->getPayment()->unsAdditionalInformation();
        }
        $dataMapping = [
            'cc_owner' => 'cc_owner',
            'cc_type' => 'cc_type',
            'cc_number' => 'cc_number_enc',
            'cc_cid' => 'cc_cid_enc',
            'cc_owner' => 'cc_owner',
            'cc_exp_month' => 'cc_exp_month',
            'cc_exp_year' => 'cc_exp_year',
        ];
        $additional_information = [];
        if (isset($payment[Payment::KEY_DATA]) && count($payment[Payment::KEY_DATA]) > 0) {
            $modelCurrency = $this->_objectManager->create("Magento\Framework\Locale\Currency");
            foreach ($payment[Payment::KEY_DATA] as $methodData) {
//                if (isset($methodData[PaymentItem::KEY_CODE]) && isset($methodData[PaymentItem::KEY_REFERENCE_NUMBER])) {
//                    $data[$methodData[PaymentItem::KEY_CODE].'_ref_no'] = $methodData[PaymentItem::KEY_REFERENCE_NUMBER];
//                }
                $code = $methodData[PaymentItem::KEY_CODE];

                $paymentModel = $this->getPaymentModelByCode($this->getPaymentModelCode($code));
                if ($paymentModel) {
                    if (isset($methodData[PaymentItem::KEY_ADDITIONAL_DATA]) &&
                        $methodData[PaymentItem::KEY_CODE] == 'cryozonic_stripe'
                    ) {
                        $token = $paymentModel->getPaymentToken(
                            $methodData[PaymentItem::KEY_ADDITIONAL_DATA]);
                        $quote->getPayment()->setAdditionalInformation('token', $token);
                    }
                    if (isset($methodData[PaymentItem::KEY_ADDITIONAL_DATA])) {
                        foreach ($methodData[PaymentItem::KEY_ADDITIONAL_DATA] as $key => $value) {
                            $quote->getPayment()->setAdditionalInformation($key, $value);
                            if (isset($dataMapping[$key])) {
                                $quote->getPayment()->setData($dataMapping[$key], $value);
                            }
                        }
                        if($methodData[PaymentItem::KEY_CODE] == 'payflowpro_integration') {
                            $quote->getPayment()->setAdditionalInformation('hasPaypalPro', 'payflowpro_integration');
                            $quote->getPayment()->setAdditionalInformation('paypalProAmount', (float)$methodData['real_amount']);
                        }
                    }
                }
//                $additional_information[] = $modelCurrency->getCurrency($session->getCurrencyId())
//                        ->toCurrency($methodData->getAmount()).' : '.$methodData->getTitle();
            }
        }
//        if(count($additional_information)>0)
//            $data['additional_information'] = $additional_information;
//        $quote->getPayment()->addData($data);
        return $this;
    }

    /**
     *
     * @param type $couponCode
     * @return \Magestore\Webpos\Model\Checkout\Create
     */
    protected function _setCouponCode($couponCode)
    {
        $quote = $this->getQuote();
        if ($couponCode) {
            $quote->setCouponCode($couponCode);
        }
        if ($quote->getCouponCode()) {
            $quote->collectTotals();
        }
        return $this;
    }

    /**
     *
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @return \Magestore\Webpos\Model\Checkout\Create
     */
    protected function _processCart($items)
    {
        if (isset($items) && count($items) > 0) {
            $products = [];
            foreach ($items as $item) {
                $product = [];
                $product[CartItem::KEY_ID] = $item->getId();
                $product[CartItem::KEY_QTY] = $item->getQty();
                $product[CartItem::KEY_CUSTOM_PRICE] = $item->getCustomPrice();
                $product[CartItem::KEY_SUPER_ATTRIBUTE] = $item->getSuperAttribute();
                $product[CartItem::KEY_SUPER_GROUP] = $item->getSuperGroup();
                $product[CartItem::KEY_BUNDLE_OPTION] = $item->getBundleOption();
                $product[CartItem::KEY_BUNDLE_OPTION_QTY] = $item->getBundleOptionQty();
                $product[CartItem::KEY_CUSTOM_OPTION] = $item->getOptions();
                $product[CartItem::KEY_IS_CUSTOM_SALE] = $item->getIsCustomSale();
                $product[CartItem::KEY_EXTENSION_DATA] = serialize($item->getExtensionData());
                $product[CartItem::CUSTOMERCREDIT_AMOUNT] = $item->getAmount();
                $product[CartItem::ITEM_ID] = $item->getItemId();

                $product[CartItem::GIFTCARD_AMOUNT] = $item->getAmount();
                $product[CartItem::GIFTCARD_TEMPLATE_ID] = $item->getGiftcardTemplateId();
                $product[CartItem::GIFTCARD_TEMPLATE_IMAGE] = $item->getGiftcardTemplateImage();
                $product[CartItem::GIFTCARD_MESSAGE] = $item->getMessage();
                $product[CartItem::GIFTCARD_RECIPIENT_NAME] = $item->getRecipientName();
                $product[CartItem::GIFTCARD_RECIPIENT_EMAIL] = $item->getRecipientEmail();
                $product[CartItem::GIFTCARD_SEND_FRIEND] = $item->getSendFriend();
                $product[CartItem::GIFTCARD_DAY_TO_SEND] = $item->getDayToSend();
                $product[CartItem::GIFTCARD_TIMEZONE_TO_SEND] = $item->getTimezoneToSend();
                $product[CartItem::GIFTCARD_RECIPIENT_ADDRESS] = $item->getRecipientAddress();
                $product[CartItem::GIFTCARD_NOTIFY_SUCCESS] = $item->getNotifySuccess();
                $product[CartItem::GIFTCARD_RECIPIENT_SHIP] = $item->getRecipientShip();


                $products[] = $product;
            }
            $this->addProducts($products);
        }
        return $this;
    }

    /**
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $note
     */
    protected function _saveOrderComment($order, $note)
    {
        if ($order instanceof \Magento\Sales\Model\Order && $note) {
            $order->addStatusHistoryComment($note);
            $order->setCustomerNote($note);
            $order->save();
        }
    }

    /**
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $data
     */
    protected function _savePaymentsToOrder($order, $data)
    {

        if ($order instanceof \Magento\Sales\Model\Order) {
            if (count($data) > 0) {
                foreach ($data as $payment) {
                    if (isset($payment[PaymentItem::KEY_CODE])) {
                        $orderPayment = $this->_objectManager->create("Magestore\Webpos\Model\Payment\OrderPayment");
                        $orderPayment->setData([
                            "order_id" => $order->getId(),
                            "real_amount" => ($payment[PaymentItem::KEY_IS_PAYLATER]) ? 0 : $payment[PaymentItem::KEY_REAL_AMOUNT],
                            "base_real_amount" => ($payment[PaymentItem::KEY_IS_PAYLATER]) ? 0 : $payment[PaymentItem::KEY_BASE_REAL_AMOUNT],
                            "payment_amount" => ($payment[PaymentItem::KEY_IS_PAYLATER]) ? 0 : $payment[PaymentItem::KEY_AMOUNT],
                            "base_payment_amount" => ($payment[PaymentItem::KEY_IS_PAYLATER]) ? 0 : $payment[PaymentItem::KEY_BASE_AMOUNT],
                            "method" => $payment[PaymentItem::KEY_CODE],
                            "method_title" => $payment[PaymentItem::KEY_TITLE],
                            "shift_id" => $payment[PaymentItem::KEY_SHIFT_ID],
                            "reference_number" => $payment[PaymentItem::KEY_REFERENCE_NUMBER],
                            "card_type" =>  $payment[PaymentItem::KEY_CARD_TYPE]
                        ]);
                        $orderPayment->save();
                    }
                }
            }
        }
    }

    /**
     *
     * @param array $data
     * @return array $paidPayment
     */
    protected function _getPaidPayment($data)
    {
        $paidPayment = [
            'amount' => 0,
            'base_amount' => 0
        ];
        if (count($data) > 0) {
            $amount = $base_amount = 0;
            foreach ($data as $payment) {
                if (isset($payment[PaymentItem::KEY_CODE])) {
                    $amount += ($payment[PaymentItem::KEY_IS_PAYLATER]) ? 0 : $payment[PaymentItem::KEY_AMOUNT];
                    $base_amount += ($payment[PaymentItem::KEY_IS_PAYLATER]) ? 0 : $payment[PaymentItem::KEY_BASE_AMOUNT];
                }
            }
            $paidPayment['amount'] = $amount;
            $paidPayment['base_amount'] = $base_amount;
        }
        return $paidPayment;
    }

    /**
     *
     * @param string $itemId
     * @param string $qty
     */
    public function _addItemToShip($itemId, $qty)
    {
        $session = $this->getSession();
        $itemToShip = ($session->getData('items_to_ship')) ? $session->getData('items_to_ship') : [];
        $itemToShip[$itemId] = $qty;
        $session->setData('items_to_ship', $itemToShip);
    }

    /**
     *
     * @param string $orderId
     * @param \Magento\Sales\Model\Order $order
     * @param boolean $createInvoice
     * @param boolean $createShipment
     * @param array $itemsToShip
     * @param \Magestore\Webpos\Model\Checkout\Data\ShippingTrack[] $tracks
     */
    public function processInvoiceAndShipment($orderId, $order, $createInvoice, $createShipment, $itemsToShip, $tracks)
    {
        if (isset($itemsToShip) && !empty($itemsToShip)) {
            $items_to_ship = [];
            $items = $order->getAllItems();
            foreach ($items as $item) {
                if (isset($itemsToShip[$item->getQuoteItemId()])) {
                    $items_to_ship[$item->getItemId()] = $itemsToShip[$item->getQuoteItemId()];
                }
            }
        } else {
            $items_to_ship = [];
        }
        $helperOrder = $this->_objectManager->create('Magestore\Webpos\Helper\Order');
        $helperOrder->createShipmentAndInvoice($orderId, $order, $createInvoice, $createShipment, $items_to_ship, $tracks);
    }

    /**
     *
     * @return array
     */
    public function getTotals()
    {
        $quote = $this->getQuote();
        $quoteTotals = $quote->getTotals();
        $totals = [];
        foreach ($quoteTotals as $code => $total) {
            $totals[$code] = $total->getData();
        }
        return $totals;
    }

    /**
     *
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function submitQuote($quoteId, $payment, $shipping, $couponCode, $config, $extensionData, $sessionData, $integration)
    {
        $session = $this->getSession();
        $this->_coreRegistry->register('webpos_extra_order_data', $extensionData);
        $order = $this->createOrderByQuote($quoteId, $payment, $shipping, $couponCode, $config, $extensionData, $sessionData, $integration);
        if ($order) {
            if ($config->getNote()) {
                $this->_saveOrderComment($order, $config->getNote());
            }
            if ($shipping->getDateTime()) {
                $order->setData('webpos_delivery_date', $shipping->getDateTime());
            }

            $order->save();
            if (isset($payment[Payment::KEY_DATA])) {
                $this->_savePaymentsToOrder($order, $payment[Payment::KEY_DATA]);
            }
            $orderRepository = $this->_objectManager->create("Magestore\Webpos\Api\Sales\OrderRepositoryInterface");
            $order = $orderRepository->get($order->getId());
            $session->clearStorage();

            $this->_coreRegistry->unregister('webpos_extra_order_data');
        } else {
            $syncedOrder = $this->getSyncOrder($extensionData);
            if ($syncedOrder !== false) {
                $orderRepository = $this->_objectManager->create("Magestore\Webpos\Api\Sales\OrderRepositoryInterface");
                $order = $orderRepository->get($syncedOrder);
            }
        }
        return $order;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function isProcessPaymentBeforeOrder($payment)
    {
        $result = false;
        $list = ['payflowpro_integration'];
        if(in_array($payment['method'], $list)) {
            $result = $payment['method'];
        } elseif($payment['method'] == 'multipaymentforpos') {
            foreach($payment['method_data'] as $item) {
                if(in_array($item['code'], $list)) {
                    $result = $item['code'];
                }
            }
        }
        return $result;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getPaymentModelCode($code)
    {
        switch ($code) {
            case 'payflowpro_integration':
                $code = 'paypal_payflowpro';
                break;
            case 'paynl_payment_instore':
                $code = 'paynl_payment_instore';
                break;
        }
        return $code;
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @return string
     * @throws \Exception
     */
    public function submitOrder($customerId, $items, $payment, $shipping, $config, $couponCode = "", $extensionData, $sessionData, $integration)
    {
        $session = $this->getSession();
        $paymentMethod = $payment['method'];
        if ($paymentMethod && $processPaymentCode = $this->isProcessPaymentBeforeOrder($payment)) {
            $quote = $this->createQuote($session, $customerId, $items, $payment, $shipping, $config,
                $couponCode, $extensionData, $sessionData, $integration);


            $result = array();
            $result['payment_infomation'] = [];
            $result['payment_model'] = $paymentMethod;
            $result['quote_id'] = $quote->getId();
            $paymentModel = $this->getPaymentModelByCode($this->getPaymentModelCode($processPaymentCode));
            if ($paymentModel) {
                $result['payment_infomation'] = $paymentModel->requestSecureToken($quote);
            }
            $this->_removeSessionData($sessionData);
            return \Zend_Json::encode($result);
        }

        $this->_coreRegistry->register('webpos_extra_order_data', $extensionData);
        $order = $this->createOrderByParams($session, $customerId, $items, $payment, $shipping, $config,
            $couponCode, $extensionData, $sessionData, $integration);

        $eventData = [
            'order' => $order,
            'payment' => $payment
        ];
        $this->_eventManager->dispatch(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_SUBMIT_ORDER_AFTER, $eventData);

        $result = array();
        if ($order) {

            $createInvoice = $config->getCreateInvoice();
            $createShipment = $config->getCreateShipment();
            $paidPayment = [
                'amount' => 0,
                'base_amount' => 0
            ];
            if (isset($payment[Payment::KEY_DATA])) {
                $paidPayment = $this->_getPaidPayment($payment[Payment::KEY_DATA]);
            }
            $itemsToShip = [];
            if ($createShipment == true) {
                $itemsToShip = ($session->getData('items_to_ship')) ? $session->getData('items_to_ship') : [];
                $session->setData('items_to_ship', null);
            }
            try {
                $this->processInvoiceAndShipment($order->getId(), $order, $createInvoice, $createShipment,
                    $itemsToShip, $shipping->getTracks());
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
            if ($config->getNote()) {
                $this->_saveOrderComment($order, $config->getNote());
            }
            if ($shipping->getDateTime()) {
                $order->setData('webpos_delivery_date', $shipping->getDateTime());
            }
            $order->setData('total_paid', $paidPayment['amount']);
            $order->setData('base_total_paid', $paidPayment['base_amount']);
            $order->save();


            if (isset($payment[Payment::KEY_DATA])) {
                $this->_savePaymentsToOrder($order, $payment[Payment::KEY_DATA]);
            }
            $this->_eventManager->dispatch('webpos_order_sync_after', ['order' => $order]);

            $paymentMethod = $payment['method'];
            $result['payment_infomation'] = [];
            $result['payment_model'] = $paymentMethod;
            $result['order_id'] = $order->getId();
            $paymentModel = $this->getPaymentModelByCode($payment['method']);
            if ($paymentModel) {
                $result['payment_infomation'] = $paymentModel->getRequestInformation($order);
            }

            $this->_coreRegistry->unregister('webpos_extra_order_data');
        } else {
            throw new \Exception(__('Cannot create order!'));
        }
        return \Zend_Json::encode($result);
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function prepareOrder($customerId, $items, $payment, $shipping, $config, $couponCode = "", $extensionData, $sessionData, $integration)
    {
        $session = $this->getSession();

        $this->_coreRegistry->register('webpos_extra_order_data', $extensionData);
        $this->_coreRegistry->register('create_order_webpos', true);

        $order = $this->createOrderByParams($session, $customerId, $items, $payment, $shipping, $config,
            $couponCode, $extensionData, $sessionData, $integration);

        $helper = $this->_objectManager->create('Magestore\Webpos\Helper\Data');
        if ($order) {
            $createInvoice = $config->getCreateInvoice();
            $createShipment = $config->getCreateShipment();
            $paidPayment = [
                'amount' => 0,
                'base_amount' => 0
            ];
            if (isset($payment[Payment::KEY_DATA])) {
                $paidPayment = $this->_getPaidPayment($payment[Payment::KEY_DATA]);
            }
            $itemsToShip = [];
            if ($createShipment == true) {
                $itemsToShip = ($session->getData('items_to_ship')) ? $session->getData('items_to_ship') : [];
                $session->setData('items_to_ship', null);
            }
            try {
                $this->processInvoiceAndShipment($order->getId(), $order, $createInvoice, $createShipment,
                    $itemsToShip, $shipping->getTracks());
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
            if ($config->getNote()) {
                $this->_saveOrderComment($order, $config->getNote());
            }
            if ($shipping->getDateTime()) {
                $order->setData('webpos_delivery_date', $shipping->getDateTime());
            }
            if($order->getData('webpos_change') && $order->getData('webpos_change') > 0) {
                $order->setData('total_paid', $paidPayment['amount'] - $order->getData('webpos_change'));
                $order->setData('base_total_paid', $paidPayment['base_amount'] - $order->getData('webpos_change'));
            } else {
                $order->setData('total_paid', $paidPayment['amount']);
                $order->setData('base_total_paid', $paidPayment['base_amount']);
            }
            $order->save();
            if (isset($payment[Payment::KEY_DATA])) {
                $this->_savePaymentsToOrder($order, $payment[Payment::KEY_DATA]);
            }
            $this->_eventManager->dispatch('webpos_order_sync_after', ['order' => $order]);

            //$order->setCanSendNewEmailFlag(true);
            if ($config->getSendSaleEmail()!==false) {
                try {
                    $this->emailSender->send($order);
                } catch (\Exception $e) {
                    $helper->addLog($e->getMessage());
                }
            }
            $orderRepository = $this->_objectManager->create("Magestore\Webpos\Api\Sales\OrderRepositoryInterface");
            $order = $orderRepository->get($order->getId());
            $session->clearStorage();
            $this->_coreRegistry->unregister('webpos_extra_order_data');

        } else {
            $syncedOrder = $this->getSyncOrder($extensionData);
            if ($syncedOrder !== false) {
                $orderRepository = $this->_objectManager->create("Magestore\Webpos\Api\Sales\OrderRepositoryInterface");
                $order = $orderRepository->get($syncedOrder);
            }
        }
        return $order;
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shippingMethod
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @return string
     */
    public function checkPromotion($customerId, $items, $payment, $shipping, $config, $couponCode = "")
    {
        $session = $this->getSession();
        $session->clearStorage();
        $storeId = $session->getStore()->getId();
        $session->setCurrencyId($config->getCurrencyCode());
        //$session->setStoreId($storeId);
        $session->setData('checking_promotion', true);

        $helperCustomer = $this->_objectManager->create('Magestore\Webpos\Helper\Customer');
        $storeAddress = $helperCustomer->getStoreAddressData();
        if ($customerId) {
            $customerResource = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer');
            $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
            $customerResource->load($customerModel, $customerId);
            if ($customerModel->getId()) {
                $session->setCustomerId($customerId);
                $this->getQuote()->setCustomer($customerModel->getDataModel());
            }
        } else {
            $this->getQuote()->setCustomerIsGuest(true);
            $this->getQuote()->setCustomerEmail($storeAddress['email']);
        }
        $this->setWebPosBillingAddress($payment, $storeAddress);

        $this->initRuleData();
        $this->_processCart($items);
        $this->setWebPosBillingAddress($payment, $storeAddress);
        if (!$this->getQuote()->isVirtual()) {
            $this->setWebPosShippingAddress($shipping, $storeAddress);
            $this->_saveShippingMethod($shipping->getMethod());
        }
        $this->_savePaymentData($payment);
        $this->_setCouponCode($couponCode);
        $this->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
        $discountTotal = 0;
        $quote = $this->getQuote();
        $itemsData = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $discountTotal += $item->getDiscountAmount();
            $itemId = $item->getBuyRequest()->getItemId();
            $itemsData[$itemId] = $item->getData();
        }
        $response = [
            "items" => $itemsData,
            "totals" => $this->getTotals(),
            "discount_amount" => $discountTotal,
            "applied_rule_ids" => $quote->getData('applied_rule_ids'),
            "message" => __("Discount amount: " . $discountTotal),
            "coupon_code" => $quote->getCouponCode()
        ];
        return \Zend_Json::encode($response);
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param string $zipcode
     * @param string $country
     * @return string
     */
    public function getShippingRates($customerId, $items, $zipcode = "", $country = "")
    {
        $session = $this->getSession();
        $session->clearStorage();
        $storeId = $session->getStore()->getId();
        //$session->setCurrencyId();
        $session->setCustomerId($customerId ? $customerId : false);
        $session->setStoreId($storeId);

        if (!$customerId) {
            $this->getQuote()->setCustomerIsGuest(true);
        }

        $this->initRuleData();
        $this->_processCart($items);

        $address = $this->getQuote()->getShippingAddress();
        if ($zipcode != "" && $country != "") {
            $address->setCountryId($country)
                ->setPostcode($zipcode);
        }
        $this->collectShippingRates();
        $rates = $address->collectShippingRates()
            ->getGroupedAllShippingRates();

        $shippingRates = [];
        foreach ($rates as $carrier) {
            foreach ($carrier as $rate) {
                $shippingRates[] = $rate->getData();
            }
        }
        $response = [
            "country" => $country,
            "zipcode" => $zipcode,
            "shipping_rates" => $shippingRates,
            "message" => __("Get shipping rate successfully")
        ];
        return \Zend_Json::encode($response);
    }

    /**
     *
     * @param string $incrementId
     * @param string $email
     * @return string
     */
    public function sendEmail($incrementId, $email)
    {
        if ($incrementId && $email) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
            if ($order && $order->getId()) {
                $order = $order->setEmailSent(false)->setCustomerEmail($email);
                $this->_objectManager->create('Magento\Sales\Model\Order\Email\Sender\OrderSender')
                    ->send($order);
                $error = false;
                $message = __('The order #%1 has been sent to the customer %2',
                    $order->getIncrementId(), $order->getCustomerEmail());
            } else {
//                $error = true;
//                $message = __('The order #%1 cannot be sent to the customer %2', $incrementId, $email);
                $message = __('The order #' . $incrementId . ' cannot be sent to the customer ' . $email);
                throw new StateException($message);
            }

        } else {
//            $error = true;
//            $message = __('Cannot send the order');
            throw new StateException(__('Cannot send the order'));
        }
        $response['error'] = $error;
        $response['message'] = $message;
        return \Zend_Json::encode($response);
    }


    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\IntegrationDataInterface[] $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function createOrderByParams($webposSession, $customerId, $items, $payment, $shipping,
                                        $config, $couponCode = "", $extensionData, $sessionData, $integration)
    {

        $this->_addSessionData($sessionData);

        /* Create Order when customer not sync */
        if ($customerId) {
            if (strpos($customerId, 'notsync') !== false) {
                $split = explode('_', $customerId);
                if (isset($split[1])) {
                    $customerEmail = $split[1];
                    $customerModel = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection')
                        ->addFieldToFilter('email', $customerEmail)
                        ->getFirstItem();
                    if ($customerModel->getId()) {
                        $customerId = $customerModel->getId();
                    }
                }
            }
        }
        /* End */
        $syncedOrder = $this->getSyncOrder($extensionData);
        if ($syncedOrder !== false) {
            return false;
        }
        $webposSession->clearStorage();
        $store = $webposSession->getStore();
        $storeId = $store->getId();
        $webposSession->setCurrencyId($config->getCurrencyCode());
        $webposSession->setStoreId($storeId);
        $webposSession->setData('checking_promotion', false);
        $webposSession->setData('webpos_order', 1);
        $store->setCurrentCurrencyCode($config->getCurrencyCode());
        $this->getQuote()->setQuoteCurrencyCode($config->getCurrencyCode());
        $helperCustomer = $this->_objectManager->create('Magestore\Webpos\Helper\Customer');
        $storeAddress = $helperCustomer->getStoreAddressData();
        if ($customerId) {
            $customerResource = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer');
            $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
            $customerResource->load($customerModel, $customerId);
            if ($customerModel->getId()) {
                $webposSession->setCustomerId($customerId);
                $this->getQuote()->setCustomer($customerModel->getDataModel());
            }
        } else {
            $this->getQuote()->setCustomerIsGuest(true);
            $this->getQuote()->setCustomerEmail($storeAddress['email']);
        }
        //$this->initRuleData();
        $this->_processCart($items);
        $this->setWebPosBillingAddress($payment, $storeAddress);
        if (!$this->getQuote()->isVirtual()) {
            $this->setWebPosShippingAddress($shipping, $storeAddress);
            $this->_saveShippingMethod($shipping->getMethod());
        }
        if (isset($payment['method_data'])) {
            $this->_eventManager->dispatch('webpos_order_save_payment_before', ['payment' => $payment]);
        }
        $this->_savePaymentData($payment);
        $this->_setCouponCode($couponCode);
        if ($config->getAppliedRuleIds()) {
            $this->getQuote()->setAppliedRuleIds($config->getAppliedRuleIds());
        }
//        if($config->getApplyPromotion() == Config::KEY_APPLY_PROMOTION_YES){
//            $this->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
//        }
        $this->getQuote()->getShippingAddress()->unsCachedItemsAll();
        $this->getQuote()->setTotalsCollectedFlag(false);
        $this->quoteRepository->save($this->getQuote());
        if ($config->getCartDiscountAmount()) {
            if ($couponCode) {
                $this->getQuote()->setData('coupon_code', $couponCode);
            }
        }
//        $this->applyM2eeGiftcard($integration);
        if ($config->getSendSaleEmail()===false) {
            $this->setSendConfirmation(false);
        }
        $order = $this->createOrder();

        $this->_removeSessionData($sessionData);

        if ($order) {
            $items = $order->getAllVisibleItems();
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $buyRequest = $item->getBuyRequest()->getData();
                    if (isset($buyRequest[CartItem::KEY_EXTENSION_DATA]) && isset($buyRequest[CartItem::KEY_ID]) && ($buyRequest[CartItem::KEY_ID] == $item->getProduct()->getId())) {
                        if (is_string($buyRequest[CartItem::KEY_EXTENSION_DATA])) {
                            $buyRequest[CartItem::KEY_EXTENSION_DATA] = unserialize($buyRequest[CartItem::KEY_EXTENSION_DATA]);
                        }
                        foreach ($buyRequest[CartItem::KEY_EXTENSION_DATA] as $data) {
                            if (isset($data[ExtensionData::KEY_FIELD_KEY]) && isset($data[ExtensionData::KEY_FIELD_VALUE])) {
                                $item->setData($data[ExtensionData::KEY_FIELD_KEY], $data[ExtensionData::KEY_FIELD_VALUE]);
                            }
                        }
                    }
                }
            }
            if (count($extensionData) > 0) {
                foreach ($extensionData as $data) {
                    $order->setData($data[ExtensionData::KEY_FIELD_KEY], $data[ExtensionData::KEY_FIELD_VALUE]);
                    if ($data[ExtensionData::KEY_FIELD_KEY] == "webpos_order_id") {
                        $order->setData("increment_id", $data[ExtensionData::KEY_FIELD_VALUE]);
                    }
                }
            }
            if ($config->getCartDiscountAmount()) {
                $order->setData('discount_amount', -$config->getCartDiscountAmount());
                $order->setData('base_discount_amount', -$config->getCartBaseDiscountAmount());
                $order->setData('discount_description', $config->getCartDiscountName());

                if ($config->getdiscountApply() == 'coupon') {
                    $order->setData('discount_description', $couponCode);
                } else {
                    $order->setData('discount_description', $config->getCartDiscountName());
                }
                if ($couponCode) {
                    $order->setData('coupon_code', $couponCode);
                }
                if (count($items) > 0) {
                    $discountAmount = $config->getCartDiscountAmount();
                    $baseDiscountAmount = $config->getCartBaseDiscountAmount();
                    $subtotal = $order->getData('subtotal');
                    $baseSubtotal = $order->getData('base_subtotal');
                    $tax = $order->getData('tax_amount');
                    $baseTax = $order->getData('base_tax_amount');
                    $shippingTax = $order->getData('shipping_tax_amount');
                    $baseShippingTax = $order->getData('base_shipping_tax_amount');
                    if ($baseDiscountAmount > $baseSubtotal) {
                        $shippingDiscount = $discountAmount - $subtotal - $tax + $shippingTax;
                        $baseShippingDiscount = $baseDiscountAmount - $baseSubtotal - $baseTax + $baseShippingTax;
                        $order->setData('shipping_discount_amount', $shippingDiscount);
                        $order->setData('base_shipping_discount_amount', $baseShippingDiscount);
                    }
                }
            }
            if ($config->getIsOnhold()) {
                $order->hold();
            } else {
                $order = $this->_processIntegration($order, $integration);
            }
            if ($config->getInitData()) {
                $order->setData(\Magestore\Webpos\Api\Data\Sales\OrderInterface::WEBPOS_INIT_DATA, $config->getInitData());
            }
        }

        return $order;
    }
//
//    /**
//     * @param \Magestore\Webpos\Api\Data\Checkout\IntegrationDataInterface[] $integration
//     */
//    public function applyM2eeGiftcard($integration){
//        /** @var \Magestore\Webpos\Api\Data\Checkout\IntegrationDataInterface $data */
//        foreach ($integration as $data){
//            if($data->getModule()=='m2ee_giftcard'){
//                $orderData = $data->getOrderData();
//                foreach ($orderData as $value){
//                    if($value->getKey()=='m2ee_giftcard_code'){
//                        /** @var \Magento\GiftCardAccount\Model\Giftcardaccount $giftCard*/
//                        $giftCard = $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount')->loadByCode($value->getValue());
//                        if($giftCard->getId()){
//                            $giftCard->addToCart(true, $this->getQuote());
//                        }
//                    }
//                }
//            }
//        }
//    }

    /**
     *
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\IntegrationDataInterface[] $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function createOrderByQuote($quoteId, $payment, $shipping, $couponCode, $config, $extensionData, $sessionData, $integration)
    {

        /* End */
        $syncedOrder = $this->getSyncOrder($extensionData);
        if ($syncedOrder !== false) {
            return false;
        }
        $quote = $this->quoteRepository->get($quoteId);
        $this->_addSessionData($sessionData);
        $this->setQuote($quote);
        if (!$this->getQuote()->isVirtual()) {
            $this->_saveShippingMethod($shipping->getMethod());
        }
        $this->getQuote()->getShippingAddress()->unsCachedItemsAll();
        $this->getQuote()->setTotalsCollectedFlag(false);
        if (isset($payment['method_data'])) {
            $this->_eventManager->dispatch('webpos_order_save_payment_before', ['payment' => $payment]);
        }
        $this->_savePaymentData($payment);
        $this->quoteRepository->save($this->getQuote());
        $order = $this->createOrder();
        $this->_removeSessionData($sessionData);
        if ($order) {
            $items = $order->getAllVisibleItems();
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $buyRequest = $item->getBuyRequest()->getData();
                    if (isset($buyRequest[CartItem::KEY_EXTENSION_DATA])) {
                        foreach ($buyRequest[CartItem::KEY_EXTENSION_DATA] as $data) {
                            $item->setData($data[ExtensionData::KEY_FIELD_KEY], $data[ExtensionData::KEY_FIELD_VALUE]);
                        }
                    }
                }
            }
            if (count($extensionData) > 0) {
                foreach ($extensionData as $data) {
                    $order->setData($data[ExtensionData::KEY_FIELD_KEY], $data[ExtensionData::KEY_FIELD_VALUE]);
                    if ($data[ExtensionData::KEY_FIELD_KEY] == "webpos_order_id") {
                        $order->setData("increment_id", $data[ExtensionData::KEY_FIELD_VALUE]);
                    }
                }
            }
            if ($config->getCartDiscountAmount()) {
                $order->setData('discount_amount', -$config->getCartDiscountAmount());
                $order->setData('base_discount_amount', -$config->getCartBaseDiscountAmount());
                $order->setData('discount_description', $config->getCartDiscountName());

                if ($config->getdiscountApply() == 'coupon') {
                    $order->setData('discount_description', $couponCode);
                } else {
                    $order->setData('discount_description', $config->getCartDiscountName());
                }
                if ($couponCode) {
                    $order->setData('coupon_code', $couponCode);
                }
                if (count($items) > 0) {
                    $baseDiscountAmount = $config->getCartBaseDiscountAmount();
                    $discountAmount = $config->getCartDiscountAmount();
                    $subtotal = $order->getData('subtotal');
                    $baseSubtotal = $order->getData('base_subtotal');
                    $tax = $order->getData('tax_amount');
                    $baseTax = $order->getData('base_tax_amount');
                    $shippingTax = $order->getData('shipping_tax_amount');
                    $baseShippingTax = $order->getData('base_shipping_tax_amount');
                    if ($baseDiscountAmount > $baseSubtotal) {
                        $shippingDiscount = $discountAmount - $subtotal - $tax + $shippingTax;
                        $baseShippingDiscount = $baseDiscountAmount - $baseSubtotal - $baseTax + $baseShippingTax;
                        $order->setData('shipping_discount_amount', $shippingDiscount);
                        $order->setData('base_shipping_discount_amount', $baseShippingDiscount);
                    }
                }
            }
            $order = $this->_processIntegration($order, $integration);
            if ($config->getInitData()) {
                $order->setData(\Magestore\Webpos\Api\Data\Sales\OrderInterface::WEBPOS_INIT_DATA, $config->getInitData());
            }
        }

        return $order;
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @param \Magestore\Webpos\Api\Data\Checkout\IntegrationDataInterface[] $integration
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Exception
     */
    public function createQuote($webposSession, $customerId, $items, $payment, $shipping,
                                $config, $couponCode = "", $extensionData, $sessionData, $integration)
    {

        $this->_addSessionData($sessionData);

        /* Create Order when customer not sync */
        if ($customerId) {
            if (strpos($customerId, 'notsync') !== false) {
                $split = explode('_', $customerId);
                if (isset($split[1])) {
                    $customerEmail = $split[1];
                    $customerModel = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection')
                        ->addFieldToFilter('email', $customerEmail)
                        ->getFirstItem();
                    if ($customerModel->getId()) {
                        $customerId = $customerModel->getId();
                    }
                }
            }
        }
        /* End */
        $syncedOrder = $this->getSyncOrder($extensionData);
        if ($syncedOrder !== false) {
            return false;
        }
        $webposSession->clearStorage();
        $store = $webposSession->getStore();
        $storeId = $store->getId();
        $webposSession->setCurrencyId($config->getCurrencyCode());
        $webposSession->setStoreId($storeId);
        $webposSession->setData('checking_promotion', false);
        $webposSession->setData('webpos_order', 1);
        $store->setCurrentCurrencyCode($config->getCurrencyCode());
        $this->getQuote()->setQuoteCurrencyCode($config->getCurrencyCode());
        $helperCustomer = $this->_objectManager->create('Magestore\Webpos\Helper\Customer');
        $storeAddress = $helperCustomer->getStoreAddressData();
        if ($customerId) {
            $customerResource = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer');
            $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
            $customerResource->load($customerModel, $customerId);
            if ($customerModel->getId()) {
                $webposSession->setCustomerId($customerId);
                $this->getQuote()->setCustomer($customerModel->getDataModel());
            }
        } else {
            $this->getQuote()->setCustomerIsGuest(true);
            $this->getQuote()->setCustomerEmail($storeAddress['email']);
        }
        //$this->initRuleData();
        $this->_processCart($items);
        $this->setWebPosBillingAddress($payment, $storeAddress);
        if (!$this->getQuote()->isVirtual()) {
            $this->setWebPosShippingAddress($shipping, $storeAddress);
            $this->_saveShippingMethod($shipping->getMethod());
        }
        $this->_savePaymentData($payment);
        $this->_setCouponCode($couponCode);

//        if($config->getApplyPromotion() == Config::KEY_APPLY_PROMOTION_YES){
//            $this->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
//        }
        $this->getQuote()->getShippingAddress()->unsCachedItemsAll();
        $this->getQuote()->setTotalsCollectedFlag(false);
        $this->quoteRepository->save($this->getQuote());
        return $this->getQuote();
    }

    /**
     *
     * @param string $code
     *
     * @return model
     */
    public function getPaymentModelByCode($code)
    {
        $paymentMethodNames = explode('_', $code);
        $paymentModelName = '';
        foreach ($paymentMethodNames as $name) {
            $paymentModelName .= '\\' . ucfirst($name);
        };
        $paymentModelName = 'Magestore\Webpos\Model\Payment\Online' . $paymentModelName;
        if (class_exists($paymentModelName)) {
            $paymentModel = $this->_objectManager->create($paymentModelName);
            return $paymentModel;
        }
        return false;
    }

    /**
     * get sync order
     *
     * @param array
     *
     * @return array
     */
    public function getSyncOrder($extensionData)
    {
        $syncedOrder = false;
        if (count($extensionData) > 0) {
            foreach ($extensionData as $data) {
                if ($data[ExtensionData::KEY_FIELD_KEY] == "webpos_order_id" && !empty($data[ExtensionData::KEY_FIELD_VALUE])) {
                    $orderModel = $this->_objectManager->create('Magento\Sales\Model\Order');
                    $orderModel = $orderModel->loadByIncrementId($data[ExtensionData::KEY_FIELD_VALUE]);
                    if ($orderModel->getEntityId()) {
                        $syncedOrder = $orderModel->getEntityId();
                    }
                }
            }
        }
        return $syncedOrder;
    }

    /**
     * set billing address
     *
     * @param array , array
     *
     * @return void
     */
    public function setWebPosBillingAddress($payment, $storeAddress)
    {
        if (!empty($payment->getAddress())) {
            $billingData = $payment->getAddress()->getData();
            if (empty($billingData['id']) || strpos($billingData['id'], "nsync") !== false) {
                unset($billingData['id']);
            }
            $billingData['saveInAddressBook'] = false;
            if (isset($billingData['region'])) {
                $region = $billingData['region'];
                $billingData['region'] = [
                    'region' => $region->getRegion(),
                    'region_id' => $region->getRegionId(),
                    'region_code' => $region->getRegionCode()
                ];
            }
            $this->setBillingAddress($billingData);
        } else {
            $this->setBillingAddress($storeAddress);
        }
    }

    /**
     * set shipping address
     *
     * @param array , array
     *
     * @return void
     */
    public function setWebPosShippingAddress($shipping, $storeAddress)
    {
        if (!empty($shipping->getAddress())) {
            $shippingData = $shipping->getAddress()->getData();
            if (empty($shippingData['id']) || strpos($shippingData['id'], "nsync") !== false) {
                unset($shippingData['id']);
            }
            $shippingData['saveInAddressBook'] = false;
            if (isset($shippingData['region'])) {
                $region = $shippingData['region'];
                $shippingData['region'] = [
                    'region' => $region->getRegion(),
                    'region_id' => $region->getRegionId(),
                    'region_code' => $region->getRegionCode()
                ];
            }
            $this->setShippingAddress($shippingData);
        } else {
            $this->setShippingAddress($storeAddress);
        }
    }

    /**
     * @param $sessionData
     */
    protected function _addSessionData($sessionData)
    {
        if (count($sessionData) > 0) {
            $session = $this->getSession();
            foreach ($sessionData as $data) {
                if (isset($data[SessionData::KEY_SESSION_CLASS])) {
                    $sessionClass = $data[SessionData::KEY_SESSION_CLASS];
                    $model = $this->_objectManager->create($sessionClass);
                    $model->setData($data[SessionData::KEY_FIELD_KEY], $data[SessionData::KEY_FIELD_VALUE]);
                } else {
                    $session->setData($data[SessionData::KEY_FIELD_KEY], $data[SessionData::KEY_FIELD_VALUE]);
                }
            }
        }
    }

    /**
     * @param $sessionData
     */
    protected function _removeSessionData($sessionData)
    {
        if (count($sessionData) > 0) {
            $session = $this->getSession();
            foreach ($sessionData as $data) {
                if (isset($data[SessionData::KEY_SESSION_CLASS])) {
                    $sessionClass = $data[SessionData::KEY_SESSION_CLASS];
                    $model = $this->_objectManager->create($sessionClass);
                    $model->setData($data[SessionData::KEY_FIELD_KEY], null);
                } else {
                    $session->setData($data[SessionData::KEY_FIELD_KEY], null);
                }
            }
        }
    }

    /**
     * @param $order
     * @param $integration
     * @return mixed
     */
    protected function _processIntegration($order, $integration)
    {
        if (count($integration) > 0) {
            foreach ($integration as $extension) {
                $datas = $extension->getOrderData();
                $extensionData = $extension->getExtensionData();
                $eventName = $extension->getEventName();
                $eventData = [
                    'order' => $order,
                    'order_data' => [],
                    'extension_data' => []
                ];
                if (count($datas) > 0) {
                    foreach ($datas as $data) {
                        $order->setData($data[ExtensionData::KEY_FIELD_KEY], $data[ExtensionData::KEY_FIELD_VALUE]);
                        $eventData['order_data'][$data[ExtensionData::KEY_FIELD_KEY]] = $data[ExtensionData::KEY_FIELD_VALUE];
                    }
                }
                if (count($extensionData) > 0) {
                    foreach ($extensionData as $data) {
                        $eventData['extension_data'][$data[ExtensionData::KEY_FIELD_KEY]] = $data[ExtensionData::KEY_FIELD_VALUE];
                    }
                }
                $this->_eventManager->dispatch($eventName, $eventData);
            }
        }
        return $order;
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shippingMethod
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @return string
     */
    public function checkGiftcard($customerId, $items, $payment, $shipping, $config, $couponCode = "")
    {
        $response = [];
        $session = $this->getSession();
        $session->clearStorage();
        $storeId = $session->getStore()->getId();
        $session->setCurrencyId($config->getCurrencyCode());
        $session->setStoreId($storeId);
        $session->setData('checking_promotion', true);

        $helperCustomer = $this->_objectManager->create('Magestore\Webpos\Helper\Customer');
        $storeAddress = $helperCustomer->getStoreAddressData();
        if ($customerId) {
            $customerResource = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer');
            $customerModel = $this->_objectManager->create('Magento\Customer\Model\Customer');
            $customerResource->load($customerModel, $customerId);
            if ($customerModel->getId()) {
                $session->setCustomerId($customerId);
                $this->getQuote()->setCustomer($customerModel->getDataModel());
            }
        } else {
            $this->getQuote()->setCustomerIsGuest(true);
            $this->getQuote()->setCustomerEmail($storeAddress['email']);
        }
        $this->setWebPosBillingAddress($payment, $storeAddress);

        $this->initRuleData();
        $this->_processCart($items);
        $this->setWebPosBillingAddress($payment, $storeAddress);
        if (!$this->getQuote()->isVirtual()) {
            $this->setWebPosShippingAddress($shipping, $storeAddress);
            $this->_saveShippingMethod($shipping->getMethod());
        }
        $this->_savePaymentData($payment);
        $this->_setCouponCode($couponCode);

        $checkoutSession = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $giftVoucher = $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher')->loadByCode($couponCode);
        if ($giftVoucher->getId() && $giftVoucher->getBaseBalance() > 0
            && $giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
        ) {
            $webposHelper = $this->_objectManager->create('\Magestore\Webpos\Helper\Data');
            $isGiftcardRebuild = $webposHelper->isGiftcardRebuild();
            if ($isGiftcardRebuild) {
                $checkoutService = $this->_objectManager->create('\Magestore\Giftvoucher\Api\Redeem\CheckoutServiceInterface');
                if (!$checkoutService->validateCustomer($giftVoucher, $customerId)) {
                    $response['error'] = true;
                    $response['message'] = __('This gift code limits the number of users');
                    $session->clearStorage();
                    return \Zend_Json::encode($response);
                }
            } else {
                $giftVoucher->addToSession($checkoutSession);
            }
            $quote = $this->getQuote();
            if ($giftVoucher->validate($quote->setQuote($quote))) {
                if ($isGiftcardRebuild) {
                    $checkoutService = $this->_objectManager->create('\Magestore\Giftvoucher\Api\Redeem\CheckoutServiceInterface');
                    $quote = $this->getQuote();
                    $checkoutService->addVoucherToQuote($quote->getId(), $giftVoucher);
                }
                $response['base_balance'] = $giftVoucher->getBaseBalance();
                $response['balance'] = $giftVoucher->getBalance();
                $response['success'] = true;
            } else {
                $response['error'] = true;
                $response['message'] = __('Can’t use this gift code since its conditions haven’t been met.');
            }
            $checkoutSession->unsGiftCodes();
        } else {
            $response['error'] = true;
            $response['message'] = __('Gift code is invalid');
        }
        $session->clearStorage();
        return \Zend_Json::encode($response);
    }

    /**
     * @param string $orderIncrementId
     */
    public function unholdOrder($orderIncrementId)
    {
        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $order = $order->loadByIncrementId($orderIncrementId);
        if ($order && $order->getId()) {
            $order->unhold();
            $order->cancel();
            $orderResource = $this->_objectManager->create('Magento\Sales\Model\ResourceModel\Order');
            $orderResource->save($order);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Order #%1 cannot be found', $orderIncrementId)
            );
        }
    }
}