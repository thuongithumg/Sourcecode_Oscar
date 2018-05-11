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

class Magestore_Webpos_Model_Source_Adminhtml_Customergroup extends Varien_Object {

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $array = array('all' => Mage::helper('webpos')->__('All groups'));
        $groups = Mage::getModel('customer/group')->getCollection();
        if ($groups->getSize() > 0) {
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
        $array = array();
        $groups = Mage::getModel('customer/group')->getCollection();
        $options = array();
        if ($groups->getSize() > 0) {
            foreach ($groups as $group) {
                if ($group->getId() == 0)
                    continue;
                $array[$group->getId()] = $group->getData('customer_group_code');
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
     * @return array
     */
    public function getOptionArray()
    {
        $array = array('all' => Mage::helper('webpos')->__('All groups'));
        $groups = Mage::getModel('customer/group')->getCollection();
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
     * @return array|null
     */
    public function getAllCustomerByCurrentStaff()
    {
        //return $this->getAllCustomerOption();

        $staffModel = Mage::helper('webpos/permission')->getCurrentStaffModel();
        if ($staffModel->getId()) {
            $staffOfGroup = explode(',', $staffModel->getCustomerGroup());
            if (in_array('all', $staffOfGroup)) {
                return $this->getAllCustomerOption();
            } else {
                $array = array();
                $groups = Mage::getModel('customer/group')->getCollection()
                    ->addFieldToFilter('customer_group_id', array('in' => $staffOfGroup));
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
}
