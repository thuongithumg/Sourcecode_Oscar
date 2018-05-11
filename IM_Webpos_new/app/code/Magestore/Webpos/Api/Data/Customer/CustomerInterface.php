<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Customer;

/**
 * Customer interface.
 */
interface CustomerInterface extends \Magento\Customer\Api\Data\CustomerInterface
{


    /**
     * Get customer telephone
     *
     * @api
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set customer telephone
     *
     * @api
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Get customer amount credit
     *
     * @api
     * @return string|null
     */
    public function getAmount();

    /**
     * Set customer amount credit
     *
     * @api
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount);
    /**
     * Get subscriber
     *
     * @api
     * @return string|null
     */
    public function getSubscriberStatus();

    /**
     * Set subscriber
     *
     * @api
     * @param string $subscriberStatus
     * @return $this
     */
    public function setSubscriberStatus($subscriberStatus);

    /**
     * Get full name
     *
     * @api
     * @return string|null
     */
    public function getFullName();

    /**
     * Set full name
     *
     * @api
     * @param string $fullName
     * @return $this
     */
    public function setFullName($fullName);
    /**
     * Get index db id
     *
     * @api
     * @return string|null
     */
    public function getIndexeddbId();

    /**
     * Set index db id
     *
     * @api
     * @param string $indexDbId
     * @return $this
     */
    public function setIndexeddbId($indexDbId);
    
    
    /**
     * Get additional attributes
     *
     * @api
     * @return anyType
     */
    public function getAdditionalAttributes();

    /**
     * Set additional attributes
     *
     * @api
     * @param anyType $values
     * @return $this
     */
    public function setAdditionalAttributes($values);

    /**
     * Get customer id
     *
     * @return int
     */
    public function getId();

    /**
     * Set customer id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get customer id
     *
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);


}
