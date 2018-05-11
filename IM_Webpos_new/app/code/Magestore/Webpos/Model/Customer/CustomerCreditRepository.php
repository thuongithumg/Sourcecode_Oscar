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
class CustomerCreditRepository implements \Magestore\Webpos\Api\Customer\CustomerCreditRepositoryInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain\CollectionFactory
     */
    protected $_customerCreditCollectionFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\Customer\CustomerComplainSearchResultsInterfaceFactory
     */
    protected $_customerCreditSearchResultsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helperData;

    /**
     * CollectionRepository constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Api\Data\Customer\CustomerCreditSearchResultsInterfaceFactory $customerCreditSearchResultsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magestore\Webpos\Helper\Data $helperData
    ){
        $this->_customerCreditSearchResultsFactory = $customerCreditSearchResultsFactory;
        $this->_objectManager = $objectManager;
        $this->timezone = $timezone;
        $this->_objectManager = $objectmanager;
        $this->_helperData = $helperData;
    }
    /**
     * @inheritdoc
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerCreditSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
        if ($this->_helperData->checkMagentoEE()) {
            $searchResults = $this->_customerCreditSearchResultsFactory->create();
            $collection = $this->_objectManager->create('\Magento\CustomerBalance\Model\Balance\History')->getCollection();
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
            $customerCredit = [];

            foreach ($collection->getItems() as $history) {
                $customerCredit[] = $history->getData();
            }
            $searchResults->setItems($customerCredit);
            $searchResults->setSearchCriteria($searchCriteria);
            return $searchResults;
        }
    }

    public function getBalance(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {

    }

    /**
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain\Collection $collection
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup, $collection
    ) {
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $fields[] = ['attribute' => $filter->getField(), 'value' => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields[0]['attribute'], $fields[0]['value']);
        }
        return $collection;
    }
}
