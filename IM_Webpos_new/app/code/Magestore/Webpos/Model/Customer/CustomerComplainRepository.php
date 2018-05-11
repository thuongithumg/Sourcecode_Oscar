<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Customer;
use Magento\Framework\Api\SortOrder;

/**
 * Class CustomerComplainRepository
 * @package Magestore\Webpos\Model\Customer
 */
class CustomerComplainRepository implements \Magestore\Webpos\Api\Customer\CustomerComplainRepositoryInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain\CollectionFactory
     */
    protected $_customerComplainCollectionFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\Customer\CustomerComplainSearchResultsInterfaceFactory
     */
    protected $_customerComplainSearchResultsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * CollectionRepository constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Api\Data\Customer\CustomerComplainSearchResultsInterfaceFactory $customerComplainSearchResultsFactory,
        \Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain\CollectionFactory $customerComplainCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ){
        $this->_customerComplainSearchResultsFactory = $customerComplainSearchResultsFactory;
        $this->_customerComplainCollectionFactory = $customerComplainCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->timezone = $timezone;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerComplainInterface $complain
     * @return string
     */
    public function save($complain) {
        $content = $complain->getContent();
        $customerEmail = $complain->getCustomerEmail();
        $createdAt = $complain->getCreatedAt();
        if (!$createdAt) {
            $createdAt =  strftime('%Y-%m-%d %H:%M:%S', $this->timezone->scopeTimeStamp());
        } 
        $complainModel = $this->_objectManager->create('Magestore\Webpos\Model\Customer\CustomerComplain');
        $complainModel->setContent($content);
        $complainModel->setCustomerEmail($customerEmail);
        $complainModel->setCreatedAt($createdAt);
        $complainModel ->save();
        if ($complainModel->getId()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerComplainSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
        $searchResults = $this->_customerComplainSearchResultsFactory->create();
        $collection = $this->_customerComplainCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $searchResults->setTotalCount($collection->getSize());

        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $customerComplain = [];

        foreach ($collection->getItems() as $complain) {
            $customerComplain[] = $complain->getData();
        }
        $searchResults->setItems($customerComplain);
        $searchResults->setSearchCriteria($searchCriteria);
        return $searchResults;
    }

    /**
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain\Collection $collection
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain\Collection $collection
    ) {
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }
}
