<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup
 * 
 * Web POS CustomerGroup source model
 * Use to get magento customer group
 * Methods:
 *  filterCustomerCollection
 *  getAllowCustomerGroups
 *  getOptionArray
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
/**
 * Class CustomerGroup
 * @package Magestore\Webpos\Model\Source\Adminhtml
 */
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface {
    
    /**
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory 
     */
    protected $_customerGroupCollectionFactory;
    
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface 
     */
    protected $_objectManager;
    
    /**
     * 
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->_objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $array = array('all' => __('All groups'));
        $groups = $this->_customerGroupCollectionFactory->create();
        if (count($groups) > 0) {
            foreach ($groups as $group) {
                if ($group->getId() == 0)
                    continue;
                $array[$group->getId()] = $group->getData('customer_group_code');
            }
        }
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
    public function getAllCustomerOption()
    {
        $groups = $this->_customerGroupCollectionFactory->create();
        $options = array();
        if (count($groups) > 0) {
            foreach ($groups as $group) {
                if ($group->getId() == 0)
                    continue;
                $options[] = array(
                    'id' => (int)$group->getId(),
                    'code' => $group->getData('customer_group_code'),
                    'tax_class_id' => (int)$group->getData('tax_class_id')
                );
            }
        }
        return $options;
    }

    /**
     * @return array|null
     */
    public function getAllCustomerByCurrentStaff()
    {
        $staffModel = $this->_objectManager->get('Magestore\Webpos\Helper\Permission')
            ->getCurrentStaffModel();
        if ($staffModel->getId()) {
            $staffOfGroup = explode(',', $staffModel->getCustomerGroup());
            if (in_array('all', $staffOfGroup)) {
                return $this->getAllCustomerOption();
            } else {
                $array = array();
                $groups = $this->_customerGroupCollectionFactory->create()->addFieldToFilter('customer_group_id', array('in' => $staffOfGroup));
                if (count($groups) > 0) {
                    foreach ($groups as $group) {
                        if ($group->getId() == 0)
                            continue;
                        $array[$group->getId()] = $group->getData('customer_group_code');
                    }
                }
                $options = array();
                foreach ($array as $value => $label) {
                    $options[] = array(
                        'id' => $value,
                        'code' => $label
                    );
                }
                return $options;

            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $array = array('all' => __('All groups'));
        $groups = $this->_customerGroupCollectionFactory->create();
        if (count($groups) > 0) {
            foreach ($groups as $group) {
                if ($group->getId() == 0)
                    continue;
                $array[$group->getId()] = $group->getData('customer_group_code');
            }
        }

        return $array;
    }
    
    /**
     * 
     * @return array
     */
    public function getAllowCustomerGroups() {
        $customer_group = array();
        $posSession = $this->_objectManager->get('Magestore\Webpos\Model\WebPosSession');
        $user = $posSession->getUser();
        if ($user->getId() != '') {
            $customer_group = explode(',', $user->getData('customer_group'));
        }
        return $customer_group;
    }
    
    /**
     * 
     * @param type $collection
     * @return Customer group collection filtered
     */
    public function filterCustomerCollection($collection) {
        try {
            if ($collection) {
                $customer_group = $this->getAllowCustomerGroups();
                if (count($customer_group) > 0 && !in_array('all', $customer_group)) {
                    $collection->addAttributeToFilter(array(array('attribute' => 'group_id', 'in' => $customer_group)));
                }
            }
            return $collection;
        } catch (\Exception $e) {
            return $collection;
        }
 
    }



}
