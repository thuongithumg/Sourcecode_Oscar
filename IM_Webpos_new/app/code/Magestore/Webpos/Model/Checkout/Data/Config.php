<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Checkout\Data;

/**
 * 
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Config extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\ConfigInterface
{
    /**
     * Returns the applied rules ids.
     *
     * @return string . Otherwise, null.
     */
    public function getAppliedRuleIds(){
        return $this->getData(self::KEY_APPLIED_RULE_IDS);
    }

    /**
     * Sets the applied rules ids.
     *
     * @param string $appliedRuleIds
     * @return $this
     */
    public function setAppliedRuleIds($appliedRuleIds){
        return $this->setData(self::KEY_APPLIED_RULE_IDS, $appliedRuleIds);
    }

    /**
     * Returns the send sale email.
     *
     * @return string . Otherwise, null.
     */
    public function getSendSaleEmail(){
        return $this->getData(self::KEY_SEND_SALE_EMAIL);
    }

    /**
     * Sets the send sale email.
     *
     * @param string $sendSaleEmail
     * @return $this
     */
    public function setSendSaleEmail($sendSaleEmail){
        return $this->setData(self::KEY_SEND_SALE_EMAIL, $sendSaleEmail);
    }

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
    public function getIsOnhold(){
        return $this->getData(self::KEY_IS_ONHOLD);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setIsOnhold($isOnhold){
        return $this->setData(self::KEY_IS_ONHOLD, $isOnhold);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getInitData(){
        return $this->getData(self::KEY_INIT_DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setInitData($initData){
        return $this->setData(self::KEY_INIT_DATA, $initData);
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

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getDiscountApply(){
        return $this->getData(self::KEY_DISCOUNT_APPLY);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setDiscountApply($discountApply){
        return $this->setData(self::KEY_DISCOUNT_APPLY, $discountApply);
    }
}