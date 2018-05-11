<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Cart;

use Magento\Framework\Cache\Frontend\Adapter\Zend;
use Magestore\Webpos\Api\Data\Cart\QuoteDataInterface;
use Magestore\Webpos\Model\Checkout\Data\Payment;
use Magestore\Webpos\Model\Checkout\Data\CartItem;
use Magestore\Webpos\Model\Checkout\Data\ExtensionData;

/**
 * Class Create
 * @package Magestore\Webpos\Model\Cart
 */
class Create extends \Magestore\Webpos\Model\Checkout\Create
    implements \Magestore\Webpos\Api\Cart\CheckoutInterface
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
     * Allow Shippings
     *
     * @var array
     */
    protected $allowShippings;

    /**
     * Allow Payments
     *
     * @var array
     */
    protected $allowPayments;

    /**
     * CC Payments
     *
     * @var array
     */
    protected $ccPayments;

    /**
     *  init quote
     *
     * @param string $quoteId
     * @return $this
     */
    public function initQuote($quoteId,$customerId = null)
    {
        if ($quoteId) {
            $quote = $this->quoteRepository->getActive($quoteId);
            $this->setQuote($quote);
            $this->getSession()->setQuoteId($quoteId);
        } else {
            $session = $this->_objectManager->create('Magento\Checkout\Model\Session');
            $session->clearStorage();
            $this->getSession()->clearStorage();
        }
    }

    /**
     *
     * @param string $customerId
     * @param string $quoteId
     * @param string $currencyId
     * @param string $storeId
     * @param string $tillId
     * @param \Magestore\Webpos\Api\Data\Cart\CustomerInterface $customer
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveCart($customerId, $quoteId, $currencyId, $storeId, $tillId, $customer, $items)
    {
        $this->_coreRegistry->register('webpos_get_product_list', true);
//        $session = $this->_objectManager->create('\Magento\Checkout\Model\Session');
//        $session->clearStorage();
        $this->createQuoteByParams($customerId, $quoteId, $currencyId, $storeId,
            $tillId, $customer, $items);
        $result = $this->getQuoteData();
        return $result;
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function createQuoteByParams($customerId, $quoteId, $currencyId, $storeId,
                                        $tillId, $customer, $items)
    {
        $webposSession = $this->getSession();
        $this->initQuote($quoteId);
        $store = $webposSession->getStore();
        $webposSession->setCurrencyId($currencyId);
        $webposSession->setStoreId($storeId);
        $webposSession->setData('checking_promotion', true);
        $webposSession->setData('webpos_order', 1);
        $webposSession->setData('till_id', $tillId);
        $store->setCurrentCurrencyCode($currencyId);
        $this->getQuote()->setQuoteCurrencyCode($currencyId);
        $helperCustomer = $this->_objectManager->create('Magestore\Webpos\Helper\Customer');
        $storeAddress = $helperCustomer->getStoreAddressData();
//        $customerId = $customer->getCustomerId();
        if ($customerId) {
            $customerData = $this->customerRepository->getById($customerId);
            if ($customerData->getId()) {
                $webposSession->setCustomerId($customerId);
                $this->getQuote()->setCustomer($customerData);
            }
            $this->getQuote()->setCustomerIsGuest(false);
        } else {
            $webposSession->setCustomerId(false);
            $this->getQuote()->getCustomer()->setId('');
            $this->getQuote()->setCustomerIsGuest(true);
            $this->getQuote()->setCustomerEmail($storeAddress['email']);
        }

        $this->_processCart($items);
        $this->initRuleData();
        $billingAddress = $this->getAddressData($customer->getBillingAddress());
        $shippingAddress = $this->getAddressData($customer->getShippingAddress());
        if (!$billingAddress)
            $billingAddress = $storeAddress;
        if (!$shippingAddress)
            $shippingAddress = $storeAddress;
        $this->setBillingAddress($billingAddress);
        if (!$this->getQuote()->isVirtual()) {
            $this->setShippingAddress($shippingAddress);
            $this->getShippingAddress()->setSameAsBilling(0);
            try {
                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true)
                    ->collectShippingRates();
            } catch (\Exception $e) {

            }
        }
        $this->getQuote()->setIsActive(1);
        $couponCode = $this->getQuote()->getCouponCode();
        if ($couponCode && $couponCode != '') {
            $this->applyCouponCode($quoteId, $couponCode);
        } else {
            $this->saveQuote();
            /*Mark: Start - fix bug shipping address copy of billing address*/
            if (!$this->getQuote()->isVirtual()&&$this->getShippingAddress()->getSameAsBilling()) {
                $this->getShippingAddress()->setSameAsBilling(0)->save();
            }
            /*Mark: End - fix bug shipping address copy of billing address*/
        }
        $session = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        $session->setQuoteId($this->getQuote()->getId());
    }

    /**
     * @return mixed
     */
    public function getQuoteInitData()
    {
        $quote = $this->getQuote();
        $quoteInitData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Quote');
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::QUOTE_ID, $quote->getId());
//        $quoteIdMask = $this->_objectManager
//            ->create('Magento\Quote\Model\QuoteIdMaskFactory')
//            ->create();
//        $quoteIdMask->load($quote->getId(), 'quote_id');
//        if(!$quoteIdMask->getId()){
//            $quoteIdMask->setQuoteId($quote->getId())->save();
//        }
//        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::QUOTE_ID_MASK, $quoteIdMask->getMaskedId());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::CUSTOMER_ID, $quote->getCustomerId());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::CURRENCY_ID, $quote->getQuoteCurrencyCode());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::TILL_ID, $quote->getTillId());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::STORE_ID, $quote->getStoreId());
        return $quoteInitData;
    }

    /**
     * @param $sections
     * @param $model
     * @return array
     */
    protected function getQuoteData($sections = null)
    {
        $quoteData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Checkout');
        if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT, $sections))
        ) {
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
                $this->getQuoteInitData());
        }
        if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS, $sections))
        ) {
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
                $this->getQuoteItems());
        }
        if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS, $sections))
        ) {
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS,
                $this->getCartTotals());
        }
        if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::SHIPPING ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::SHIPPING, $sections))
        ) {
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::SHIPPING,
                $this->getShipping());
        }
        if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT, $sections))
        ) {
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT,
                $this->getPayment());
        }
        if ($this->_objectManager->get('\Magento\Framework\Module\Manager')->isEnabled('Magestore_Giftvoucher')) {
            if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::GIFTCARD ||
                (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::GIFTCARD, $sections))
            ) {
                $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::GIFTCARD,
                    $this->getGiftCardDiscount());
            }
        }
        if ($this->_objectManager->get('\Magento\Framework\Module\Manager')->isEnabled('Magestore_Rewardpoints')) {
            if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::REWARDPOINTS ||
                (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::REWARDPOINTS, $sections))
            ) {
                $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::REWARDPOINTS,
                    $this->getPointsDiscount());
            }
        }
        if ($this->_objectManager->get('\Magento\Framework\Module\Manager')->isEnabled('Magestore_Customercredit')) {
            if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::STORECREDIT ||
                (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::STORECREDIT, $sections))
            ) {
                $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::STORECREDIT,
                    $this->getStorecredit());
            }
        }
        $quote = $this->getQuote();
        $this->updateWebposSessionData($quote->getStoreId(), $quote->getTillId(), $quote->getId());
        return $quoteData;
    }

    /**
     * @return array
     */
    public function getQuoteItems()
    {
        $result = array();
        $items = $this->getQuote()->getAllVisibleItems();
        if (count($items)) {
            foreach ($items as $item) {
                $item->setData('super_attribute', $item->getBuyRequest()->getData('super_attribute'));
                $product = $item->getProduct();
                $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
                $imageUrl = $this->getImage($product);
                $item->setData('offline_item_id', $item->getBuyRequest()->getData('item_id'));
                $item->setData('minimum_qty', $stockItem->getMinSaleQty());
                $item->setData('maximum_qty', $stockItem->getMaxSaleQty());
                $item->setData('qty_increment', $stockItem->getQtyIncrements());
                if ($this->_objectManager->get('\Magento\Framework\Module\Manager')->isEnabled('Magestore_Giftvoucher')) {
                    if ($item->getProduct()->getTypeId() == 'giftvoucher') {
                        if ($item->getOptionByCode('giftcard_template_image')) {
                            $filename = $item->getOptionByCode('giftcard_template_image')->getValue();
                            $urlImage = '/giftvoucher/template/images/' . $filename;
                            $imageUrl = $this->_objectManager->get('Magestore\Giftvoucher\Helper\Data')->getStoreManager()
                                    ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$urlImage;

                        }
                    }
                }

                $item->setData('image_url', $imageUrl);
                $item->setData('product_id', $product->getId());
                $item->setData('is_salable', $product->isSalable());
                $item->setData('stocks', [[
                    'backorders' => $stockItem->getBackorders(),
                    'is_in_stock' => $stockItem->getIsInStock(),
                    'is_qty_decimal' => $stockItem->getIsDecimalDivided(),
                    'item_id' => $stockItem->getItemId(),
                    'manage_stock' => $stockItem->getManageStock(),
                    'max_sale_qty' => $stockItem->getManageStock(),
                    'min_sale_qty' => $stockItem->getMinQty(),
                    'name' => $product->getName(),
                    'product_id' => $stockItem->getProductId(),
                    'qty' => $stockItem->getQty(),
                    'qty_increments' => $stockItem->getQtyIncrements(),
                    'sku' => $product->getSku(),
                    'stock_id' => $stockItem->getStockId(),
                    'use_config_backorders' => $stockItem->getUseConfigBackorders(),
                    'use_config_manage_stock' => $stockItem->getUseConfigManageStock(),
                    'use_config_max_sale_qty' => $stockItem->getUseConfigMaxSaleQty(),
                    'use_config_min_sale_qty' => $stockItem->getUseConfigMinSaleQty(),
                ]]);
                $childrend = $item->getChildren();
                if(count($childrend)){
                    $item->setData('child_id',$childrend[0]->getProduct()->getId());
                }
                $itemData = $item->getData();
                if(isset($itemData['product'])) {
                    unset($itemData['product']);
                }
                $result[$item->getId()] = $itemData;
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getCartTotals()
    {
        $totals = $this->getAllTotals($this->getQuote());
        $totalsResult = array();
        if ($this->_objectManager->get('\Magento\Framework\Module\Manager')
            ->isEnabled('Magestore_Rewardpoints')
        ) {
            foreach ($totals as $total) {
                $data = $total->getData();
                if ($total->getData('code') == 'rewardpointsearning') {
                    $data['value'] = $this->_objectManager->create('Magestore\Rewardpoints\Helper\Calculation\Earning')
                        ->getTotalPointsEarning($this->getQuote());
                    $data['title'] = __('Customer will earn');
                }
                $totalsResult[] = $data;
            }
        } else {
            foreach ($totals as $total) {
                $data = $total->getData();
                $totalsResult[] = $data;
            }
        }
        return $totalsResult;
    }

    /**
     * Get all quote totals (sorted by priority)
     * Method process quote states isVirtual and isMultiShipping
     *
     * @return array
     */
    public function getAllTotals($quote)
    {
        if ($quote->isVirtual()) {
            return $quote->getBillingAddress()->getTotals();
        }
        return $quote->getShippingAddress()->getTotals();
    }

    /**
     * @return array
     */
    public function getShipping()
    {
        $shippingList = array();
        $api = $this->_objectManager->create('Magento\Quote\Model\ShippingMethodManagement');
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $list = $api->estimateByExtendedAddress($this->getQuote()->getId(), $shippingAddress);
        if (count($list) > 0) {
            $shippingHelper = $this->_objectManager->create('Magestore\Webpos\Helper\Shipping');
            foreach ($list as $data) {
                if(!$shippingHelper->isAllowOnWebPOS($data->getCarrierCode())) {
                    continue;
                }
                $methodCode = $data->getMethodCode();
                $carrierCode = $data->getCarrierCode();
                $isDefault = '0';
                if ($methodCode == $shippingHelper->getDefaultShippingMethod()) {
                    $isDefault = '1';
                }
                $methodTitle = $data->getCarrierTitle() . ' - ' . $data->getMethodTitle();
                $methodPrice = ($data->getPriceExclTax() != null) ? $data->getPriceInclTax() : '0';
                $methodPriceType = '';
//                $methodDescription = ($data->getDescription() != null) ? $data->getDescription() : '0';
//                $methodSpecificerrmsg = ($data->getErrorMessage() != null) ? $data->getErrorMessage() : '';
                $methodDescription = '';
                $methodSpecificerrmsg = '';

                $shippingModel = $this->_objectManager->create('Magestore\Webpos\Model\Shipping\Shipping');
                $shippingModel->setCode($carrierCode . '_' . $methodCode);
                $shippingModel->setTitle($methodTitle);
                $shippingModel->setPrice($methodPrice);
                $shippingModel->setDescription($methodDescription);
                $shippingModel->setIsDefault($isDefault);
                $shippingModel->setErrorMessage($methodSpecificerrmsg);
                $shippingModel->setPriceType($methodPriceType);
                $shippingList[] = $shippingModel;
            }
        }
        return $shippingList;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        $api = $this->_objectManager->create('Magento\Quote\Model\PaymentMethodManagement');
        $list = $api->getList($this->getQuote()->getId());
        $this->allowPayments = array('cashforpos', 'codforpos', 'ccforpos', 'cp1forpos', 'cp2forpos',
            'paypal_direct', 'authorizenet_directpost', 'payflowpro_integration');
        $this->ccPayments = array('authorizenet_directpost', 'payflowpro_integration', 'stripe_integration', 'paynl_payment_instore');
        $mobilePayments = array('paypal_here', 'authorizenet_integration', 'paynl_payment_instore');
        $webPayments = array('authorizenet_directpost', 'payflowpro_integration', 'authorizenet', 'paypal_direct', 'stripe_integration', 'paynl_payment_instore');
        $paymentHelper = $this->_objectManager->create('Magestore\Webpos\Helper\Payment');
        if($paymentHelper->isRetailerPos()) {
            $deactivePayments = $webPayments;
        } else {
            $deactivePayments = $mobilePayments;
        }
        $paymentList = array();
        if (count($list) > 0) {
            foreach ($list as $data) {
                if (!in_array($data->getCode(), $this->allowPayments))
                    continue;
                if (in_array($data->getCode(), $deactivePayments)) {
                    continue;
                }
                $code = $data->getCode();
                $title = $data->getTitle();
                $ccTypes = '0';
                $useCvv = 0;
                if (in_array($data->getCode(), $this->ccPayments)) {
                    $ccTypes = '1';
                    $useCvv = $paymentHelper->useCvv($code);
                }
                $iconClass = 'icon-iconPOS-payment-cp1forpos';
                $isDefault = ($code == $paymentHelper->getDefaultPaymentMethod()) ?
                    \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
                    \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
                $isReferenceNumber = $paymentHelper->isReferenceNumber($code) ? '1' : '0';
                $isPayLater = $paymentHelper->isPayLater($code) ? '1' : '0';

                $paymentModel = $this->_objectManager->create('Magestore\Webpos\Model\Payment\Payment');
                $paymentModel->setCode($code);
                $paymentModel->setIconClass($iconClass);
                $paymentModel->setTitle($title);
                $paymentModel->setInformation('');
                $paymentModel->setType(($ccTypes) ? $ccTypes : \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO);
                $paymentModel->setTypeId(($ccTypes) ? $ccTypes : \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO);
                $paymentModel->setIsDefault($isDefault);
                $paymentModel->setIsReferenceNumber($isReferenceNumber);
                $paymentModel->setIsPayLater($isPayLater);
                $paymentModel->setMultiable(0);
                $paymentModel->setUsecvv($useCvv);
                $paymentList[] = $paymentModel->getData();
            }
        }
        $data = array(
            'payments' => new \Magento\Framework\DataObject(array(
                'list' => $paymentList,
            ))
        );
        $this->_eventManager->dispatch(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_GET_PAYMENT_AFTER, $data);
        $paymentList = $data['payments']->getList();
        return $paymentList;
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposPaypal()
    {
        $paymentHelper = $this->_objectManager->create('Magestore\Webpos\Helper\Payment');
        $helper = $this->_objectManager->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('webpos/payment/paypal/is_sandbox');
        $clientId = $helper->getStoreConfig('webpos/payment/paypal/client_id');
        $isDefault = ('paypal_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = $this->_objectManager->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('paypal_integration');
        $paymentModel->setIconClass('paypal_integration');
        $paymentModel->setTitle(_('Web POS - Paypal Integration'));
        $paymentModel->setInformation('');
        $paymentModel->setType('2');
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setClientId($clientId);
        $paymentModel->setIsSandbox($isSandbox);
        return $paymentModel;
    }

    /**
     * Sets product image from it's child if possible
     *
     * @return string
     */
    public function getImage($product)
    {
        $imageString = $product->getThumbnail();
        if ($imageString && $imageString != 'no_selection') {
            return $product->getMediaConfig()->getMediaUrl($imageString);
        } else {
            $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Store\Model\StoreManagerInterface'
            );

            $block = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Block\Webpos'
            );
            return $block->getViewFileUrl('Magestore_Webpos::images/category/image.jpg');
        }
    }

    /**
     * remove cart by quote id
     *
     * @param string $quoteId
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteInterface
     * @throws \Exception
     */
    public function removeCart($quoteId)
    {
        $quoteData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Quote');
        if (!empty($quoteId)) {
            $quote = $this->quoteRepository->get($quoteId);
            $eventData = array(
                'quote' => $quote
            );
            $this->_eventManager->dispatch(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_BEFORE,
                $eventData);
            $storeId = $quote->getStoreId();
            $shiftId = $quote->getTillId();
            try {
                $this->quoteRepository->delete($quote);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Unable to remove cart')
                );
            }
            $session = $this->_objectManager->create('\Magento\Checkout\Model\Session');
            $session->clearStorage();
            $backendSession = $this->getSession();
            $backendSession->clearStorage();
            $this->updateWebposSessionData($storeId, $shiftId, '');
            $this->_eventManager->dispatch(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_AFTER,
                $eventData);
        }
        return $quoteData;
    }

    /**
     * save shipping method
     *
     * @param string $quoteId
     * @param string $shippingMethod
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveShippingMethod($quoteId, $shippingMethod)
    {
        $this->initQuote($quoteId);
        $billingAddress = $this->getQuote()->getBillingAddress();
        try {
            $this->_saveShippingMethod($shippingMethod);
            $this->setBillingAddress($billingAddress);
            $extensionAttributes = $this->getQuote()->getExtensionAttributes();
            if (!$this->getQuote()->isVirtual() && $extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                $shippingAssignments = $extensionAttributes->getShippingAssignments();
                if (count($shippingAssignments) == 1) {
                    $shippingAssignment = $shippingAssignments[0];
                    $shipping = $shippingAssignment->getShipping();
                    if(!empty($shipping->getMethod()) && $this->getQuote()->getItemsCount() > 0){
                        $shipping->setMethod($shippingMethod);
                    }
                }
            }
            /*Mark - Start: fix wrong shipping address*/
//            $extensionAttributes = $this->getQuote()->getExtensionAttributes();
            if ($extensionAttributes) {
                $extensionAttributes->setShippingAssignments([]);
            }
            /*Mark - End*/
            $this->saveQuote();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to save shipping method')
            );
        }
        $data = array(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::GIFTCARD,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::REWARDPOINTS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::STORECREDIT
        );
        $result = $this->getQuoteData($data);
        return $result;
    }

    /**
     * save payment method
     *
     * @param string $quoteId
     * @param string $paymentMethod
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function savePaymentMethod($quoteId, $paymentMethod)
    {
        $this->initQuote($quoteId);
        try {
            $this->setPaymentMethod($paymentMethod);
            /*Mark - Start: fix wrong shipping address*/
            $extensionAttributes = $this->getQuote()->getExtensionAttributes();
            if ($extensionAttributes) {
                $extensionAttributes->setShippingAssignments([]);
            }
            /*Mark - End*/
            $this->saveQuote();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to save payment method')
            );
        }
        $data = array(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::GIFTCARD,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::REWARDPOINTS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::STORECREDIT
        );
        $result = $this->getQuoteData($data);
        return $result;
    }

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface $quoteData
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveQuoteData($quoteId, $quoteData)
    {
        $this->initQuote($quoteId);
        try {
            $this->getQuote()->addData($quoteData->getData());
            $this->checkDiscount($quoteData);
            $this->saveQuote();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to save cart')
            );
        }
        $result = $this->getQuoteData();
        return $result;
    }

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface $quoteData
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function checkDiscount($quoteData)
    {
        if ($quoteData->getWebposCartDiscountValue() <= 0) {
            $this->getQuote()->setCouponCode('');
        }
    }

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param string $shippingMethod
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface $quoteData
     * @param \Magestore\Webpos\Api\Data\Cart\ActionInterface $actions
     * @param \Magestore\Webpos\Api\Data\Checkout\Integration\ModuleInterface[] $integration
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function placeOrder($quoteId, $payment, $shippingMethod, $quoteData, $actions, $integration, $extensionData)
    {
        $this->_coreRegistry->register('create_order_webpos', true);
        $data = array('order' => new \Magento\Framework\DataObject(array(
            'quote_id' => $quoteId,
            'payment' => $payment,
            'shipping_method' => $shippingMethod,
            'quote_data' => $quoteData,
            'can_process' => true
        ))
        );
        $this->_eventManager->dispatch(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_ORDER_PLACE_BEFORE, $data);
//        if(!$data['order']->getData('can_process')) {
//            throw new \Magento\Framework\Exception\StateException(
//                __('Cannot place order')
//            );
//        }
        $this->initQuote($quoteId);
        try {
            $billingAddress = $this->getQuote()->getBillingAddress();
            if ($shippingMethod) {
                $this->_saveShippingMethod($shippingMethod);
            }
            $this->setBillingAddress($billingAddress);
            if (isset($payment['method_data'])) {
                $this->_eventManager->dispatch('webpos_order_save_payment_before', ['payment' => $payment]);
            }
            $this->_savePaymentData($payment);
            $order = $this->createOrder();
            $this->_removeSessionData($this->getSession());
            $order = $this->processOrderData($order, $extensionData);
            $order = $this->_processIntegration($order, $integration);
            $this->processActionsAfterCreateOrder($order, $actions);
            $this->_saveOrderComment($order, $quoteData->getCustomerNote());
            $order = $this->processPaymentAfterCreateOrder($order, $payment);
            $this->_eventManager->dispatch('webpos_order_sync_after', ['order' => $order]);
            $isWebVersion = $quoteData->getIsWebVersion();
            $isWebVersion = ($isWebVersion && ($isWebVersion == QuoteDataInterface::YES))?true:false;
            $paymentModel = $this->getPaymentModelByCode($payment['method']);

            // caculate the shipping discount
            $this->setShippingDiscount($order);

            // change total_paid when total_paid > grand_total
            if($order->getData('webpos_change') && $order->getData('webpos_change') > 0) {
                $order->setData('total_paid', $order->getData('total_paid') - $order->getData('webpos_change'));
                $order->setData('base_total_paid', $order->getData('base_total_paid') - $order->getData('webpos_change'));
                $order->save();
            }

            if ($paymentModel && ($isWebVersion || $payment['method'] == 'authorizenet_directpost')
                && !$this->isProcessPaymentBeforeOrder($payment)
            ) {
                $result['order'] = $order->getData();
                $result['payment_model'] = $payment['method'];
                $result['payment_infomation'] = $paymentModel->getRequestInformation($order);
                $result = \Zend_Json::encode($result);
            } else {
                $orderRepository = $this->_objectManager->create("Magestore\Webpos\Api\Sales\OrderRepositoryInterface");
                $order = $orderRepository->get($order->getId());
                $result = $order;
                if ($actions->getSendSaleEmail()) {
                    $this->emailSender->send($order);
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
        $orderData = array(
            'result' => $result
        );
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $session->setData('use_storecredit_ee', false);
        $this->_eventManager->dispatch(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_ORDER_PLACE_AFTER, $orderData);
        return $result;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     */
    public function setShippingDiscount($order){
        $itemDiscount = $baseItemDiscount = 0;
        /** @var \Magento\Sales\Model\Order\Item $_item */
        foreach ($order->getAllVisibleItems() as $_item){
            $itemDiscount += $_item->getDiscountAmount();
            $baseItemDiscount += $_item->getBaseDiscountAmount();
        }
        $shippingDiscount = $order->getDiscountAmount() + $itemDiscount;
        $baseShippingDiscount = $order->getBaseDiscountAmount() + $baseItemDiscount;
        if ($shippingDiscount < 0 && $baseShippingDiscount < 0){
            $order->setShippingDiscountAmount(-$shippingDiscount)
                ->setBaseShippingDiscountAmount(-$baseShippingDiscount);
            $order->save();
        }
        return $order;
    }

    /**
     * process order data
     *
     * @param $order
     * @param $payment
     */
    public function processOrderData($order, $extensionData)
    {
        if (count($extensionData) > 0) {
            foreach ($extensionData as $data) {
                $order->setData($data[ExtensionData::KEY_FIELD_KEY], $data[ExtensionData::KEY_FIELD_VALUE]);
                if ($data[ExtensionData::KEY_FIELD_KEY] == "webpos_order_id") {
                    $order->setData("increment_id", $data[ExtensionData::KEY_FIELD_VALUE]);
                }
            }
        }
        return $order;
    }

    /**
     * save payment information
     *
     * @param $order
     * @param $payment
     */
    public function processPaymentAfterCreateOrder($order, $payment)
    {
        $paidPayment = [
            'amount' => 0,
            'base_amount' => 0
        ];
        if (isset($payment[Payment::KEY_DATA])) {
            $paidPayment = $this->_getPaidPayment($payment[Payment::KEY_DATA]);
        }
        $order->setData('total_paid', $paidPayment['amount']);
        $order->setData('base_total_paid', $paidPayment['base_amount']);
        $order->save();
        if (isset($payment[Payment::KEY_DATA])) {
            $this->_savePaymentsToOrder($order, $payment[Payment::KEY_DATA]);
        }
        return $order;
    }

    /**
     * process invoice shipment and other information
     *
     * @param $order
     * @param $actions
     */
    public function processActionsAfterCreateOrder($order, $actions)
    {
        $createInvoice = 0;
        $createShipment = 0;
        if (!empty($actions)) {
            $payment = $order->getPayment();
            $method = $payment->getMethodInstance();
            $methodCode = $method->getCode();
            if ($methodCode != 'authorizenet_directpost') {
                if ($actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_INVOICE)) {
                    $createInvoice = $actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_INVOICE);
                }
            }
            if ($actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_SHIPMENT)) {
                $createShipment = $actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_SHIPMENT);
            }
            if ($actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::DELIVERY_TIME)) {
                $order->setData('webpos_delivery_date', $actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::DELIVERY_TIME));
            }
        }
        try {
            $this->processInvoiceAndShipment($order->getId(), $order, $createInvoice, $createShipment,
                array(), array());
        } catch (\Exception $e) {
        }
    }

    /**
     *
     * @param string $quoteId
     * @param string $itemId
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function removeItemById($itemId, $quoteId)
    {
        if ($quoteId) {
            $quote = $this->quoteRepository->get($quoteId);
            $this->setQuote($quote);
            try {
                $this->removeItem($itemId, 'quote');
                $this->removeDiscount();
                $this->saveQuote();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Unable to remove item')
                );
            }
        } else {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove item')
            );
        }
        $data = array(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS
        );
        $result = $this->getQuoteData($data);

        return $result;
    }

    public function removeDiscount()
    {
        $quote = $this->getQuote();
        if (count($quote->getAllItems()) == 0) {
            $quote->setData('webpos_cart_discount_value', null);
            $quote->setData('coupon_code', null);
        }
    }

    /**
     * @param string $quoteId
     * @param string $couponCode
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function applyCouponCode($quoteId, $couponCode)
    {
        if ($quoteId) {
            $this->initQuote($quoteId);
            if (!$this->getQuote()->getItemsCount()) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Cart %1 doesn\'t contain products', $quoteId));
            }
            try {
                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '');
                $this->quoteRepository->save($this->getQuote()->collectTotals());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\StateException(__('Could not apply coupon code'));
            }

            if ($this->getQuote()->getCouponCode() != $couponCode) {
                throw new \Magento\Framework\Exception\StateException(__('Coupon code is not valid'));
            }
        } else {
            throw new \Magento\Framework\Exception\StateException(
                __('Could not apply')
            );
        }
        $result = $this->getQuoteData();

        return $result;
    }

    /**
     * Add multiple products to current order quote
     *
     * @param array $products
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addProducts(array $products)
    {

        foreach ($products as $productConfig) {
            try {
                $productConfig['qty'] = isset($productConfig['qty']) ? (double)$productConfig['qty'] : 1;
                $this->addProduct($productConfig[CartItem::KEY_ID], $productConfig);

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->__toString())
                );
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->__toString())
                );
            }
        }

        return $this;
    }

    /**
     * Update quantity of order quote items
     *
     * @param array $items
     * @return $this
     * @throws \Exception|\Magento\Framework\Exception\LocalizedException
     */
    public function updateQuoteItems($items)
    {
        if (!is_array($items)) {
            return $this;
        }

        try {
            foreach ($items as $info) {
                $itemId = $info[CartItem::ITEM_ID];
                if (!empty($info['configured'])) {
                    $item = $this->getQuote()->updateItem($itemId, $this->objectFactory->create($info));
                    $info['qty'] = (double)$item->getQty();
                } else {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item) {
                        continue;
                    }
                    $info['qty'] = (double)$info['qty'];
                }
                $this->quoteItemUpdater->update($item, $info);
                if ($item && !empty($info['action'])) {
                    $this->moveQuoteItem($item, $info['action'], $item->getQty());
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->recollectCart();
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        $this->recollectCart();

        return $this;
    }

    /**
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @return \Magestore\Webpos\Model\Checkout\Create
     */
    protected function _processCart($items)
    {
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $exist = false;
            foreach ($items as $itemSend) {
                if ($itemSend->getItemId() == $item->getId()) {
                    $exist = true;
                }
            }
            if ($exist == false) {
                $this->removeItem($item->getId(), 'quote');
            }
        }

        if (isset($items) && count($items) > 0) {
            $newProducts = [];
            $updateProducts = [];
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
                $product[CartItem::KEY_EXTENSION_DATA] = $item->getExtensionData();
                $product[CartItem::CUSTOMERCREDIT_AMOUNT] = $item->getAmount();
                $product[CartItem::CUSTOMERCREDIT_PRICE_AMOUNT] = $item->getCreditPriceAmount();
                $product[CartItem::ITEM_ID] = $item->getItemId();
                $product[CartItem::USE_DISCOUNT] = $item->getUseDiscount();

                $product[CartItem::GIFTCARD_AMOUNT] = $item->getAmount();
                $product[CartItem::GIFTCARD_TEMPLATE_ID] = $item->getGiftcardTemplateId();
                $product[CartItem::GIFTCARD_TEMPLATE_IMAGE] = $item->getGiftcardTemplateImage();
                $product[CartItem::GIFTCARD_MESSAGE] = $item->getMessage();
                $product[CartItem::GIFTCARD_RECIPIENT_NAME] = $item->getRecipientName();
                $product[CartItem::GIFTCARD_RECIPIENT_EMAIL] = $item->getRecipientEmail();
                $product[CartItem::GIFTCARD_SEND_FRIEND] = $item->getSendFriend();
                $product[CartItem::GIFTCARD_DAY_TO_SEND] =  $item->getDayToSend();
                $product[CartItem::GIFTCARD_TIMEZONE_TO_SEND] = $item->getTimezoneToSend();
                $product[CartItem::GIFTCARD_RECIPIENT_ADDRESS] = $item->getRecipientAddress();
                $product[CartItem::GIFTCARD_NOTIFY_SUCCESS] = $item->getNotifySuccess();
                $product[CartItem::GIFTCARD_RECIPIENT_SHIP] = $item->getRecipientShip();

                $product[CartItem::GIFTCARD_M2EE_CUSTOM_AMOUNT] = (int)$item->getCustomGiftcardAmount();
                $product[CartItem::GIFTCARD_M2EE_AMOUNT] = $item->getGiftcardAmount();
                $product[CartItem::GIFTCARD_M2EE_RECEIPIENT_NAME] = $item->getGiftcardRecipientName();
                $product[CartItem::GIFTCARD_M2EE_SENDER_NAME] = $item->getGiftcardSenderName();
                $product[CartItem::GIFTCARD_M2EE_SENDER_EMAIL] = $item->getGiftcardSenderEmail();
                $product[CartItem::GIFTCARD_M2EE_RECIPIENT_EMAIL] = $item->getGiftcardRecipientEmail();
                $product[CartItem::GIFTCARD_M2EE_MESSAGE] = $item->getGiftcardMessage();



                if (!$this->getQuote()->getItemById($item->getItemId())) {
                    $newProducts[] = $product;
                } else {
                    $updateProducts[] = $product;
                }
            }
            if (!empty($newProducts)) {
                $this->addProducts($newProducts);
            }
            if (!empty($updateProducts)) {
                $this->updateQuoteItems($updateProducts);
            }
        }
        return $this;
    }

    /**
     *
     * @param string $quoteId
     * @param string $code
     * @param string $amount
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function applyGiftcard($quoteId, $code, $amount)
    {
        $this->initQuote($quoteId);
        $error = '';
        $session = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $quote = $this->getQuote();
        $addCodes = array();
        if ($code = trim($code)) {
            $addCodes[] = $code;
        }
        $helper = $this->_objectManager->create('Magestore\Giftvoucher\Helper\Data');
        $webposHelper = $this->_objectManager->create('\Magestore\Webpos\Helper\Data');
        $isGiftcardRebuild = $webposHelper->isGiftcardRebuild();
        if ($isGiftcardRebuild) {
            $checkoutService = $this->_objectManager->create('\Magestore\Giftvoucher\Api\Redeem\CheckoutServiceInterface');
            $addedCodes = $checkoutService->getUsingGiftCodes($quoteId);
            if (count($addedCodes)) {
                foreach ($addedCodes as $addedCode) {
                    if ($addedCode[\Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface::CODE] == $code) {
                        $addedCode[\Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface::DISCOUNT] = $amount;
                    }
                }
            } else {
                $addedCode[] = [
                    \Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface::CODE => $code,
                    \Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface::DISCOUNT => $amount
                ];
            }
            $result = $checkoutService->applyCodes($quoteId, $addedCodes, $code);
            if (!empty($result[\Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface::ERRORS])) {
                throw new \Magento\Framework\Exception\StateException($result[\Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface::ERRORS][0]);
            }
            if (!empty($result[\Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface::NOTICES])) {
                throw new \Magento\Framework\Exception\StateException($result[\Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface::NOTICES][0]);
            }
            return $this->getQuoteData();
        }


        $giftvoucherSession = $this->_objectManager->create('\Magestore\Giftvoucher\Model\Session');
        $max = $helper->getGeneralConfig('maximum');
        $codes = $giftvoucherSession->getCodes();
        if (!count($addCodes)) {
            $errorMessage = __('Invalid gift code. Please try again. ');
            throw new \Magento\Framework\Exception\StateException($errorMessage);
        }
        foreach ($addCodes as $code) {
            $giftVoucher = $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher')->loadByCode($code);
            if (!$giftVoucher->getId() || $giftVoucher->getSetId()) {
                $codes[] = $code;
                $codes = array_unique($codes);
                $giftvoucherSession->setCodes($codes);
                if ($giftVoucher->getSetId()) {
                    $errorMessage = __('Gift card is invalid.');
                } else {
                    $errorMessage = __('Gift card "%1" is invalid.', $code);
                }
                $error .= $errorMessage;
            } elseif (!$giftVoucher->validate($quote->setQuote($quote))) {
                $error = __('You can’t use this gift code since its conditions haven’t been met');
            } elseif ($giftVoucher->getId() && $giftVoucher->getBaseBalance() > 0
                && $giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
            ) {
                if ($helper->canUseCode($giftVoucher)) {
                    $giftVoucher->addToSession($session);
                    $session->setUseGiftCard(1);
                    $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
                    if (!is_array($giftMaxUseAmount)) {
                        $giftMaxUseAmount = array();
                    }
                    if ($amount > 0) {
                        $giftMaxUseAmount[$code] = $amount;
                    } else {
                        unset($giftMaxUseAmount[$code]);
                    }
                    $session->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                    $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
                } else {
                    if ($error == '') {
                        $error .= '<br/>';
                    }
                    $error .= __('This gift code limits the number of users', $code);
                }
            } else {
                if (isset($errorMessage)) {
                    $error = $errorMessage . '<br/>';
                } elseif (isset($result['error'])) {
                    $error .= '<br/>';
                } else {
                    $error = '';
                }
                $error .= __('Gift code "%1" is no longer available to use.', $code);
            }
        }
        if ($error != '') {
            throw new \Magento\Framework\Exception\StateException(__($error));
        }
//        $this->saveQuote();
        $result = $this->getQuoteData();
        return $result;
    }

    /**
     *
     * @param string $quoteId
     * @param string $code
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function removeGiftcard($quoteId, $code)
    {
        $this->initQuote($quoteId);
        $quote = $this->getQuote();
        $helper = $this->_objectManager->create('Magestore\Giftvoucher\Helper\Data');
        $webposHelper = $this->_objectManager->create('\Magestore\Webpos\Helper\Data');
        $isGiftcardRebuild = $webposHelper->isGiftcardRebuild();
        if ($isGiftcardRebuild) {
            $checkoutService = $this->_objectManager->create('\Magestore\Giftvoucher\Api\Redeem\CheckoutServiceInterface');
            $result = $checkoutService->removeCode($quoteId, $code);
            if (!empty($result[\Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface::ERRORS])) {
                throw new \Magento\Framework\Exception\StateException($result[\Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface::ERRORS][0]);
            }
            return $this->getQuoteData();
        }
        $session = $helper->getCheckoutSession();
        $code = trim($code);
        $codes = trim($session->getGiftCodes());
        $success = false;
        if ($code && $codes) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $key => $value) {
                if ($value == $code) {
                    unset($codesArray[$key]);
                    $success = true;
                    $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
                    if (is_array($giftMaxUseAmount) && array_key_exists($code, $giftMaxUseAmount)) {
                        unset($giftMaxUseAmount[$code]);
                        $session->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                    }
                    break;
                }
            }
        }

        if ($success) {
            $codes = implode(',', $codesArray);
            $session->setGiftCodes($codes);
            $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
        } else {
            throw new \Magento\Framework\Exception\StateException(__('Gift card "%1" not found!', $code));
        }
        $result = $this->getQuoteData();
        return $result;
    }

    /**
     * @return array
     */
    public function getGiftCardDiscount()
    {
        $quote = $this->getQuote();
        $session = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        $discounts = array();
        $giftCard = $this->_objectManager->create('\Magestore\Webpos\Model\Integration\Response\Giftcard');
        $existedCodes = array();
        $webposCurrencyHelper = $this->_objectManager->get('\Magestore\Webpos\Helper\Currency');
        $quoteCurrencyCode = $session->getQuote()->getQuoteCurrencyCode();
        $codes = $session->getGiftCodes();
        $codesDiscount = $session->getCodesDiscount();
        $webposHelper = $this->_objectManager->create('\Magestore\Webpos\Helper\Data');
        $isGiftcardRebuild = $webposHelper->isGiftcardRebuild();
        if ($isGiftcardRebuild) {
            $codes = $quote->getGiftVoucherGiftCodes();
            $codesDiscount = $quote->getGiftVoucherGiftCodesDiscount();
            $checkoutService = $this->_objectManager->create('\Magestore\Giftvoucher\Api\Redeem\CheckoutServiceInterface');
            $customerCodes = $checkoutService->getExistedGiftCodes($quote->getId());
            if (count($customerCodes) > 0) {
                foreach ($customerCodes as $giftode) {
                    $code = $giftode[\Magestore\Giftvoucher\Api\Data\GiftcodeInterface::GIFT_CODE];
                    $balance = $giftode[\Magestore\Giftvoucher\Api\Data\GiftcodeInterface::BALANCE];
                    $existedCodes[] = [
                        'code' => $code,
                        'balance' => $balance,
                        'label' => $code . " (" . $balance . ")"
                    ];
                }
            }

        }

        if ($codes) {
            $codesArray = explode(',', $codes);
            $codesDiscountArray = explode(',', $codesDiscount);
            foreach ($codesArray as $key => $value) {
                $discount = array();
                if (!isset($codesDiscountArray[$key])) {
                    $discount['amount'] = '';
                } else {
                    $discount['amount'] = abs(round($codesDiscountArray[$key], 2));
                }
                $discount['code'] = $value;
                $giftVoucher = $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher')
                    ->loadByCode($value);
                $giftVoucherCurrency = ($isGiftcardRebuild) ? $giftVoucher->getCurrency() : $giftVoucher->getCurrencyCode();
                $discount['balance'] = abs(round($webposCurrencyHelper
                    ->currencyConvert($giftVoucher->getBalance(), $giftVoucherCurrency, $quoteCurrencyCode), 2));
                $discounts[] = $discount;

            }
        }
        $usedCodes = $discounts;
        $giftCard->setUsedCodes($usedCodes);
        $giftCard->setExistedCodes($existedCodes);
        return $giftCard;
    }

    /**
     *
     * @param string $quoteId
     * @param string $usedPoint
     * @param string $ruleId
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function spendPoint($quoteId, $usedPoint, $ruleId)
    {
        $this->initQuote($quoteId);
        $session = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        $session->setData('use_point', true);
        $session->setQuoteId($this->getQuote()->getId());
        $session->setRewardSalesRules(array(
            'rule_id' => $ruleId,
            'use_point' => $usedPoint,
        ));

        if ($this->getQuote()->getItemsCount()) {
            $this->checkUseDefault();
        }
        $this->saveQuote();
        $result = $this->getQuoteData();
        return $result;
    }

    public function checkUseDefault()
    {
        $session = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        $session->setData('use_max', 0);
        $rewardSalesRules = $session->getRewardSalesRules();
        $helperSpend = $this->_objectManager->create('\Magestore\Rewardpoints\Helper\Block\Spend');
        $arrayRules = $helperSpend->getRulesArray();
        $calculationSpending = $this->_objectManager->create('\Magestore\Rewardpoints\Helper\Calculation\Spending');
        if ($calculationSpending->isUseMaxPointsDefault()) {
            if (isset($rewardSalesRules['use_point']) &&
                isset($rewardSalesRules['rule_id']) &&
                isset($arrayRules[$rewardSalesRules['rule_id']]) &&
                isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']) &&
                isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints']) && ($rewardSalesRules['use_point'] < $arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints'])
            ) {
                $session->setData('use_max', 0);
            } else {
                $session->setData('use_max', 1);
            }
        }
    }

    /**
     * @return array
     */
    public function getPointsDiscount()
    {
        $balance = 0;
        $usedPoint = 0;
        $maxPoint = 0;
        $rewardPoint = $this->_objectManager->create('\Magestore\Webpos\Model\Integration\Response\Rewardpoints');
        $pointSpending = $this->_objectManager->create('\Magestore\Rewardpoints\Helper\Calculation\Spending');
        $helperSpend = $this->_objectManager->create('\Magestore\Rewardpoints\Helper\Block\Spend');
        $helperCustomer = $this->_objectManager->create('\Magestore\Rewardpoints\Helper\Customer');
        if ($pointSpending->getTotalPointSpent()) {
            $usedPoint = $pointSpending
                ->getTotalPointSpent();
        }
        if ($helperSpend->getRulesArray()) {
            $rules = $helperSpend->getRulesArray();
            $rule = $rules['rate'];
            if ($rule['optionType'] == 'needPoint') {
                $maxPoint = $rule['needPoint'];
            } else {
                $maxPoint = $rule['sliderOption']['maxPoints'];
            }
        }
        if ($helperCustomer->getBalance()) {
            $balance = $helperCustomer->getBalance();
        }
        $rewardPoint->setUsedPoint($usedPoint);
        $rewardPoint->setMaxPoints($maxPoint);
        $rewardPoint->setBalance($balance);
        return $rewardPoint;
    }

    /**
     * @return array
     */
    public function getStorecredit()
    {
        $balance = 0;
        $storecredit = $this->_objectManager->create('\Magestore\Customercredit\Model\Customercredit');
        if ($customerId = $this->getQuote()->getCustomer()->getId()) {
            if ($storecredit->load($customerId, 'customer_id')->getCreditBalance()) {
                $balance = $storecredit->load($customerId, 'customer_id')->getCreditBalance();
            }
        }
        $webposCurrencyHelper = $this->_objectManager->get('\Magestore\Webpos\Helper\Currency');
        $quoteCurrencyCode = $this->getQuote()->getQuoteCurrencyCode();
        $balance = $webposCurrencyHelper->convertFromBase($balance, $quoteCurrencyCode);
        $storecredit->setBalance($balance);
        return $storecredit;
    }

    public function getAddressData($address)
    {
        $addressData = [];
        if (!empty($address)) {
            $addressData = $address->getData();
            if (empty($addressData['id']) || strpos($addressData['id'], "nsync") !== false) {
                unset($addressData['id']);
            }
            $addressData['saveInAddressBook'] = false;
            if (isset($addressData['region'])) {
                $region = $addressData['region'];
                $addressData['region'] = [
                    'region' => $region->getRegion(),
                    'region_id' => $region->getRegionId(),
                    'region_code' => $region->getRegionCode()
                ];
            }
        }
        return $addressData;
    }

    public function updateWebposSessionData($storeId, $shiftId, $quoteId)
    {
        $helperPermission = $this->_objectManager->create('\Magestore\Webpos\Helper\Permission');
        $session = $helperPermission->getCurrentSessionModel();
        $session->setCurrentQuoteId($quoteId);
        $session->setCurrentShiftId($shiftId);
        $session->setCurrentStoreId($storeId);
        $session->save();
    }

    /**
     * @param string $customerId
     * @param string $quoteId
     * @param string $currencyId
     * @param string $storeId
     * @param string $tillId
     * @param string[] $section
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function getCartData($customerId, $quoteId, $currencyId, $storeId, $tillId, $section)
    {
        if ($quoteId) {
            try {
                $quote = $this->quoteRepository->getActive($quoteId);
                $this->setQuote($quote);
            } catch (\Exception $e) {

            }
        }
        return $this->getQuoteData($section);
    }

    /**
     * @param string $customerId
     * @param string $quoteId
     * @param string $currencyId
     * @param string $storeId
     * @param string $tillId
     * @param string[] $section
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     */
    public function getCartDataByCustomer($customerId, $quoteId, $currencyId, $storeId, $tillId, $section)
    {
        try {
            $this->getSession()->setCustomerId($customerId);
            $quote = $this->getCustomerCart();
            if ($quote->getId()) {
                $this->setQuote($quote);
            }else{
                $quoteData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Checkout');
                $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,[]);
                return $quoteData;
            }
        } catch (\Exception $e) {

        }
        return $this->getQuoteItemsData($section);
    }

    /**
     * @param $sections
     * @param $model
     * @return array
     */
    protected function getQuoteItemsData($sections = null)
    {
        $quoteData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Checkout');
        if (empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS, $sections))
        ) {
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
                $this->getQuoteItems());
        }
        return $quoteData;
    }
    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @return string
     * @throws \Exception
     */
    public function getPaymentRequest($quoteId, $payment)
    {
        if ($quoteId) {
            $this->initQuote($quoteId);
        }
        if ($payment) {
            $this->_savePaymentData($payment);
            $this->saveQuote();
        }
        $quote = $this->getQuote();
        $paymentModel = $quote->getPayment();
        if ($paymentModel &&
            $paymentModel->getAdditionalInformation('hasPaypalPro'))
        {
            $paymentMethod = $paymentModel->getAdditionalInformation('hasPaypalPro');
            if ($paymentMethod && $processPaymentCode = $this->isProcessPaymentBeforeOrder($payment)) {
                $result = array();
                $result['payment_infomation'] = [];
                $result['payment_model'] = $paymentMethod;
                $result['quote_id'] = $quoteId;
                $paymentModel = $this->getPaymentModelByCode($this->getPaymentModelCode($processPaymentCode));
                if ($paymentModel) {
                    $result['payment_infomation'] = $paymentModel->requestSecureToken($quote);
                }
                return \Zend_Json::encode($result);
            }
        }
        return false;
    }
}