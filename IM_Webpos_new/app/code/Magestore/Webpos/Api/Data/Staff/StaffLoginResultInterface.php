<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Staff;

interface StaffLoginResultInterface
{

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
     * Get session id
     *
     * @api
     * @return string|null
     */
    public function getSessionId();
    /**
     * Set session id
     *
     * @api
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId($sessionId);
    /**
     * Get locations
     *
     * @api
     * @return array|null
     */
    public function getLocations();
    /**
     * Set locations
     *
     * @api
     * @param array $locations
     * @return $this
     */
    public function setLocations($locations);

}
