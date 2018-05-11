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
 * Class Magestore_Webpos_Model_Checkout_Data_Address
 */
class Magestore_Webpos_Model_Checkout_Data_Config extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Checkout_ConfigInterface
{
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getApplyPromotion(){
        return $this->getData(self::KEY_APPLY_PROMOTION);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setApplyPromotion($apply_promotion){
        return $this->setData(self::KEY_APPLY_PROMOTION, $apply_promotion);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getNote(){
        return $this->getData(self::KEY_NOTE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setNote($note){
        return $this->setData(self::KEY_NOTE, $note);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCreateInvoice(){
        return $this->getData(self::KEY_CREATE_INVOICE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCreateInvoice($createInvoice){
        return $this->setData(self::KEY_CREATE_INVOICE, $createInvoice);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCreateShipment(){
        return $this->getData(self::KEY_CREATE_SHIPMENT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCreateShipment($createShipment){
        return $this->setData(self::KEY_CREATE_SHIPMENT, $createShipment);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCartDiscountAmount(){
        return $this->getData(self::KEY_CART_DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCartDiscountAmount($cartDiscountAmount){
        return $this->setData(self::KEY_CART_DISCOUNT_AMOUNT, $cartDiscountAmount);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCartDiscountName(){
        return $this->getData(self::KEY_CART_DISCOUNT_NAME);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCartDiscountName($cartDiscountName){
        return $this->setData(self::KEY_CART_DISCOUNT_NAME, $cartDiscountName);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCurrencyCode(){
        return $this->getData(self::KEY_CURRENCY_CODE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCurrencyCode($currencyCode){
        return $this->setData(self::KEY_CURRENCY_CODE, $currencyCode);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCartBaseDiscountAmount(){
        return $this->getData(self::KEY_CART_BASE_DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCartBaseDiscountAmount($cartBaseDiscountAmount){
        return $this->setData(self::KEY_CART_BASE_DISCOUNT_AMOUNT, $cartBaseDiscountAmount);
    }
}