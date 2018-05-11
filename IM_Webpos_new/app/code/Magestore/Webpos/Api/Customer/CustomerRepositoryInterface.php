<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Customer;

/**
 * Customer CRUD interface.
 */
interface CustomerRepositoryInterface
{
    /**
     * Retrieve customers which match a specified criteria.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get customer by customer ID.
     *
     * @param int $customerId
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId);

    /**
     * Create customer.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $customer
     * @param string $passwordHash
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Customer\CustomerInterface $customer, $passwordHash = null);
}
