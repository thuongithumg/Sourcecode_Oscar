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
 * Interface Magestore_Webpos_Api_Checkout_AddressInterface
 */
interface Magestore_Webpos_Api_Checkout_AddressInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_ID = 'id';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_REGION = 'region';
    const KEY_REGION_ID = 'region_id';
    const KEY_COUNTRY_ID = 'country_id';
    const KEY_STREET = 'street';
    const KEY_COMPANY = 'company';
    const KEY_TELEPHONE = 'telephone';
    const KEY_FAX = 'fax';
    const KEY_POSTCODE = 'postcode';
    const KEY_CITY = 'city';
    const KEY_FIRSTNAME = 'firstname';
    const KEY_LASTNAME = 'lastname';
    const KEY_MIDDLENAME = 'middlename';
    const KEY_PREFIX = 'prefix';
    const KEY_SUFFIX = 'suffix';
    const KEY_VAT_ID = 'vat_id';
    const KEY_TYPE = 'address_type';
    const KEY_EMAIL = 'email';
    const DEFAULT_BILLING = 'default_billing';
    const DEFAULT_SHIPPING = 'default_shipping';
    /**#@-*/
    
    /**
     * Get ID
     *
     * @api
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @api
     * @param int $id
     * @return $this
     */
    public function setId($id);



    /**
     * Get region
     *
     * @api
     * @return \Magento\Customer\Api\Data\RegionInterface
     */
    public function getRegion();

    /**
     * Set region
     *
     * @api
     * @param \Magento\Customer\Api\Data\RegionInterface $region
     * @return $this
     */
    public function setRegion(\Magento\Customer\Api\Data\RegionInterface $region = null);

    /**
     * Get region ID
     *
     * @api
     * @return int|null
     */
    public function getRegionId();

    /**
     * Set region ID
     *
     * @api
     * @param int $regionId
     * @return $this
     */
    public function setRegionId($regionId);

    /**
     * Get customer ID
     *
     * @api
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @api
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get street
     *
     * @api
     * @return string[]|null
     */
    public function getStreet();

    /**
     * Set street
     *
     * @api
     * @param string[] $street
     * @return $this
     */
    public function setStreet(array $street);

    /**
     * Get telephone number
     *
     * @api
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone number
     *
     * @api
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Get postcode
     *
     * @api
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     *
     * @api
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);


    /**
     * Two-letter country code in ISO_3166-2 format
     *
     * @api
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country id
     *
     * @api
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId);

    
    /**
     * Get company
     *
     * @api
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     *
     * @api
     * @param string $company
     * @return $this
     */
    public function setCompany($company);



    /**
     * Get fax number
     *
     * @api
     * @return string|null
     */
    public function getFax();

    /**
     * Set fax number
     *
     * @api
     * @param string $fax
     * @return $this
     */
    public function setFax($fax);


    /**
     * Get city name
     *
     * @api
     * @return string|null
     */
    public function getCity();

    /**
     * Set city name
     *
     * @api
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * Get first name
     *
     * @api
     * @return string|null
     */
    public function getFirstname();

    /**
     * Set first name
     *
     * @api
     * @param string $firstName
     * @return $this
     */
    public function setFirstname($firstName);

    /**
     * Get last name
     *
     * @api
     * @return string|null
     */
    public function getLastname();

    /**
     * Set last name
     *
     * @api
     * @param string $lastName
     * @return $this
     */
    public function setLastname($lastName);

    /**
     * Get middle name
     *
     * @api
     * @return string|null
     */
    public function getMiddlename();

    /**
     * Set middle name
     *
     * @api
     * @param string $middleName
     * @return $this
     */
    public function setMiddlename($middleName);

    /**
     * Get prefix
     *
     * @api
     * @return string|null
     */
    public function getPrefix();

    /**
     * Set prefix
     *
     * @api
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * Get suffix
     *
     * @api
     * @return string|null
     */
    public function getSuffix();

    /**
     * Set suffix
     *
     * @api
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix);
    /**
     * Get if this address is default shipping address.
     *
     * @api
     * @return bool|null
     */
    public function isDefaultShipping();

    /**
     * Set if this address is default shipping address.
     *
     * @api
     * @param bool $isDefaultShipping
     * @return $this
     */
    public function setIsDefaultShipping($isDefaultShipping);

    /**
     * Get if this address is default billing address
     *
     * @api
     * @return bool|null
     */
    public function isDefaultBilling();

    /**
     * Set if this address is default billing address
     *
     * @api
     * @param bool $isDefaultBilling
     * @return $this
     */
    public function setIsDefaultBilling($isDefaultBilling);
    /**
     * Get Vat id
     *
     * @api
     * @return string|null
     */
    public function getVatId();

    /**
     * Set Vat id
     *
     * @api
     * @param string $vatId
     * @return $this
     */
    public function setVatId($vatId);
    
    /**
     * Returns the address type.
     *
     * @return string
     */
    public function getAddressType();

    /**
     * Sets the address type.
     *
     * @param string $type
     * @return $this
     */
    public function setAddressType($type);
    
    /**
     * Returns the email.
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Sets the email.
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);
    
    /**
     * set address data.
     *
     * @param array $data
     * @return $this
     */
    public function setAddressData($data);
        

}
