<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *  
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Customergroup extends Varien_Object {

    static public function getOptionArray() {
        $array = array('all' => Mage::helper('webpos')->__('All groups'));
        $groups = Mage::getModel('customer/group')->getCollection();
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

    static public function getOptionHash() {
        return self::getOptionArray();
    }

    public function getAllowCustomerGroups() {
        $customer_group = array();
        $posSession = Mage::getModel('webpos/session');
        $user = $posSession->getUser();
        if ($user->getId() != '') {
            $customer_group = explode(',', $user->getData('customer_group'));
        }
        return $customer_group;
    }

    public function filterCustomerCollection($collection) {
        try {
            if ($collection) {
                $customer_group = $this->getAllowCustomerGroups();
                if (count($customer_group) > 0 && !in_array('all', $customer_group)) {
                    $collection->addAttributeToFilter(array(array('attribute' => 'group_id', 'in' => $customer_group)));
                }
            }
            return $collection;
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $collection;
    }

}
