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
class PaymentItem extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\PaymentItemInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCode(){
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCode($code){
        return $this->setData(self::KEY_CODE, $code);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getRealAmount(){
        return $this->getData(self::KEY_REAL_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setRealAmount($paidAmount){
        return $this->setData(self::KEY_REAL_AMOUNT, $paidAmount);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseRealAmount(){
        return $this->getData(self::KEY_BASE_REAL_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseRealAmount($basePaidAmount){
        return $this->setData(self::KEY_BASE_REAL_AMOUNT, $basePaidAmount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAmount(){
        return $this->getData(self::KEY_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setAmount($amount){
        return $this->setData(self::KEY_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseAmount(){
        return $this->getData(self::KEY_BASE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseAmount($baseAmount){
        return $this->setData(self::KEY_BASE_AMOUNT, $baseAmount);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTitle(){
        return $this->getData(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTitle($title){
        return $this->setData(self::KEY_TITLE, $title);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getReferenceNumber(){
        return $this->getData(self::KEY_REFERENCE_NUMBER);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setReferenceNumber($refenrenceNumber){
        return $this->setData(self::KEY_REFERENCE_NUMBER, $refenrenceNumber);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCardType(){
        return $this->getData(self::KEY_CARD_TYPE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCardType($cardType){
        return $this->setData(self::KEY_CARD_TYPE, $cardType);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAdditionalData(){
        return $this->getData(self::KEY_ADDITIONAL_DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setAdditionalData($additionalData){
        return $this->setData(self::KEY_ADDITIONAL_DATA, $additionalData);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getIsPayLater(){
        return ($this->getData(self::KEY_IS_PAYLATER))?true:false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setIsPayLater($isPayLater){
        return $this->setData(self::KEY_IS_PAYLATER, $isPayLater);
    }


    /**
     * Returns shift Id
     *
     * @return string
     */
    public function getShiftId(){
        return $this->getData(self::KEY_SHIFT_ID);
    }

    /**
     * Sets shift Id
     *
     * @param string $shiftId
     * @return $this
     */
    public function setShiftId($shiftId){
        return $this->setData(self::KEY_SHIFT_ID, $shiftId);
    }
}