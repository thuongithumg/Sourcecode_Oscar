<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Pos;


/**
 * Interface PosRepositoryInterface
 * @package Magestore\Webpos\Api\Pos
 */
interface PosRepositoryInterface
{
    /**
     * get list Pos
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * assign staff for pos
     *
     * @param string $posId
     * @param string $locationId
     * @param string $currentSessionId
     * @return boolean
     */
    public function assignStaff($posId, $locationId, $currentSessionId);

    /**
     * Change pin
     *
     * @param \Magestore\Webpos\Api\Data\Register\PinInterface $pin
     * @return boolean
     */
    public function changePin(\Magestore\Webpos\Api\Data\Register\PinInterface $pin);

    /**
     * Unlock pos
     * @param string $pin
     * @param string $posId
     * @return boolean
     */
    public function unlockPos($pin, $posId);

    /**
     * Check pos
     * @param string $posId
     * @return boolean
     */
    public function checkPos($posId);

}