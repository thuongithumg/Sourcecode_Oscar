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
 * Interface Magestore_Webpos_Api_PaymentInterface
 */
interface Magestore_Webpos_Api_PaymentInterface
{
    const CODE = 'code';
    const ICON_CLASS = 'icon_class';
    const TITLE = 'title';
    const INFORMATION = 'information';
    const TYPE = 'type';
    const IS_DEFAULT = 'is_default';
    const IS_REFERENCE_NUMBER = 'is_reference_number';
    const IS_PAY_LATER = 'is_pay_later';
    const MULTIABLE = 'multiable';
    const TEMPLATE = 'template';
    const FORM_DATA = 'form_data';

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
    public function getIconClass();

    /**
     * @param string $class
     * @return string
     */
    public function setIconClass($class);

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
    public function getInfomation();

    /**
     * @param string $information
     * @return mixed
     */
    public function setInfomation($information);
    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return mixed
     */
    public function setType($type);
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
    public function getIsReferenceNumber();

    /**
     * @param string $isReferenceNumber
     * @return mixed
     */
    public function setIsReferenceNumber($isReferenceNumber);
    /**
     * @return string
     */
    public function getIsPayLater();

    /**
     * @param string $isPayLater
     * @return mixed
     */
    public function setIsPayLater($isPayLater);

    /**
     * @return string
     */
    public function getMultiable();

    /**
     * @param string $multiable
     * @return mixed
     */
    public function setMultiable($multiable);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $template
     * @return mixed
     */
    public function setTemplate($template);

    /**
     * @return string
     */
    public function getFormData();

    /**
     * @param string $formData
     * @return mixed
     */
    public function setFormData($formData);
}
