<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Pos\Pos;


use Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface;

/**
 * Class Collection
 * @package Magestore\Webpos\Model\ResourceModel\Pos\Pos
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    implements PosSearchResultsInterface
{
    const POS_LOCKED = 3;
    const POS_ENABLE = 1;
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'pos_id';
    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Pos\Pos',
                'Magestore\Webpos\Model\ResourceModel\Pos\Pos');
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('pos_id','pos_name');
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'pos_id') {
            $field = 'main_table.pos_id';
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Get Available Staff
     *
     * @param string $posId
     * @return $this
     */
    public function getAvailabeStaff($posId)
    {
        $collection = \Magento\Framework\App\ObjectManager::getInstance()
                        ->get('Magestore\Webpos\Model\ResourceModel\Staff\Staff\Collection');
        $config = \Magento\Framework\App\ObjectManager::getInstance()
                        ->get('Magestore\Webpos\Helper\Data');
        if($config->getStoreConfig('webpos/general/enable_session')) {
            $posCollection = $this;
            if ($posId != 0) {
                $posCollection->addFieldToFilter('pos_id', array('nin' => array($posId)))
                    ->addFieldToFilter('staff_id', array('neq' => null));
            }
            $staffIds = array();
            if ($posCollection->getSize() > 0) {
                foreach ($posCollection as $pos) {
                    $staffIds[] = $pos->getStaffId();
                }
            }
            if (count($staffIds) > 0) {
                $collection->addFieldToFilter('staff_id', array('nin' => array($staffIds)));
            }
        }
        return $collection;
    }

    /**
     * Get Available Pos
     *
     * @param string $staffId
     * @return $this
     */
    public function getAvailablePos($staffId = null)
    {
        $config = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magestore\Webpos\Helper\Data');
        if($config->getStoreConfig('webpos/general/enable_session')) {
            if ($staffId) {
                $staff = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('Magestore\Webpos\Model\Staff\StaffFactory')->create()->load($staffId);
                $staffIds = array($staffId, NULL, 0);
                $posIds = $staff->getPosIds();
                $locationIds = $staff->getLocationId();
                if ($posIds) {
                    $posIds = explode(',', $posIds);
                    $this->addFieldToFilter('pos_id', array('in' => array($posIds)));
                    if($locationIds) {
                        $locationIds = explode(',', $locationIds);
                        $this->addFieldToFilter('main_table.location_id', array('in' => array($locationIds)));
                    }
                }
                $this->addFieldToFilter('staff_id', array(
                        array('in' => array($staffIds)),
                        array('null' => true)
                    )
                );
            }
        }
        $this->addFieldToFilter('status', array('in' => array(self::POS_LOCKED, self::POS_ENABLE)));
        return $this;
    }

    /**
     * Get Location Collection
     *
     * @param string $staffId
     * @return $this
     */
    public function getLocationCollection($staffId = null)
    {
        $collection = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magestore\Webpos\Model\ResourceModel\Location\Location\Collection'
        );
        return $collection;
    }

    /**
     * Get Available Pos
     *
     * @param string $staffId
     * @return $this
     */
    public function getAvailableLocation($staffId = null)
    {
        if ($staffId) {
            $staff = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magestore\Webpos\Model\Staff\StaffFactory')->create()->load($staffId);
            $locationIds = $staff->getLocationId();
            if ($locationIds) {
                $locationIds = explode(',', $locationIds);
                $this->addFieldToFilter('location.location_id', array('in' => array($locationIds)));
            }
        }
        $storeInterface = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Store\Model\StoreManagerInterface'
        );
        $website = $storeInterface->getStore()->getWebsite();
        $storeIds = $website->getStoreIds();
        if(count($storeIds)) {
            $this->addFieldToFilter('location.store_id', array('in' => $storeIds));
        }
        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        if (!$items) {
            return $this;
        }
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * Join to location table
     *
     * @return $this
     */
    public function joinToLocation()
    {
        $this->getSelect()->joinLeft(array('location' => $this->getTable('webpos_staff_location')),
                            'main_table.location_id = location.location_id' ,
                            array(
                                  'display_name' => 'display_name',
                                  'location_store_id' => 'location.store_id'
                                  )
                    );
        $storeInterface = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Store\Model\StoreManagerInterface'
        );
        $website = $storeInterface->getStore()->getWebsite();
        $storeIds = $website->getStoreIds();
        if(count($storeIds)) {
            $this->addFieldToFilter('location.store_id', array('in' => $storeIds));
        }
        return $this;
    }

    /**
     * Join right to location table
     *
     * @return $this
     */
    public function joinRightToLocation()
    {
        $this->getSelect()->joinRight(array('location' => $this->getTable('webpos_staff_location')),
            'main_table.location_id = location.location_id' ,
            array(
                'display_name' => 'display_name',
                'location_store_id' => 'location.store_id'
            )
        );
        $this->getSelect()->group('main_table.location_id');
        $storeInterface = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Store\Model\StoreManagerInterface'
        );
        $website = $storeInterface->getStore()->getWebsite();
        $storeIds = $website->getStoreIds();
        if(count($storeIds)) {
            $this->addFieldToFilter('location.store_id', array('in' => $storeIds));
        }
//        var_dump($this->getSelect()->__toString());die;
        return $this;
    }

}