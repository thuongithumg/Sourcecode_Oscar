<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Register;

/**
 * Interface PosInterface
 * @package Magestore\Webpos\Api\Data\Pos
 */
interface PinInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const STAFF_ID = "staff_id";

    const PASSWORD = "password";

    const PIN_CODE = "pin_code";

    const POS_ID = "pos_id";

    /**
     *  Get Staff Id
     * @return string|null
     */
    public function getStaffId();

    /**
     * Set Staff Id
     *
     * @param string $posId
     * @return $this
     */
    public function setStaffId($staffId);

    /**
     *  Get Pin
     * @return string|null
     */
    public function getPinCode();

    /**
     * Set Pin
     *
     * @param string $pinCode
     * @return $this
     */
    public function setPinCode($pinCode);
    /**
     *  Get Password
     * @return string|null
     */
    public function getPassword();

    /**
     * Set Password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password);
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

}