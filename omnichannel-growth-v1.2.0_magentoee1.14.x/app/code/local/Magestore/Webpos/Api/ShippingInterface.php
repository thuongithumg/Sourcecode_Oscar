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
 * Interface Magestore_Webpos_Api_ShippingInterface
 */
interface Magestore_Webpos_Api_ShippingInterface
{
    const CODE = 'code';
    const TITLE = 'title';
    const PRICE = 'price';
    const DESCRIPTION = 'description';
    const ERROR_MESSAGE = 'error_message';
    const PRICE_TYPE = 'price_type';
    const IS_DEFAULT = 'is_default';

    const YES = '1';
    const NO = '0';

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return string
     */
    public function setCode($code);
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return mixed
     */
    public function setTitle($title);
    /**
     * @return string
     */
    public function getPrice();

    /**
     * @param string $price
     * @return mixed
     */
    public function setPrice($price);
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return mixed
     */
    public function setDescription($description);
    /**
     * @return string
     */
    public function getIsDefault();

    /**
     * @param string $isDefault
     * @return mixed
     */
    public function setIsDefault($isDefault);
    /**
     * @return string
     */
    public function getErrorMessage();

    /**
     * @param string $errorMessage
     * @return mixed
     */
    public function setErrorMessage($errorMessage);
    /**
     * @return string
     */
    public function getPriceType();

    /**
     * @param string $priceType
     * @return mixed
     */
    public function setPriceType($priceType);
}
