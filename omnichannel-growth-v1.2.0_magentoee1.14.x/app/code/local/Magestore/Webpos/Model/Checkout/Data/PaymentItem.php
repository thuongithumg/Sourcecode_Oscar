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
 * Class Magestore_Webpos_Model_Checkout_Data_PaymentItem
 */
class Magestore_Webpos_Model_Checkout_Data_PaymentItem extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Checkout_PaymentItemInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCode(){
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getRealAmount(){
        return $this->getData(self::REAL_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setRealAmount($paidAmount){
        return $this->setData(self::REAL_AMOUNT, $paidAmount);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseRealAmount(){
        return $this->getData(self::BASE_REAL_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseRealAmount($basePaidAmount){
        return $this->setData(self::BASE_REAL_AMOUNT, $basePaidAmount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAmount(){
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setAmount($amount){
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBaseAmount(){
        return $this->getData(self::BASE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBaseAmount($baseAmount){
        return $this->setData(self::BASE_AMOUNT, $baseAmount);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTitle(){
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTitle($title){
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getReferenceNumber(){
        return $this->getData(self::REFERENCE_NUMBER);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setReferenceNumber($refenrenceNumber){
        return $this->setData(self::REFERENCE_NUMBER, $refenrenceNumber);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAdditionalData(){
        return $this->getData(self::ADDITIONAL_DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setAdditionalData($additionalData){
        return $this->setData(self::ADDITIONAL_DATA, $additionalData);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getIsPayLater(){
        return ($this->getData(self::IS_PAYLATER))?true:false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setIsPayLater($isPayLater){
        return $this->setData(self::IS_PAYLATER, $isPayLater);
    }


    /**
     * Returns till Id
     *
     * @return string
     */
    public function getTillId(){
        return $this->getData(self::TILL_ID);
    }

    /**
     * Sets till Id
     *
     * @param string $tillId
     * @return $this
     */
    public function setTillId($tillId){
        return $this->setData(self::TILL_ID, $tillId);
    }
}