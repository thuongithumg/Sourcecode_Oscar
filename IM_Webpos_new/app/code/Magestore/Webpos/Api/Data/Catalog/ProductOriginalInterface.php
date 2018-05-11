<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface ProductOriginalInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const SPECIAL_PRICE = 'special_price';
    const SPECIAL_FROM_DATE = 'special_from_date';
    const SPECIAL_TO_DATE = 'special_to_date';
    const SHORT_DESCRIPTION = 'short_description';
    const DESCRIPTION = 'description';
    const MEDIA_GALLERY = 'media_gallery';
    const TAX_CLASS_ID = 'tax_class_id';
    const IS_IN_STOCK = 'is_in_stock';
    const MIN_SALE_QTY = 'min_sale_qty';
    const MAX_SALE_QTY = 'max_sale_qty';
    const QTY = 'qty';
    const ENABLE_QTY_INCREMENTS = 'enable_qty_increments';
    const QTY_INCREMENTS = 'qty_increments';
    const IS_QTY_DECIMAL = 'is_qty_decimal';

    /**#@-*/

    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Product type id
     *
     * @return string|null
     */
    public function getTypeId();

    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Product price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Product final price
     *
     * @return float|null
     */
    public function getFinalPrice();

    /**
     * Product special price
     *
     * @return float|null
     */
    public function getSpecialPrice();

    /**
     * Product special price from date
     *
     * @return string|null
     */
    public function getSpecialFromDate();

    /**
     * Product special price to date
     *
     * @return string|null
     */
    public function getSpecialToDate();


    /**
     * Product short description
     *
     * @return string|null
     */
    public function getShortDescription();

    /**
     * Product description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Product status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Product updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Product weight
     *
     * @return float|null
     */
    public function getWeight();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Catalog\Api\Data\ProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes);

    /**
     * Get category ids by product
     *
     * @return string
     */
    public function getCategoryIds();

    /**
     * Get product qty increment
     *
     * @return float|null
     */
    public function getQtyIncrement();

    /**
     * Sets product image from it's child if possible
     *
     * @return string
     */
    public function getImage();

    /**
     * Retrieve images
     *
     * @return string[]
     */
    public function getImages();

    /**
     * Retrieve image
     *
     * @return string/null
     */
    //public function getImage();

    /**
     * Get stock data by product
     *
     * @return anyType
     */
    public function getStock();

    /**
     * Gets list of product tier prices
     *
     * @return \Magento\Catalog\Api\Data\ProductTierPriceInterface[]|null
     */
    public function getTierPrices();

    /**
     * Retrieve product tax class id
     *
     * @return int
     */
    public function getTaxClassId();

    /**
     * Retrieve product has option
     *
     * @return int
     */
    public function hasOptions();

    /**
     * get search string to search product
     *
     * @return string
     */
    public function getSearchString();

    /**
     * get barcode string
     *
     * @return string
     */
    public function getBarcodeString();

    /**
     * get is virtual
     *
     * @return boolean
     */
    public function getIsVirtual();

    /**
     * Product credit value
     *
     * @return string
     */
    public function getCustomercreditValue();

    /**
     * Product credit value
     *
     * @return int
     */
    public function getStorecreditType();

    /**
     * Product credit value
     *
     * @return float|null
     */
    public function getStorecreditRate();

    /**
     * Product credit min value
     *
     * @return float|null
     */
    public function getStorecreditMin();

    /**
     * Product credit max value
     *
     * @return float|null
     */
    public function getStorecreditMax();


    /**
     *
     * @return float
     */
    public function getGiftvoucherValue();

    /**
     *
     * @return int
     */
    public function getGiftvoucherSelectPriceType();

    /**
     *
     * @return float
     */
    public function getGiftvoucherPrice();

    /**
     *
     * @return float
     */
    public function getGiftvoucherFrom();

    /**
     *
     * @return float
     */
    public function getGiftvoucherTo();
    /**
     *
     * @return string
     */
    public function getGiftvoucherDropdown();

    /**
     *
     * @return int
     */
    public function getGiftvoucherPriceType();
    /**
     *
     * @return string
     */
    public function getGiftvoucherTemplate();
    /**
     *
     * @return int
     */
    public function getGiftvoucherType();

    /**
     *
     * @return int
     */
    public function getAllowOpenAmount();/**
     *
     * @return int
     */
    public function getGiftcardAmounts();
    /**
     *
     * @return int
     */
    public function getOpenAmountMin();
    /**
     *
     * @return int
     */
    public function getOpenAmountMax();
    /**
     *
     * @return int
     */
    public function getGiftcardType();

    /**
     * Get is in stock
     *
     * @return int
     */
    public function getIsInStock();

    /**
     * Get minimum qty
     *
     * @return float|null
     */
    public function getMinimumQty();

    /**
     * Get maximum qty
     *
     * @return float|null
     */
    public function getMaximumQty();

    /**
     * Get qty
     *
     * @return float|null
     */
    public function getQty();

    /**
     * Get back orders
     *
     * @return float|null
     */
    public function getBackorders();

    /**
     * @return int
     */
    public function getIsSalable();

    /**
     * @return int
     */
    public function getQtyIncrements();

    /**
     * @return int
     */
    public function getEnableQtyIncrements();

    /**
     * @return int
     */
    public function getIsQtyDecimal();
}
