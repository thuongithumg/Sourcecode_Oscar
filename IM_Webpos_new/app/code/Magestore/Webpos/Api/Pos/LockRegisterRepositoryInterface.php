<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Pos;

/**
 * Interface LockRegisterRepositoryInterface
 * @package Magestore\Webpos\Api\Pos
 */
interface LockRegisterRepositoryInterface
{
    /**
     * Lock Pos
     *
     * @param string|int|null $posId
     * @param string|int|null pin
     * @return boolean
     * @throws \Exception
     */
    public function lockPos($posId = null, $pin = null);
}