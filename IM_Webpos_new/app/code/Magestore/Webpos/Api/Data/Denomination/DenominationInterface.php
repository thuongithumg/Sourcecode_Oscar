<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Denomination;

/**
 * Interface DenominationInterface
 * @package Magestore\Webpos\Api\Data\Denomination
 */
interface DenominationInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const DENOMINATION_ID = "denomination_id";
    const DENOMINATION_NAME = "denomination_name";
    const DENOMINATION_VALUE = "denomination_value";
    const SORT_ORDER = "sort_order";

    /**
     *  Get Denomination Id
     * @return string|null
     */
    public function getDenominationId();

    /**
     * Set Denomination Id
     *
     * @param string $denominationId
     * @return $this
     */
    public function setDenominationId($denominationId);

    /**
     *  Get Denomination Id
     * @return string|null
     */
    public function getDenominationName();

    /**
     * Set Denomination Name
     *
     * @param string $denominationName
     * @return $this
     */
    public function setDenominationName($denominationName);

    /**
     *  Get Denomination Id
     * @return float
     */
    public function getDenominationValue();

    /**
     * Set Denomination Name
     *
     * @param string $denominationName
     * @return $this
     */
    public function setDenominationValue($denominationValue);

    /**
     *  Get $sortOrder
     * @return float
     */
    public function getSortOrder();

    /**
     * Set $sortOrder
     *
     * @param string $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);
}