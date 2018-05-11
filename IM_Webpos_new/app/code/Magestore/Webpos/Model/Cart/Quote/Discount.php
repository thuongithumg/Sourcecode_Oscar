<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Cart\Quote;

class Discount extends \Magento\SalesRule\Model\Quote\Discount
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\Webpos\Helper\Currency
     */
    protected $helperCurrency;

    /**
     * Total items price to calculate discount
     *
     * @var array
     */
    protected $total = [];

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magestore\Webpos\Helper\Currency $helperCurrency
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Webpos\Helper\Data $helper,
        \Magestore\Webpos\Helper\Currency $helperCurrency
    )
    {
        $this->setCode('discount');
        $this->eventManager = $eventManager;
        $this->calculator = $validator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        $this->helperCurrency = $helperCurrency;
    }

    /**
     * Collect address discount amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        
        $type = $quote->getData('webpos_cart_discount_type');
        $discountValue = $quote->getData('webpos_cart_discount_value');
        $discountName = $quote->getData('webpos_cart_discount_name');
        if (isset($type) && isset($discountValue) && $discountValue > 0) {
            if ($type == '%') {
                $quote->setWebposDiscountAmount(0)
                    ->setWebposDiscountPercent($discountValue)
                    ->setWebposDiscountDesc($discountName);
            } else {
                $quote->setWebposDiscountAmount($discountValue)
                    ->setWebposDiscountPercent(0)
                    ->setWebposDiscountDesc($discountName);
            }
        } else {
            return $this;
        }

        $address = $shippingAssignment->getShipping()->getAddress();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }

        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        $maxPercent = 100;
        $quoteCurrency = $quote->getQuoteCurrencyCode();
        $baseCurrency = $quote->getBaseCurrencyCode();
        $showItemPriceInclTax = $this->helper->getStoreConfig('tax/cart_display/price');
        if ($type == '%') {
            $discountPercent = ($maxPercent > $discountValue) ? $discountValue : $maxPercent;
            foreach ($items as $item) {
                if ($item->getParentItemId()) continue;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $itemBasePrice = ($showItemPriceInclTax != 1) ? $child->getBasePriceInclTax() : $child->getBasePrice();
                        $baseDiscount = $child->getQty() * $itemBasePrice * $discountPercent / 100;
                        $baseDiscount = min($baseDiscount, $child->getQty() * $itemBasePrice - $child->getBaseDiscountAmount());

                        $itemPrice = ($showItemPriceInclTax != 1) ? $child->getPriceInclTax() : $child->getPrice();
                        $discount = $child->getQty() * $itemPrice * $discountPercent / 100;
                        $discount = min($discount, $child->getQty() * $itemPrice - $child->getDiscountAmount());

//                        $discount = ($baseCurrency == $quoteCurrency)?$baseDiscount:
//                                    $this->helperCurrency->currencyConvert($baseDiscount, $baseCurrency, $quoteCurrency);

                        $child->setDiscountAmount($child->getDiscountAmount() + $discount)
                            ->setBaseDiscountAmount($child->getBaseDiscountAmount() + $baseDiscount);

                        $this->_addAmount(-$discount);
                        $this->_addBaseAmount(-$baseDiscount);
                    }
                } else {
                    $itemBasePrice = ($showItemPriceInclTax != 1) ? $item->getBasePriceInclTax() : $item->getBasePrice();
                    $baseDiscount = $item->getQty() * $itemBasePrice * $discountPercent / 100;
                    $baseDiscount = min($baseDiscount, $item->getQty() * $itemBasePrice - $item->getBaseDiscountAmount());

                    $itemPrice = ($showItemPriceInclTax != 1) ? $item->getPriceInclTax() : $item->getRowTotal() / $item->getQty();
                    $discount = $item->getQty() * $itemPrice * $discountPercent / 100;
                    $discount = min($discount, $item->getQty() * $itemPrice - $item->getDiscountAmount());
//                    $discount = ($baseCurrency == $quoteCurrency)?$baseDiscount:
//                        $this->helperCurrency->currencyConvert($baseDiscount, $baseCurrency, $quoteCurrency);

                    $item->setDiscountAmount($item->getDiscountAmount() + $discount)
                        ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseDiscount);
                    $this->_addAmount(-$discount);
                    $this->_addBaseAmount(-$baseDiscount);
                }
            }
            if ($address->getShippingAmount()) {
                $baseDiscount = $address->getBaseShippingAmount() * $discountPercent / 100;
                $baseDiscount = min($baseDiscount, $address->getBaseShippingAmount() - $address->getBaseShippingDiscountAmount());

                $discount = $address->getShippingAmount() * $discountPercent / 100;
                $discount = min($discount, $address->getShippingAmount() - $address->getShippingDiscountAmount());

                $address->setShippingDiscountAmount($address->getShippingDiscountAmount() + $discount)
                    ->setBaseShippingDiscountAmount($address->getBaseShippingDiscountAmount() + $baseDiscount);

                $this->_addAmount(-$discount);
                $this->_addBaseAmount(-$baseDiscount);
            }
            $this->_addCustomDiscountDescription($address);
            return $this;
        }

        /** Prepare total before discount */
        $this->initTotals($items, $address);

        if ($this->total['base_items_price'] <= 0) {
            return $this;
        }

        $baseDiscountValue = ($baseCurrency == $quoteCurrency) ? $discountValue :
            $this->helperCurrency->currencyConvert($discountValue, $quoteCurrency, $baseCurrency);

        $baseDiscountTotal = $discountTotal = 0;

        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            $qty = $item->getTotalQty();
            $baseItemPrice = $this->getItemBasePrice($item) - $item->getBaseDiscountAmount()/$qty;
            $itemPrice = $this->getItemPrice($item) - $item->getDiscountAmount()/$qty;
            $discountRate = $baseItemPrice * $qty / $this->total['base_items_price'];

            $baseItemDiscountAmount = $baseDiscountValue * $discountRate;
            $baseItemDiscountAmount = $this->priceCurrency->round($baseItemDiscountAmount);
            $baseItemDiscountAmount = min($baseItemDiscountAmount, $baseItemPrice * $qty);

            $itemDiscountAmount = $this->priceCurrency->convert($baseItemDiscountAmount, $quote->getStore());
            $itemDiscountAmount = $this->priceCurrency->round($itemDiscountAmount);
            $itemDiscountAmount = min($itemDiscountAmount, $itemPrice * $qty);

            $item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseItemDiscountAmount)
                ->setDiscountAmount($item->getDiscountAmount() + $itemDiscountAmount);

            $total->addBaseTotalAmount($this->getCode(), -$baseItemDiscountAmount);
            $total->addTotalAmount($this->getCode(), -$itemDiscountAmount);

            $baseDiscountTotal += $baseItemDiscountAmount;
            $discountTotal += $itemDiscountAmount;
        }

        if ($baseDiscountValue > $baseDiscountTotal) {
            $shippingAmount = $address->getShippingAmountForDiscount();
            if ($shippingAmount !== null) {
                $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
            } else {
                $baseShippingAmount = $address->getBaseShippingAmount();
            }
            $baseShippingAmount = $baseShippingAmount - $address->getBaseShippingDiscountAmount();


            $baseDiscountShipping = $baseDiscountValue - $baseDiscountTotal;
            $baseDiscountShipping = min($baseDiscountShipping, $baseShippingAmount);
            $baseDiscountShipping = $this->priceCurrency->round($baseDiscountShipping);

            $discountShipping = $this->priceCurrency->convert($baseDiscountShipping, $quote->getStore());
            $discountShipping = $this->priceCurrency->round($discountShipping);
            
            $address->setBaseShippingDiscountAmount($address->getBaseShippingDiscountAmount() + $baseDiscountShipping)
                ->setShippingDiscountAmount($address->getShippingDiscountAmount() + $discountShipping);
            
            $total->addBaseTotalAmount($this->getCode(), -$baseDiscountShipping);
            $total->addTotalAmount($this->getCode(), -$discountShipping);
        }
        
        $this->_addCustomDiscountDescription($address);
        return $this;
    }

    /**
     * add custom discount label
     * @param $address
     */
    protected function _addCustomDiscountDescription($address)
    {
        $description = $address->getDiscountDescriptionArray();

        $label = $address->getQuote()->getWebposDiscountDesc();
        if (!$label) {
            $label = __('Custom Discount');
        }
        $description[0] = $label;

        $address->setDiscountDescriptionArray($description);
        $this->calculator->prepareDescription($address);
    }

    /**
     * Calculate quote totals items before discount
     *
     * @param $items
     * @param $address
     * @return $this
     */
    public function initTotals($items, $address)
    {
        $totalItemsPrice = 0;
        $totalBaseItemsPrice = 0;
        $validItemsCount = 0;
        foreach ($items as $item) {
            //Skipping child items to avoid double calculations
            if ($item->getParentItemId()) {
                continue;
            }

            $qty = $item->getTotalQty();
            $totalItemsPrice += $this->getItemPrice($item) * $qty - $item->getDiscountAmount();
            $totalBaseItemsPrice += $this->getItemBasePrice($item) * $qty - $item->getBaseDiscountAmount();
            $validItemsCount++;
        }

        $this->total = [
            'items_price' => $totalItemsPrice,
            'base_items_price' => $totalBaseItemsPrice,
            'items_count' => $validItemsCount,
        ];
        return $this;
    }


    /**
     * Return item base price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $price : $item->getBaseCalculationPrice();
    }

    /**
     * Return item price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return $price === null ? $item->getCalculationPrice() : $price;
    }
}
