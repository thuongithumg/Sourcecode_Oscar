<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Denomination;

use Magento\Framework\Exception\StateException;
use Magestore\Webpos\Api\Data\Denomination\DenominationSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\Framework\Api\SortOrder;

/**
 * Class DenominationRepository
 * @package Magestore\Webpos\Model\Denomination
 */
class DenominationRepository implements \Magestore\Webpos\Api\Denomination\DenominationRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Model\Denomination\DenominationFactory
     */
    protected $denominationFactory;

    /**
     * @var  \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination
     */
    protected $denominationResourceModel;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var  SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * DenominationRepository constructor.
     * @param DenominationFactory $denominationFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory $collectionFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination $denominationResourceModel
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\Denomination\DenominationFactory $denominationFactory,
        \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory $collectionFactory,
        \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination $denominationResourceModel,
        SearchResultFactory $searchResultFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->denominationFactory = $denominationFactory;
        $this->denominationResourceModel = $denominationResourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * get list Pos
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Data\Denomination\DenominationSearchResultsInterface $searchResult */
        $searchResult =  $this->searchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        if($searchCriteria ->getCurrentPage()) {
            $searchResult->setCurPage($searchCriteria->getCurrentPage());
        }
        if($searchCriteria ->getPageSize()) {
            $searchResult->setPageSize($searchCriteria->getPageSize());
        }
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }

    /**
     * get denomination
     *
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationInterface
     */
    public function get($denominationId)
    {
        $denomination = $this->denominationFactory->create()->load($denominationId);
        if($denomination->getId()) {
            return $denomination;
        }
        return null;
    }

    /**
     * save denomination
     *
     * @param \Magestore\Webpos\Api\Data\Denomination\DenominationInterface $denomination
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationInterface
     * @throws \Exception
     */
    public function save(\Magestore\Webpos\Api\Data\Denomination\DenominationInterface $denomination)
    {
        try {
            $this->denominationResourceModel->save($denomination);
        }catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save denomination'));
        }
        return $denomination;
    }
}