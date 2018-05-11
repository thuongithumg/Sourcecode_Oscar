<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Pos;

use Magestore\Webpos\Api\Data\Denomination\DenominationInterface;

/**
 * Class Pos
 * @package Magestore\Webpos\Model\Pos
 */
class Pos extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Pos\PosInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_pos';

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory
     */
    protected $denominationCollectionFactory;

    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $staffFactory;

    /**
     * Pos constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory $denominationCollectionFactory
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory $denominationCollectionFactory,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->denominationCollectionFactory = $denominationCollectionFactory;
        $this->staffFactory = $staffFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Pos\Pos');
    }

    /**
     * get option array
     *
     * return array
     */
    public function toOptionArray()
    {
        $collection = $this->getCollection()->getAvailablePos();
        $options = array();
        if ($collection->getSize() > 0) {
            foreach ($collection as $pos) {
                $options[] = array('value' => $pos->getId(), 'label' => $pos->getData('pos_name'));
            }
        }
        return $options;
    }

    /**
     * get option array
     *
     * return array
     */
    public function getAvailableStaff($posId = 0)
    {
        $collection = $this->getCollection()->getAvailabeStaff($posId);
        $options = array();
        if ($collection->getSize() > 0) {
            foreach ($collection as $staff) {
                $options[] = array('value' => $staff->getId(), 'label' => $staff->getData('display_name'));
            }
        }
        return $options;
    }

    /**
     * get pos list for form select element
     * return array
     */
    public function getValuesForForm($posId = 0)
    {
        $options = array();
        $options[] = array('value' => null, 'label' => ' ');
        $optionsArr = $this->getAvailableStaff($posId);
        $options = array_merge($options, $optionsArr);
        return $options;
    }

    /**
     *  Get Pos Id
     * @return string|null
     */
    public function getPosId()
    {
        return $this->getData(self::POS_ID);
    }

    /**
     * Set Pos Id
     *
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId)
    {
        return $this->setData(self::POS_NAME, $posId);
    }

    /**
     *  Get Pos Name
     * @return string|null
     */
    public function getPosName()
    {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magestore\Webpos\Helper\Data'
        );
        if (!$helper->getStoreConfig('webpos/general/enable_session')) {
            return $this->getData(self::LOCATION_NAME);
        }
        return $this->getData(self::POS_NAME);
    }

    /**
     * Set Pos Name
     *
     * @param string $posName
     * @return $this
     */
    public function setPosName($posName)
    {
        return $this->setData(self::POS_NAME, $posName);
    }

    /**
     *  location_id
     * @return int|null
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     *  location_name
     * @return int|null
     */
    public function getLocationName()
    {
        return $this->getData(self::LOCATION_NAME);
    }

    /**
     * Set Location Id
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * Set Location Name
     *
     * @param int $locationName
     * @return $this
     */
    public function setLocationName($locationName)
    {
        return $this->setData(self::LOCATION_NAME, $locationName);
    }

    /**
     *  get store id
     * @return int|null
     */
    public function getStoreId()
    {
        if ($this->getData('location_store_id')) {
            return $this->getData('location_store_id');
        }
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     *  Staff Id
     * @return int|null
     */
    public function getStaffId()
    {
        return $this->getData(self::STAFF_ID);
    }

    /**
     * Set Staff Id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId($staff_id)
    {
        return $this->setData(self::STAFF_ID, $staff_id);
    }

    /**
     * status
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * set Status
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Denominations
     * @param string $denominationIds
     * @return $this
     */
    public function setDenominationIds($denominationIds)
    {
        return $this->setData(self::DENOMINATION_IDS, $denominationIds);
    }

    /**
     * Denominations
     * @return string
     */
    public function getDenominationIds()
    {
        return $this->getData(self::DENOMINATION_IDS);
    }


    /**
     * Denominations
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[]
     */
    public function getDenominations()
    {
        $denominationIds = $this->getDenominationIds();
        $denominations = $this->denominationCollectionFactory->create();
        $denominations->setOrder(DenominationInterface::SORT_ORDER, 'ASC');
        if ($denominationIds) {
            $denominationIds = explode(',', $denominationIds);
            if (!empty($denominationIds) && !in_array(self::ALL, $denominationIds)) {
                $denominations->addFieldToFilter(DenominationInterface::DENOMINATION_ID, ['in' => $denominationIds]);
            }
        }
        return $denominations->getData();
    }

    /**
     * Set staff locked id
     *
     * @param int $staffLocked
     * @return $this
     */
    public function setStaffLocked($staffLocked) {
        return $this->setData(self::STAFF_LOCKED, $staffLocked);
    }

    /**
     * get staff locked id
     *
     * @return int
     */
    public function getStaffLocked() {
        return $this->getData(self::STAFF_LOCKED);
    }

    /**
     * Set is allow to lock
     *
     * @param int $isAllowToLock
     * @return $this
     */
    public function setIsAllowToLock($isAllowToLock) {
        return $this->setData(self::IS_ALLOW_TO_LOCK, $isAllowToLock);
    }

    /**
     * get is allow to lock
     *
     * @return int
     */
    public function getIsAllowToLock() {
        return $this->getData(self::IS_ALLOW_TO_LOCK);
    }

    /**
     * Set staff name
     *
     * @param string $staffName
     * @return $this
     */
    public function setStaffName($staffName) {
        return $this->setData(self::STAFF_NAME, $staffName);
    }

    /**
     * get staff name
     *
     * @return string
     */
    public function getStaffName() {
        return $this->staffFactory->create()->load($this->getStaffId())->getUsername();
    }

    /**
     * @param string $key
     * @param null $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        $data = parent::getData($key, $index);
        if ('' === $key) {
            $data[self::DENOMINATIONS] = $this->getDenominations();
        }
        return $data;
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    public function beforeSave()
    {
        if ($this->getData('is_allow_to_lock') == \Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab\Form::STATUS_DISABLED
            && $this->getStatus() == \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED
        ) {
            $this->setStatus(\Magestore\Webpos\Model\Pos\Status::STATUS_ENABLED);
        }
        return parent::beforeSave();
    }
}