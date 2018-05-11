<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Cart;

interface QuoteItemInterface extends \Magento\Quote\Api\Data\CartItemInterface
{

    const ITEM_ID = 'item_id';
    const UNIT_PRICE = 'unit_price';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const TAX_PERCENT = 'tax_percent';
    const TAX_AMOUNT = 'tax_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    const IS_VIRTUAL = 'is_virtual';
    const HAS_ERROR = 'has_error';
    const QTY_INCREMENT = 'qty_increment';
    const MAXIMUM_QTY = 'maximum_qty';
    const MINIMUM_QTY = 'minimum_qty';
    const SAVED_ONLINE_ITEM = 'saved_online_item';
    const IMAGE_URL = 'image_url';
    const OFFLINE_ITEM_ID = 'offline_item_id';
    const PRODUCT_ID = 'product_id';
    const PRICE_INCL_TAX = 'price_incl_tax';

    /**
     * Sets item id
     *
     * @param string $itemId
     * @return $this
     */
    public function setItemId($itemId);
    
    /**
     * Gets item id
     *
     * @return string.
     */
    public function getItemId();

    /**
     * Sets unit price
     *
     * @param string $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice);

    /**
     * Gets unit price
     *
     * @return string.
     */
    public function getUnitPrice();

    /**
     * Sets discount amount
     *
     * @param string $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount);

    /**
     * Gets discount amount
     *
     * @return string.
     */
    public function getDiscountAmount();

    /**
     * Sets base discount amount
     *
     * @param string $baseDiscountAmount
     * @return $this
     */
    public function setBaseDiscountAmount($baseDiscountAmount);

    /**
     * Gets base discount amount
     *
     * @return string.
     */
    public function getBaseDiscountAmount();

    /**
     * Sets tax percent
     *
     * @param string $taxPercent
     * @return $this
     */
    public function setTaxPercent($taxPercent);

    /**
     * Gets tax percent
     *
     * @return string.
     */
    public function getTaxPercent();

    /**
     * Sets base tax amount
     *
     * @param string $taxAmount
     * @return $this
     */
    public function setTaxAmount($taxAmount);

    /**
     * Gets base tax amount
     *
     * @return string.
     */
    public function getTaxAmount();

    /**
     * Sets base tax amount
     *
     * @param string $baseTaxAmount
     * @return $this
     */
    public function setBaseTaxAmount($baseTaxAmount);

    /**
     * Gets base tax amount
     *
     * @return string.
     */
    public function getBaseTaxAmount();

    /**
     * Sets is virtual
     *
     * @param string $isVirtual
     * @return $this
     */
    public function setIsVirtual($isVirtual);

    /**
     * Gets is virtual
     *
     * @return string.
     */
    public function getIsVirtual();

    /**
     * Sets has error
     *
     * @param string $hasError
     * @return $this
     */
    public function setHasError($hasError);

    /**
     * Gets has error
     *
     * @return string.
     */
    public function getHasError();

    /**
     * Sets qty increment
     *
     * @param string $qtyIncrement
     * @return $this
     */
    public function setQtyIncrement($qtyIncrement);

    /**
     * Gets qty increment
     *
     * @return string.
     */
    public function getQtyIncrement();

    /**
     * Sets maximum qty
     *
     * @param string $maximumQty
     * @return $this
     */
    public function setMaximumQty($maximumQty);

    /**
     * Gets maximum qty
     *
     * @return string.
     */
    public function getMaximumQty();

    /**
     * Sets minimum qty
     *
     * @param string $minimumQty
     * @return $this
     */
    public function setMinimumQty($minimumQty);

    /**
     * Gets minimum qty
     *
     * @return string.
     */
    public function getMinimumQty();

    /**
     * Sets saved online item
     *
     * @param string $savedOnlineItem
     * @return $this
     */
    public function setSavedOnlineItem($savedOnlineItem);

    /**
     * Gets saved online item
     *
     * @return string.
     */
    public function getSavedOnlineItem();

    /**
     * Sets image url
     *
     * @param string $imageUrl
     * @return $this
     */
    public function setImageUrl($imageUrl);

    /**
     * Gets image url
     *
     * @return string.
     */
    public function getImageUrl();

    /**
     * Sets offline item id
     *
     * @param string $offlineItemId
     * @return $this
     */
    public function setOfflineItemId($offlineItemId);

    /**
     * Gets offline item id
     *
     * @return string.
     */
    public function getOfflineItemId();

    /**
     * Sets product id
     *
     * @param string $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Gets product id
     *
     * @return string.
     */
    public function getProductId();

    /**
     * Sets price incl tax
     *
     * @param float  $priceInclTax
     * @return $this.
     */
    public function setPriceInclTax($priceInclTax);

    /**
     * Gets price incl tax
     *
     * @return float
     */
    public function getPriceInclTax();
}
