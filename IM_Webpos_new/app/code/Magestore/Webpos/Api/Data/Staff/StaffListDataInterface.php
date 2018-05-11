<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Staff;

interface StaffListDataInterface
{

    const DISPLAY_NAME = 'display_name';
    const STAFF_ID = 'staff_id';
    const ROLE = 'role';
    const PERMISSION = 'permission';
    const PIN = 'pin';

    /**
     * Get staff id
     *
     * @api
     * @return string|null
     */
    public function getStaffId();

    /**
     * Set staff id
     *
     * @api
     * @param string $staffId
     * @return $this
     */
    public function setStaffId($staffId);

    /**
     * Get display name
     *
     * @api
     * @return string|null
     */
    public function getDisplayName();

    /**
     * Set display name
     *
     * @api
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName);

    /**
     * Get permission
     *
     * @api
     * @return string[]
     */
    public function getPermission();

    /**
     * Set permission
     *
     * @api
     * @param string[] $permission\
     * @return $this
     */
    public function setPermission($permission);

    /**
     * Get role
     *
     * @api
     * @return string[]
     */
    public function getRole();

    /**
     * Set role
     *
     * @api
     * @param string[] $role
     * @return $this
     */
    public function setRole($role);

    /**
     * Get pin
     *
     * @api
     * @return string
     */
    public function getPin();

    /**
     * Set pin
     *
     * @api
     * @param string $pin
     * @return $this
     */
    public function setPin($pin);

}
