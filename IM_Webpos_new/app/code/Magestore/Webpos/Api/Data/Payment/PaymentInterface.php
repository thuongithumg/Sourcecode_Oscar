<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Payment;

interface PaymentInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Array of collection items.
     */

    /**#@+
     * Constants for keys of data array
     */
    const CODE = 'code';
    const TITLE = 'title';
    const INFORMATION = 'information';
    const TYPE = 'type';
    const TYPE_ID = 'type_id';
    const ICON_CLASS = 'icon_class';
    const IS_DEFAULT = 'is_default';
    const IS_PAY_LATER = 'is_pay_later';
    const IS_REFERENCE_NUMBER = 'is_reference_number';
    const MULTIABLE = 'multiable';
    const USECVV = 'usecvv';
    const TEMPLATE = 'template';
    const FORM_DATA = 'form_data';

    const YES = '1';
    const NO = '0';
    /**#@-*/

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get title
     *
     * @api
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get type id
     *
     * @api
     * @return string
     */
    public function getTypeId();

    /**
     * Set type id
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setTypeId($typeId);

    /**
     * Get type
     *
     * @api
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get information
     *
     * @api
     * @return string|null
     */
    public function getInformation();

    /**
     * Set information
     *
     * @api
     * @param string $information
     * @return $this
     */
    public function setInformation($information);

    /**
     * Get icon class
     *
     * @api
     * @return string|null
     */
    public function getIconClass();

    /**
     * Set icon class
     *
     * @api
     * @param string $iconClass
     * @return $this
     */
    public function setIconClass($iconClass);

    /**
     * Get is default
     *
     * @api
     * @return string|null
     */
    public function getIsDefault();

    /**
     * Set is default
     *
     * @api
     * @param string $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault);

    /**
     * Get is pay later
     *
     * @api
     * @return string|null
     */
    public function getIsPayLater();

    /**
     * Set is pay later
     *
     * @api
     * @param string $isPayLater
     * @return $this
     */
    public function setIsPayLater($isPayLater);

    /**
     * Get is reference number
     *
     * @api
     * @return string|null
     */
    public function getIsReferenceNumber();

    /**
     * Set is reference number
     *
     * @api
     * @param string $isReferenceNumber
     * @return $this
     */
    public function setIsReferenceNumber($isReferenceNumber);

    /**
     * Get multiable
     *
     * @api
     * @return string|null
     */
    public function getMultiable();

    /**
     * Set multiable
     *
     * @api
     * @param string $multiable
     * @return $this
     */
    public function setMultiable($multiable);

    /**
     * Get use cvv
     *
     * @api
     * @return string|null
     */
    public function getUsecvv();

    /**
     * Set use cvv
     *
     * @api
     * @param string $usecvv
     * @return $this
     */
    public function setUsecvv($usecvv);


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
