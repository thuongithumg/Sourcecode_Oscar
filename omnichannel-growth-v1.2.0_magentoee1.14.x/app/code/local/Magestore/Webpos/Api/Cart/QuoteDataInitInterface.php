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
 * Interface Magestore_Webpos_Api_Cart_QuoteDataInitInterface
 */
interface Magestore_Webpos_Api_Cart_QuoteDataInitInterface
{
    /**#@+
     * Data key
     */
    const QUOTE_ID = 'quote_id';
    const STORE_ID = 'store_id';
    const TILL_ID = 'till_id';
    const SHIFT_ID = 'shift_id';
    const CUSTOMER_ID = 'customer_id';
    const CURRENCY_ID = 'currency_id';

    const QUOTE_INIT = 'quote_init';
    const PAYMENT = 'payment';
    const SHIPPING = 'shipping';
    const TOTALS = 'totals';
    const ITEMS = 'items';
    const BILLING_ADDRESS = 'billing_address';
    const SHIPPING_ADDRESS = 'shipping_address';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_FULLNAME = 'customer_fullname';
    const CUSTOMER_DATA = 'customer_data';

    const CART_DISCOUNT_NAME = 'cart_discount_name';
    const CART_DISCOUNT_TYPE = 'cart_discount_type';
    const CART_DISCOUNT_VALUE = 'cart_discount_value';
    /**#@- */

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getStoreId();

    /**
     * @param string $storeId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getCustomerId();

    /**
     * @param string $customerId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string
     */
    public function getCurrencyId();

    /**
     * @param string $currencyId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setCurrencyId($currencyId);

    /**
     * @return string
     */
    public function getTillId();

    /**
     * @param string $tillId
     * @return Magestore_Webpos_Api_Cart_QuoteDataInitInterface
     */
    public function setTillId($tillId);
}
