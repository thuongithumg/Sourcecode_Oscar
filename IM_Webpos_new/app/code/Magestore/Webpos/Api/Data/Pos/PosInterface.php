<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Pos;

/**
 * Interface PosInterface
 * @package Magestore\Webpos\Api\Data\Pos
 */
interface PosInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const POS_ID = "pos_id";
    const POS_NAME = "pos_name";
    const LOCATION_ID = "location_id";
    const LOCATION_NAME = "display_name";
    const STORE_ID = "store_id";
    const STAFF_ID = "staff_id";
    const DENOMINATIONS = "denominations";
    const DENOMINATION_IDS = "denomination_ids";
    const STATUS = "status";
    const STAFF_LOCKED = "staff_locked";
    const IS_ALLOW_TO_LOCK = "is_allow_to_lock";
    const STAFF_NAME = "staff_name";

    const ALL = "all";

    /**
     *  Get Pos Id
     * @return string|null
     */
    public function getPosId();

    /**
     * Set Pos Id
     *
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId);

    /**
     *  Get Pos Id
     * @return string|null
     */
    public function getPosName();

    /**
     * Set Pos Name
     *
     * @param string $posName
     * @return $this
     */
    public function setPosName($posName);

    /**
     *  location_id
     * @return int|null
     */
    public function getLocationId();

    /**
     *  location_name
     * @return string|null
     */
    public function getLocationName();

    /**
     * Set Location Id
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId);

    /**
     * Set Location Name
     *
     * @param string $locationName
     * @return $this
     */
    public function setLocationName($locationName);

    /**
     *  Staff Id
     * @return int|null
     */
    public function getStaffId();

    /**
     * Set Staff Id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId($staff_id);

    /**
     * get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * set Status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Denominations
     * @param string $denominationIds
     * @return $this
     */
    public function setDenominationIds($denominationIds);

    /**
     * Denominations
     * @return string
     */
    public function getDenominationIds();

    /**
     * Denominations
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[]  Array of denominations items
     */
    public function getDenominations();

    /**
     * Set staff locked id
     *
     * @param int $staffLocked
     * @return $this
     */
    public function setStaffLocked($staffLocked);

    /**
     * get staff locked id
     *
     * @return int
     */
    public function getStaffLocked();

    /**
     * Set is allow to lock
     *
     * @param int $isAllowToLock
     * @return $this
     */
    public function setIsAllowToLock($isAllowToLock);

    /**
     * get is allow to lock
     *
     * @return int
     */
    public function getIsAllowToLock();

    /**
     * Set staff name
     *
     * @param string $staffName
     * @return $this
     */
    public function setStaffName($staffName);

    /**
     * get staff name
     *
     * @return string
     */
    public function getStaffName();
}