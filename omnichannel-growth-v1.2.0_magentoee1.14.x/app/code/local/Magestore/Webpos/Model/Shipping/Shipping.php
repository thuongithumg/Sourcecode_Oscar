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


class Magestore_Webpos_Model_Shipping_Shipping extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_ShippingInterface
{
    /**
     * @return string
     */
    public function getCode(){
        return $this->getData(self::CODE);
    }

    /**
     * @param string $code
     * @return string
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
    }
    /**
     * @return string
     */
    public function getTitle(){
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return mixed
     */
    public function setTitle($title){
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getIsDefault(){
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * @param string $isDefault
     * @return mixed
     */
    public function setIsDefault($isDefault){
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * @return string
     */
    public function getPrice(){
        return $this->getData(self::PRICE);
    }

    /**
     * @param string $price
     * @return mixed
     */
    public function setPrice($price){
        return $this->setData(self::PRICE, $price);
    }
    /**
     * @return string
     */
    public function getDescription(){
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return mixed
     */
    public function setDescription($description){
        return $this->setData(self::DESCRIPTION, $description);
    }
    /**
     * @return string
     */
    public function getErrorMessage(){
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * @param string $errorMessage
     * @return mixed
     */
    public function setErrorMessage($errorMessage){
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }
    /**
     * @return string
     */
    public function getPriceType(){
        return $this->getData(self::PRICE_TYPE);
    }

    /**
     * @param string $priceType
     * @return mixed
     */
    public function setPriceType($priceType){
        return $this->setData(self::PRICE_TYPE, $priceType);
    }
}
