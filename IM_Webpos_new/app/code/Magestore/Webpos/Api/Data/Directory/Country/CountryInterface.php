<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Directory\Country;

interface CountryInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const COUNTRY_ID = 'country_id';
    const COUNTRY_NAME = 'country_name';
    /**#@-*/

    /**
     * Get country id
     *
     * @api
     * @return string
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
     * Get country name
     *
     * @api
     * @return string|null
     */
    public function getCountryName();

    /**
     * Set country name
     *
     * @api
     * @param string $countryName
     * @return $this
     */
    public function setCountryName($countryName);

}
