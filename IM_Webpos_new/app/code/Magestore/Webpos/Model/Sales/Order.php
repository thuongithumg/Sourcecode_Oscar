<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales;

use Magestore\Webpos\Api\Data\Sales\OrderInterface;
use Magestore\Webpos\Api\Data\Checkout\InfoBuyInterface;

class Order extends \Magento\Sales\Model\Order implements \Magento\Sales\Model\EntityInterface, OrderInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\Staff\StaffInterface
     */
    protected $_staff;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory
     */
    protected $_orderPaymentCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
        \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory $orderPaymentCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
        $this->dateTime = $dateTime;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $timezone,
            $storeManager,
            $orderConfig,
            $productRepository,
            $orderItemCollectionFactory,
            $productVisibility,
            $invoiceManagement,
            $currencyFactory,
            $eavConfig,
            $orderHistoryFactory,
            $addressCollectionFactory,
            $paymentCollectionFactory,
            $historyCollectionFactory,
            $invoiceCollectionFactory,
            $shipmentCollectionFactory,
            $memoCollectionFactory,
            $trackCollectionFactory,
            $salesOrderCollectionFactory,
            $priceCurrency,
            $productListFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\ResourceModel\Order');
    }

    /**
     * Sets the rewardpoints sp earn pointent.
     *
     * @param float $earnPoint
     * @return $this
     */
    public function setRewardpointsEarn($earnPoint)
    {
        return $this->setData(self::REWARDPOINTS_EARN, $earnPoint);
    }

    /**
     * Gets the rewardpoints earn point.
     *
     * @return float.
     */
    public function getRewardpointsEarn()
    {
        return $this->getData(OrderInterface::REWARDPOINTS_EARN);
    }

    /**
     * Sets the rewardpoints spent.
     *
     * @param float $spentPoint
     * @return $this
     */
    public function setRewardpointsSpent($spentPoint)
    {
        return $this->setData(self::REWARDPOINTS_SPENT, $spentPoint);
    }

    /**
     * Sets the customer base balance amount
     *
     * @param float $balanceAmount
     * @return $this
     */
    public function setBaseCustomerBalanceAmount($baseBalanceAmount)
    {
        return $this->setData(self::BASE_CUSTOMER_BALANCE_AMOUNT, $baseBalanceAmount);
    }

    /**
     * Sets the base customer balance amount
     *
     * @param float $balanceAmount
     * @return $this
     */
    public function getCustomerBalanceAmount()
    {
        return $this->getData(OrderInterface::CUSTOMER_BALANCE_AMOUNT);
    }

    /**
     * Gets the customer base balance amount
     *
     * @param float $balanceAmount
     * @return $this
     */
    public function getBaseCustomerBalanceAmount()
    {
        return $this->getData(OrderInterface::BASE_CUSTOMER_BALANCE_AMOUNT);
    }

    
    /**
     * Sets the credit amount
     *
     * @param int $amount
     * @return $this
     */
    public function setCustomerBalanceAmount($amount) {
        return $this->setData(self::CUSTOMER_BALANCE_AMOUNT, $amount);
    }

    /**
     * Gets the rewardpoints spent.
     *
     * @return float.
     */
    public function getRewardpointsSpent()
    {
        return $this->getData(OrderInterface::REWARDPOINTS_SPENT);
    }

    /**
     * Sets the rewardpoints discount.
     *
     * @param float $discount
     * @return $this
     */
    public function setRewardpointsDiscount($discount)
    {
        return $this->setData(self::REWARDPOINTS_DISCOUNT, $discount);
    }

    /**
     * Gets the webpos rewardpoints discount.
     *
     * @return float.
     */
    public function getRewardpointsDiscount()
    {
        return -$this->getData(OrderInterface::REWARDPOINTS_DISCOUNT);
    }

    /**
     * Sets the webpos rewardpoints base discount.
     *
     * @param float $baseDiscount
     * @return $this
     */
    public function setRewardpointsBaseDiscount($baseDiscount)
    {
        return $this->setData(self::REWARDPOINTS_BASE_DISCOUNT, $baseDiscount);
    }

    /**
     * Gets the webpos rewardpoints base discount.
     *
     * @return float.
     */
    public function getRewardpointsBaseDiscount()
    {
        return -$this->getData(OrderInterface::REWARDPOINTS_BASE_DISCOUNT);
    }

    /**
     * Set the total rewardpoints refunded .
     *
     * @return float.
     */
    public function setRewardpointsRefunded($rewardpointsRefunded){
        return $this->setData(OrderInterface::REWARDPOINTS_REFUNDED, $rewardpointsRefunded);
    }

    /**
     * Gets the total rewardpoints refunded .
     *
     * @return float.
     */
    public function getRewardpointsRefunded()
    {
        if($this->getData(OrderInterface::REWARDPOINTS_REFUNDED) == null) {
            $this->setData(OrderInterface::REWARDPOINTS_REFUNDED, 0);
            if ($this->_objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Magestore_Rewardpoints')) {
                $rewardpointsRefunded = (int)$this->_objectManager
                    ->create('Magestore\Rewardpoints\Model\ResourceModel\Transaction\Collection')
                    ->addFieldToFilter('action', 'spending_creditmemo')
                    ->addFieldToFilter('order_id', $this->getEntityId())
                    ->getFieldTotal();
                $this->setData(OrderInterface::REWARDPOINTS_REFUNDED, $rewardpointsRefunded ? $rewardpointsRefunded : 0);
            }
        }
        return $this->getData(OrderInterface::REWARDPOINTS_REFUNDED);;
    }

    /**
     * Sets the webpos giftcard discount.
     *
     * @param float $discount
     * @return $this
     */
    public function setGiftVoucherDiscount($discount)
    {
        return $this->setData(self::GIFT_VOUCHER_DISCOUNT, $discount);
    }

    /**
     * Gets the webpos  giftcard discount.
     *
     * @return float.
     */
    public function getGiftVoucherDiscount()
    {
        return -$this->getData(OrderInterface::GIFT_VOUCHER_DISCOUNT);
    }

    /**
     * Sets the webpos base giftcard discount.
     *
     * @param float $baseDiscount
     * @return $this
     */
    public function setBaseGiftVoucherDiscount($baseDiscount)
    {
        return $this->setData(self::BASE_GIFT_VOUCHER_DISCOUNT, $baseDiscount);
    }

    /**
     * Gets the webpos base giftcard discount.
     *
     * @return float.
     */
    public function getBaseGiftVoucherDiscount()
    {
        return -$this->getData(OrderInterface::BASE_GIFT_VOUCHER_DISCOUNT);
    }

    /**
     * Sets the webpos giftcard discount.
     *
     * @param float $discount
     * @return $this
     */
    public function setGiftCardsAmount($discount)
    {
        return $this->setData(self::GIFT_CARDS_AMOUNT, $discount);
    }

    /**
     * Gets the webpos  giftcard discount.
     *
     * @return float.
     */
    public function getGiftCardsAmount()
    {
        return -$this->getData(OrderInterface::GIFT_CARDS_AMOUNT);
    }

    /**
     * Sets the webpos base giftcard discount.
     *
     * @param float $baseDiscount
     * @return $this
     */
    public function setBaseGiftCardsAmount($baseDiscount)
    {
        return $this->setData(self::BASE_GIFT_CARDS_AMOUNT, $baseDiscount);
    }

    /**
     * Gets the webpos base giftcard discount.
     *
     * @return float.
     */
    public function getBaseGiftCardsAmount()
    {
        return -$this->getData(OrderInterface::BASE_GIFT_CARDS_AMOUNT);
    }

    /**
     * Sets the webpos base credit amount.
     *
     * @param float $baseDiscount
     * @return $this
     */
    public function setBaseCustomercreditDiscount($baseDiscount)
    {
        return $this->setData(self::BASE_CUSTOMERCREDIT_DISCOUNT, $baseDiscount);
    }

    /**
     * Gets the webpos base credit amount.
     *
     * @return float.
     */
    public function getBaseCustomercreditDiscount()
    {
        return -$this->getData(OrderInterface::BASE_CUSTOMERCREDIT_DISCOUNT);
    }

    /**
     * Sets the credit amount.
     *
     * @param float $discount
     * @return $this
     */
    public function setCustomercreditDiscount($discount)
    {
        return $this->setData(self::CUSTOMERCREDIT_DISCOUNT, $discount);
    }

    /**
     * Gets the credit amount.
     *
     * @return float.
     */
    public function getCustomercreditDiscount()
    {
        return -$this->getData(OrderInterface::CUSTOMERCREDIT_DISCOUNT);
    }

    /**
     * Set Webpos base change
     *
     * @param float $webposBaseChange
     * @return $this
     */
    public function setWebposBaseChange($webposBaseChange)
    {
        return $this->setData(self::WEBPOS_BASE_CHANGE, $webposBaseChange);
    }

    /**
     * Returns Webpos base change
     *
     * @return float
     */
    public function getWebposBaseChange()
    {
        return $this->getData(OrderInterface::WEBPOS_BASE_CHANGE);
    }

    /**
     * Set Webpos change
     *
     * @param float $webposChange
     * @return $this
     */
    public function setWebposChange($webposChange)
    {
        return $this->setData(self::WEBPOS_CHANGE, $webposChange);
    }

    /**
     * Returns Webpos change
     *
     * @return float
     */
    public function getWebposChange()
    {
        return $this->getData(OrderInterface::WEBPOS_CHANGE);
    }

    /**
     * Set Webpos staff ID for Order
     *
     * @param int $webposStaffId
     * @return $this
     */
    public function setWebposStaffId($webposStaffId)
    {
        return $this->setData(self::WEBPOS_STAFF_ID, $webposStaffId);
    }

    /**
     * Returns Webpos staff ID
     *
     * @return int
     */
    public function getWebposStaffId()
    {
        return $this->getData(OrderInterface::WEBPOS_STAFF_ID);
    }

    /**
     * Sets the Webpos staff name for the order.
     *
     * @param string $webposStaffName
     * @return $this
     */
    public function setWebposStaffName($webposStaffName)
    {
        return $this->setData(self::WEBPOS_STAFF_NAME, $webposStaffName);
    }

    /**
     * Sets the Customer full name for the order.
     *
     * @param string $fullName
     * @return $this
     */
    public function setCustomerFullname($fullName)
    {
        return $this->setData(self::CUSTOMER_FULLNAME, $fullName);
    }

    /**
     * Sets the Fulfill online name for the order.
     *
     * @param int $fulfill
     * @return $this
     */
    public function setFulfillOnline($fulfill) {
        return $this->setData(self::FULFILL_ONLINE, $fulfill);
    }

    /**
     * Gets the Webpos staff name for the order.
     *
     * @return string|null Webpos staff name.
     */
    public function getWebposStaffName()
    {
        if (!$this->getData(OrderInterface::WEBPOS_STAFF_NAME)) {
            if ($staffId = $this->getData(OrderInterface::WEBPOS_STAFF_ID)) {
                return $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')
                    ->load($staffId)->getDisplayName();
            }
        }
        return $this->getData(OrderInterface::WEBPOS_STAFF_NAME);
    }

    /**
     * Gets the Customer full name for the order.
     *
     * @return string|null customer full name.
     */
    public function getCustomerFullname()
    {
        return $this->getData(OrderInterface::CUSTOMER_FULLNAME);
    }

    /**
     * Gets the fulfill online for the order.
     *
     * @return int|null fulfill order.
     */
    public function getFulfillOnline() {
        return $this->getData(OrderInterface::FULFILL_ONLINE);
    }

    /**
     * Gets the fulfill online for the order.
     *
     * @return int|null fulfill online attribute from order.
     */
    public function geFulfillOnline() {
        return $this->getData(OrderInterface::FULFILL_ONLINE);
    }
    /**
     * Sets the Webpos location ID for the order.
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * Gets the Webpos location ID for the order.
     *
     * @return int|null Webpos location ID.
     */
    public function getLocationId()
    {
        return $this->getData(OrderInterface::LOCATION_ID);
    }

    /**
     * Sets the Webpos delivery date for the order.
     *
     * @param string $deliveryDate
     * @return $this
     */
    public function setWebposDeliveryDate($deliveryDate)
    {
        return $this->setData(OrderInterface::WEBPOS_DELIVERY_DATE, $deliveryDate);
    }

    /**
     * Gets the Webpos location ID for the order.
     *
     * @return string|null Webpos delivery date.
     */
    public function getWebposDeliveryDate()
    {
        return $this->getData(OrderInterface::WEBPOS_DELIVERY_DATE);
    }

    /**
     * Sets the Webpos order payments for the order.
     *
     * @param \Magestore\Webpos\Api\Data\Payment\OrderPaymentInterface[] $webposOrderPayments
     * @return $this
     */
    public function setWebposOrderPayments($webposOrderPayments)
    {
        return $this->setData(OrderInterface::WEBPOS_ORDER_PAYMENTS, $webposOrderPayments);
    }

    /**
     * Gets the Webpos order payments for the order.
     *
     * @return \Magestore\Webpos\Api\Data\Payment\OrderPaymentInterface[]|null Webpos staff name.
     */
    public function getWebposOrderPayments()
    {
        if ($this->getData(OrderInterface::WEBPOS_ORDER_PAYMENTS) == null) {
            $this->setData(
                OrderInterface::WEBPOS_ORDER_PAYMENTS,
                $this->_orderPaymentCollectionFactory->create()
                    ->addFieldToFilter('order_id', $this->getEntityId())->getItems()
            );
        }
        return $this->getData(OrderInterface::WEBPOS_ORDER_PAYMENTS);
    }

    /**
     * Sets re-order params.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\ItemsInfoBuyInterface $itemsInfoBuy
     * @return $this
     */
    public function setItemsInfoBuy($itemsInfoBuy)
    {
        return $this->setData(OrderInterface::ITEMS_INFO_BUY, $itemsInfoBuy);
    }

    /**
     * Gets re-order params.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\ItemsInfoBuyInterface.
     */
    public function getItemsInfoBuy()
    {
        $itemsBuyRequest = $this->_objectManager->create('Magestore\Webpos\Api\Data\Checkout\ItemsInfoBuyInterface');
        $items = $this->getAllVisibleItems();
        if (count($items) > 0) {
            $itemsInfoBuy = [];
            foreach ($items as $item) {
                $labels = [];
                $itemInfo = $this->_objectManager->create('Magestore\Webpos\Api\Data\Checkout\InfoBuyInterface');
                if (!is_null($item->getProduct()) && $item->getProduct()->getTypeId() != 'customsale') {
                    $itemInfo->setId($item->getProduct()->getId());
                }
                $baseOriginalPrice = ($item->getBaseOriginalPrice()) ? $item->getBaseOriginalPrice() : "";
                $originalPrice = ($item->getOriginalPrice()) ? $item->getOriginalPrice() : "";
                $itemInfo->setBaseOriginalPrice($baseOriginalPrice);
                $itemInfo->setOriginalPrice($originalPrice);
                $itemInfo->setUnitPrice($item->getPrice());
                $itemInfo->setBaseUnitPrice($item->getBasePrice());

                $bool = true;
                if($item->getBaseOriginalPrice() == $item->getBaseRowTotalInclTax()){
                    $bool = false;
                }
                $itemInfo->setHasCustomPrice($bool);
                
                $labels = array_merge($labels, $this->getBundleOptionsLabel($item->getProductOptionByCode("bundle_options")));
                $labels = array_merge($labels, $this->getOptionsLabel($item->getProductOptionByCode("attributes_info")));
                $labels = array_merge($labels, $this->getOptionsLabel($item->getProductOptionByCode("options")));
                $info = $item->getBuyRequest()->toArray();
                if (count($info) > 0) {
                    foreach ($info as $key => $value) {
                        if (is_array($value)) {
                            $options = [];
                            foreach ($value as $code => $data) {
                                $options[] = [
                                    "id" => $code,
                                    "value" => $data
                                ];
                            }
                            $value = $options;
                        }
                        $itemInfo->setData($key, $value);
                    }
                }
                if (!is_null($item->getProduct()) && $item->getProduct()->getTypeId() == 'customsale') {
                    $itemInfo->setId('custom_item');
                    $itemInfo->setCustomSalesInfo(
                        [
                            [
                                'product_id' => 'customsale',
                                'product_name' => $item->getName(),
                                'unit_price' => $item->getPrice(),
                                'tax_class_id' => $item->getCustomTaxClassId(),
                                'is_virtual' => $item->getIsVirtual(),
                                'qty' => $item->getQtyOrdered()
                            ]
                        ]
                    );
                }
                $childs = $item->getChildrenItems();
                if (count($childs) > 0) {
                    $child = $childs[0];
                    $itemInfo->setChildId($child->getProductId());
                }
                $itemInfo->setData(InfoBuyInterface::KEY_OPTIONS_LABEL, implode(', ', $labels));
                $itemsInfoBuy[$item->getId()] = $itemInfo;
            }
            $itemsBuyRequest->setItems($itemsInfoBuy);
        }
        return $itemsBuyRequest;
    }

    /**
     *
     * @param array $options
     * @return array
     */
    protected function getBundleOptionsLabel($options)
    {
        $labels = [];
        if ($options) {
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    foreach ($option['value'] as $data) {
                        $labels[] = $data['qty'] . "x " . $data['title'];
                    }
                }
            }
        }
        return $labels;
    }

    /**
     *
     * @param array $options
     * @return array
     */
    protected function getOptionsLabel($options)
    {
        $labels = [];
        if ($options) {
            foreach ($options as $option) {
                $labels[] = $option['value'];
            }
        }
        return $labels;
    }

    /**
     * Webpos Paypal Invoice Id.
     *
     * @return string.
     */
    public function getWebposPaypalInvoiceId()
    {
        return $this->getData(OrderInterface::WEBPOS_PAYPAL_INVOICE_ID);
    }

    /**
     * @param string $paypalInvoiceId
     * @return $this
     */
    public function setWebposPaypalInvoiceId($paypalInvoiceId)
    {
        return $this->setData(OrderInterface::WEBPOS_PAYPAL_INVOICE_ID, $paypalInvoiceId);
    }

    /**
     * Webpos Init Data.
     *
     * @return string.
     */
    public function getWebposInitData()
    {
        return $this->getData(OrderInterface::WEBPOS_INIT_DATA);
    }

    /**
     * @param string $initData
     * @return $this
     */
    public function setWebposInitData($initData)
    {
        return $this->setData(OrderInterface::WEBPOS_INIT_DATA, $initData);
    }

//    /**
//     * @param string $initData
//     * @return $this
//     */
//    public function getCreatedAt(){
//        $createdAt = $this->setData(OrderInterface::CREATED_AT);
//        return $this->convertDate($createdAt);
//    }
//
//    /**
//     * @param $dateString
//     * @return mixed
//     */
//    public function convertDate($dateString){
//        try {
//            $dateString = $this->dateTime->date($dateString, \Varien_Date::DATETIME_INTERNAL_FORMAT)->toString('Y-M-d H:m:s');
//        }
//        catch (\Exception $e)
//        {
//            $dateString = $this->dateTime->date($dateString, \Varien_Date::DATETIME_INTERNAL_FORMAT)->toString('Y-M-d H:m:s');
//        }
//        return $dateString;
//
//    }

    /**
     * Sets the Webpos shift ID for the order.
     *
     * @param string $webposShiftId
     * @return $this
     */
    public function setWebposShiftId($webposShiftId)
    {
        return $this->setData(OrderInterface::WEBPOS_SHIFT_ID, $webposShiftId);
    }

    /**
     * Gets the Webpos shift ID for the order.
     *
     * @return string|null Webpos staff ID.
     */
    public function getWebposShiftId()
    {
        return $this->getData(OrderInterface::WEBPOS_SHIFT_ID);
    }
}
