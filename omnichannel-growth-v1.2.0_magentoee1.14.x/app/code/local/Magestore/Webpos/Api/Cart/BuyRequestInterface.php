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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Interface Magestore_Webpos_Api_Cart_BuyRequestInterface
 */
interface Magestore_Webpos_Api_Cart_BuyRequestInterface
{
    /**#@+
     * Data key
     */
    const ITEM_ID = 'item_id';
    const ID = 'id';
    const QTY = 'qty';
    const SUPER_ATTRIBUTE = 'super_attribute';
    const OPTIONS = 'options';
    const BUNDLE_OPTION = 'bundle_option';
    const BUNDLE_OPTION_QTY = 'bundle_option_qty';
    const CUSTOM_PRICE = 'custom_price';
    const USE_DISCOUNT = 'use_discount';
    const IS_CUSTOM_SALE = 'is_custom_sale';
    const QUOTE_ITEM_DATA = 'quote_item_data';
    const EXTENSION_DATA = 'extension_data';

    const CUSTOM_SALE_ID = 'customsale';
    const CUSTOM_SALE_PRODUCT_SKU = 'webpos-customsale';
    const CUSTOM_SALE_TAX_CLASS_ID = 0;
    const CUSTOMERCREDIT_AMOUNT = 'amount';
    const CUSTOMERCREDIT_PRICE_AMOUNT = 'credit_price_amount';
    /**#@- */

    /**
     * @return string
     */
    public function getItemId();

    /**
     * @param string $itemId
     * @return string
     */
    public function setItemId($itemId);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return string
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getQty();

    /**
     * @param string $qty
     * @return string
     */
    public function setQty($qty);

    /**
     * @return array
     */
    public function getSuperAttribute();

    /**
     * @param array $superAttribute
     * @return array
     */
    public function setSuperAttribute($superAttribute);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $options
     * @return array
     */
    public function setOptions($options);

    /**
     * @return array
     */
    public function getBundleOption();

    /**
     * @param array $bundleOptions
     * @return array
     */
    public function setBundleOption($bundleOptions);

    /**
     * @return array
     */
    public function getBundleOptionQty();

    /**
     * @param array $bundleOptionsQty
     * @return array
     */
    public function setBundleOptionQty($bundleOptionsQty);

    /**
     * @return string
     */
    public function getCustomPrice();

    /**
     * @param string $customPrice
     * @return string
     */
    public function setCustomPrice($customPrice);

    /**
     * @return string
     */
    public function getUseDiscount();

    /**
     * @param string $useDiscount
     * @return string
     */
    public function setUseDiscount($useDiscount);

    /**
     * @return boolean
     */
    public function getIsCustomSale();

    /**
     * @param boolean $isCustomSale
     * @return boolean
     */
    public function setIsCustomSale($isCustomSale);

    /**
     * @return array
     */
    public function getQuoteItemData();

    /**
     * @param array $quoteItemData
     * @return array
     */
    public function setQuoteItemData($quoteItemData);

    /**
     * @return array
     */
    public function getExtensionData();

    /**
     * @param array $extensionData
     * @return array
     */
    public function setExtensionData($extensionData);

    /**
     * get item extension data.
     *
     * @return string
     */
    public function getAmount();

    /**
     * set item extension data.
     *
     * @param string
     * @return $this
     */
    public function setAmount($amount);

    /**
     * get item extension data.
     *
     * @return string
     */
    public function getCreditPriceAmount();

    /**
     * set item extension data.
     *
     * @param string
     * @return $this
     */
    public function setCreditPriceAmount($amount);
}
