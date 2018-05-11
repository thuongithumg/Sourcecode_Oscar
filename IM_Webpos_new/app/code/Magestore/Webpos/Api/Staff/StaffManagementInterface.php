<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Staff;

use Magento\Framework\Exception\StateException;

interface StaffManagementInterface
{

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function login($staff);
    /**
     * 
     * @return string
     */
    public function logout();

    /**
     * @return boolean
     */
    public function forceLogout();

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function changepassword($staff);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Staff\StaffSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $staffId
     * @param string $pin
     * @return \Magestore\Webpos\Api\Data\Staff\StaffListDataInterface $staff
     * @throws StateException
     */
    public function changeStaff($staffId, $pin);

}
