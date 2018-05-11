<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Payment;
use Magestore\Webpos\Api\Data\Payment\PaymentInterface;

/**
 * Class Magestore\Webpos\Model\Payment\Payment
 *
 */
class Payment extends \Magento\Framework\Model\AbstractModel implements
    \Magestore\Webpos\Api\Data\Payment\PaymentInterface
{
    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get title
     *
     * @api
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }


    /**
     * Set type
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get type
     *
     * @api
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set type id
     *
     * @api
     * @param string $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    /**
     * Get type id
     *
     * @api
     * @return string
     */
    public function getTypeId()
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * Set information
     *
     * @api
     * @param string $information
     * @return $this
     */
    public function setInformation($information)
    {
        return $this->setData(self::INFORMATION, $information);
    }

    /**
     * Get information
     *
     * @api
     * @return string|null
     */
    public function getInformation()
    {
        return $this->getData(self::INFORMATION);
    }

    /**
     * Get icon class
     *
     * @api
     * @return string|null
     */
    public function getIconClass()
    {
        return $this->getData(self::ICON_CLASS);
    }

    /**
     * Set icon class
     *
     * @api
     * @param string $iconClass
     * @return $this
     */
    public function setIconClass($iconClass)
    {
        return $this->setData(self::ICON_CLASS, $iconClass);
    }

    /**
     * Get is default
     *
     * @api
     * @return string|null
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * Set is default
     *
     * @api
     * @param string $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * Get is pay later
     *
     * @api
     * @return string|null
     */
    public function getIsPayLater()
    {
        return $this->getData(self::IS_PAY_LATER);
    }

    /**
     * Set is pay later
     *
     * @api
     * @param string $isPayLater
     * @return $this
     */
    public function setIsPayLater($isPayLater)
    {
        return $this->setData(self::IS_PAY_LATER, $isPayLater);
    }

    /**
     * Get is reference number
     *
     * @api
     * @return string|null
     */
    public function getIsReferenceNumber()
    {
        return $this->getData(self::IS_REFERENCE_NUMBER);
    }

    /**
     * Set is reference number
     *
     * @api
     * @param string $isReferenceNumber
     * @return $this
     */
    public function setIsReferenceNumber($isReferenceNumber)
    {
        return $this->setData(self::IS_REFERENCE_NUMBER, $isReferenceNumber);
    }

    /**
     * Get multiable
     *
     * @api
     * @return string|null
     */
    public function getMultiable()
    {
        return $this->getData(self::MULTIABLE);
    }

    /**
     * Set multiable
     *
     * @api
     * @param string $multiable
     * @return $this
     */
    public function setMultiable($multiable)
    {
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

    /**
     * Get use cvv
     *
     * @api
     * @return string|null
     */
    public function getUsecvv()
    {
        return $this->getData(self::USECVV);
    }

    /**
     * Set use cvv
     *
     * @api
     * @param string $usecvv
     * @return $this
     */
    public function setUsecvv($usecvv)
    {
        return $this->setData(self::USECVV, $usecvv);
    }


}
