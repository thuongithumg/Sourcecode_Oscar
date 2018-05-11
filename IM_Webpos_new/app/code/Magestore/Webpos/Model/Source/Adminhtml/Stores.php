<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Stores
 * 
 * Stores source model
 * Methods:
 *  getGroupCollection
 *  getOptionArray
 *  getOptionHash
 *  getStoreCollection
 *  getStoreIds
 *  getWebsiteCollecion
 *  getWebsiteIds
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Stores implements \Magento\Framework\Option\ArrayInterface {

    protected $_storeManager;
    protected $_websiteFactory;
    protected $_storeGroupFactory;
    protected $_storeFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Store\Model\GroupFactory $storeGroupFactory,
        \Magento\Store\Model\StoreFactory $storeFactory
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
        $this->_storeManager = $context->getStoreManager();
    }


    public function toOptionArray() {

        $array = array('all' => __('All Stores'));
        foreach ($this->getWebsiteCollection() as $_website):
            foreach ($this->getGroupCollection($_website) as $_group):
                foreach ($this->getStoreCollection($_group) as $_store):
                    $array[$_store->getId()] = $_group->getName().' - '.$_store->getName();
                endforeach;
            endforeach;
        endforeach;

        $options = array();
        foreach ($array as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $options;
    }

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        $array = array('all' => __('All Stores'));
        foreach (self::getWebsiteCollection() as $_website):
            foreach (self::getGroupCollection($_website) as $_group):
                foreach (self::getStoreCollection($_group) as $_store):
                    $array[$_store->getId()] = $_group->getName().' - '.$_store->getName();
                endforeach;
            endforeach;
        endforeach;

        $options = array();
        foreach ($array as $value => $label) {
            $options[$value] = $label;
        }
        return $options;
    }

    static public function getOptionHash() {
        return self::getOptionArray();
    }

    public function getWebsiteCollection()
    {

        $collection = $this->_websiteFactory->create()->getResourceCollection();

        $websiteIds = $this->getWebsiteIds();
        if ($websiteIds !== null) {
            $collection->addIdFilter($this->getWebsiteIds());
        }

        return $collection->load();
    }

    /**
     * @return array
     */
    public function getWebsiteIds()
    {
        $websiteIds = [];
        $collection = $this->_websiteFactory->create()->getCollection();
        foreach ($collection as $website) {
            $websiteIds[] = $website->getId();
        }
        return $websiteIds;
    }


    public function getGroupCollection($website)
    {
        if (!$website instanceof \Magento\Store\Model\Website) {
            $website = $this->_websiteFactory->create()->load($website);
        }
        return $website->getGroupCollection();
    }


    public function getStoreCollection($group)
    {
        if (!$group instanceof \Magento\Store\Model\Group) {
            $group = $this->_storeGroupFactory->create()->load($group);
        }
        $stores = $group->getStoreCollection();
        $_storeIds = $this->getStoreIds();
        if (!empty($_storeIds)) {
            $stores->addIdFilter($_storeIds);
        }
        return $stores;
    }

    public function getStoreIds()
    {
        $allStores = $this->_storeManager->getStores();
        $storeIds = array();
        foreach ($allStores as $storeId => $val)
        {
            $storeIds[] = $storeId;
        }
        return $storeIds;
    }

}
