<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Customer\Data;

/**
 * Class Customer
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Customer extends \Magento\Customer\Model\Data\Customer implements  \Magestore\Webpos\Api\Data\Customer\CustomerInterface
{
    /**
     * Get customer billing telephone
     *
     * @api
     * @return string|null
     */
    public function getTelephone() {
        return $this->_get('telephone');
    }

    /**
     * Set customer billing telephone
     *
     * @api
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone) {
        $this->setData('telephone', $telephone);
    }
    /**
     * Get customer store credit
     *
     * @api
     * @return string|null
     */
    public function getAmount() {
        return $this->_get('amount');
    }

    /**
     * Set customer store credit
     *
     * @api
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount) {
        $this->setData('amount', $amount);
    }
    /**
     * Get subscriber
     *
     * @api
     * @return string|null
     */
    public function getSubscriberStatus() {
        return $this->_get('subscriber_status');
    }

    /**
     * Set subscriber
     *
     * @api
     * @param string $subscriberStatus
     * @return $this
     */
    public function setSubscriberStatus($subscriberStatus) {
        $this->setData('subscriber_status', $subscriberStatus);
    }
    /**
     * Get full name
     *
     * @api
     * @return string|null
     */
    public function getFullName() {
        return $this->_get('full_name');
    }

    /**
     * Set full name
     *
     * @api
     * @param string $fullName
     * @return $this
     */
    public function setFullName($fullName) {
        $this->setData('full_name', $fullName);
    }

    /**
     * 
     * @return array
     */
    public function getAdditionalAttributes() {
        return $this->_get('additional_attributes');
    }

    /**
     * 
     * @param array $values
     * @return type
     */
    public function setAdditionalAttributes($values) {
        return $this->setData('additional_attributes', $values);
    }

    /**
     * Get index db id
     *
     * @api
     * @return string|null
     */
    public function getIndexeddbId() {
        return $this->_get('indexeddb_id');
    }

    /**
     * Set index db id
     *
     * @api
     * @param string $indexDbId
     * @return $this
     */
    public function setIndexeddbId($indexDbId){
        return $this->setData('indexeddb_id', $indexDbId);
    }

    /**
     * Get customer id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }


    /**
     * Set customer id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }


    /**
     * Get customer id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get('customer_id');
    }


    /**
     * Set customer id
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

}
