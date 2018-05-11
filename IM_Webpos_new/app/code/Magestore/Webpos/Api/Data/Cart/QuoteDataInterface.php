<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Cart;

interface QuoteDataInterface
{
    const CUSTOMER_NOTE = 'customer_note';
    const DISCOUNT_NAME = 'webpos_cart_discount_name';
    const DISCOUNT_TYPE = 'webpos_cart_discount_type';
    const DICOUNT_VALUE = 'webpos_cart_discount_value';
    const IS_WEB_VERSION = 'is_web_version';

    const YES = '1';
    const NO = '0';

    /**
     * Sets is web version
     *
     * @param string $isWebVersion
     * @return $this
     */
    public function setIsWebVersion($isWebVersion);
    
    /**
     * Gets is web version
     *
     * @return string.
     */
    public function getIsWebVersion();

    /**
     * Sets customer note
     *
     * @param string $customerNote
     * @return $this
     */
    public function setCustomerNote($customerNote);

    /**
     * Gets customer note
     *
     * @return string.
     */
    public function getCustomerNote();

    /**
     * Sets webpos cart discount name
     *
     * @param string $discountName
     * @return $this
     */
    public function setWebposCartDiscountName($discountName);

    /**
     * Gets webpos cart discount name
     *
     * @return string.
     */
    public function getWebposCartDiscountName();

    /**
     * Sets webpos cart discount type
     *
     * @param string $discountType
     * @return $this
     */
    public function setWebposCartDiscountType($discountType);

    /**
     * Gets webpos cart discount type
     *
     * @return string.
     */
    public function getWebposCartDiscountType();


    /**
     * Sets webpos cart discount value
     *
     * @param string $discountValue
     * @return $this
     */
    public function setWebposCartDiscountValue($discountValue);

    /**
     * Gets webpos cart discount value
     *
     * @return string.
     */
    public function getWebposCartDiscountValue();


}
