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


class Magestore_Webpos_Model_Payment_Payment extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_PaymentInterface
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
    public function getIconClass(){
        return $this->getData(self::ICON_CLASS);
    }

    /**
     * @param string $class
     * @return string
     */
    public function setIconClass($class){
        return $this->setData(self::ICON_CLASS, $class);
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
    public function getInfomation(){
        return $this->getData(self::INFORMATION);
    }

    /**
     * @param string $information
     * @return mixed
     */
    public function setInfomation($information){
        return $this->setData(self::INFORMATION, $information);
    }
    /**
     * @return string
     */
    public function getType(){
        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function setType($type){
        return $this->setData(self::TYPE, $type);
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
    public function getIsReferenceNumber(){
        return $this->getData(self::IS_REFERENCE_NUMBER);
    }

    /**
     * @param string $isReferenceNumber
     * @return mixed
     */
    public function setIsReferenceNumber($isReferenceNumber){
        return $this->setData(self::IS_REFERENCE_NUMBER, $isReferenceNumber);
    }
    /**
     * @return string
     */
    public function getIsPayLater(){
        return $this->getData(self::IS_PAY_LATER);
    }

    /**
     * @param string $isPayLater
     * @return mixed
     */
    public function setIsPayLater($isPayLater){
        return $this->setData(self::IS_PAY_LATER, $isPayLater);
    }

    /**
     * @return string
     */
    public function getMultiable(){
        return $this->getData(self::MULTIABLE);
    }

    /**
     * @param string $multiable
     * @return mixed
     */
    public function setMultiable($multiable){
        return $this->setData(self::MULTIABLE, $multiable);
    }

    /**
     * @return string
     */
    public function getTemplate(){
        return $this->getData(self::TEMPLATE);
    }

    /**
     * @param string $template
     * @return mixed
     */
    public function setTemplate($template){
        return $this->setData(self::TEMPLATE, $template);
    }

    /**
     * @return string
     */
    public function getFormData(){
        return $this->getData(self::FORM_DATA);
    }

    /**
     * @param string $formData
     * @return mixed
     */
    public function setFormData($formData){
        return $this->setData(self::FORM_DATA, $formData);
    }
}
