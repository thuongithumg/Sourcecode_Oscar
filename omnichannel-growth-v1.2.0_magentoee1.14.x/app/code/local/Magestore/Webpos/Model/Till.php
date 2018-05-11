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

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Till extends Mage_Core_Model_Abstract
{
    const VALUE_ALL_TILL = 'all';

    /**
     * Contructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('webpos/till');
    }

    /**
     * Get enable till
     * @param bool $locationId
     * @return mixed
     */
    public function getEnableTill($locationId = false)
    {
        $collection = $this->getCollection()->addFieldToFilter('status', Magestore_Webpos_Model_Status::STATUS_ENABLED);
        if ($locationId) {
            $collection->addFieldToFilter('location_id', $locationId);
        }
        return $collection;
    }

    /**
     * For select element
     * @return array
     */
    public function toOptionArray($locationId = false)
    {
        $options = array();
        $collection = $this->getEnableTill($locationId);
        if ($collection->getSize() > 0) {
            $options = array(self::VALUE_ALL_TILL => Mage::helper('webpos')->__('---All Cash Drawer---'));
            foreach ($collection as $till) {
                $key = $till->getTillId();
                $value = $till->getTillName();
                $options [$key] = $value;
            }
        }
        return $options;
    }

    /**
     * For multiple select element
     * @return array
     */
    public function getOptionArray($locationId = false)
    {
        $options = array();
        $collection = $this->getEnableTill($locationId);
        if ($collection->getSize() > 0) {
            $options[] = array(
                'value' => self::VALUE_ALL_TILL,
                'label' => Mage::helper('webpos')->__('--- All ---')
            );
            foreach ($collection as $till) {
                $key = $till->getTillId();
                $value = $till->getTillName();
                $options[] = array(
                    'value' => $key,
                    'label' => $value
                );
            }
        }
        return $options;
    }

    /**
     * get staff list no using pos
     * return array
     */
    public function getAvailableStaff($tillId = 0)
    {
        $options = array();
        $options[] = array('value' => null, 'label' => ' ');
        $collection = $this->getCollection()->getAvailabeStaff($tillId);
        $optionsArr = array();
        if ($collection->getSize() > 0) {
            foreach ($collection as $staff) {
                $optionsArr[] = array('value' => $staff->getId(), 'label' => $staff->getData('display_name'));
            }
        }
        $options = array_merge($options, $optionsArr);
        return $options;
    }

    /**
     * assign staff for pos
     *
     * @param string $posId
     * @param string $staffId
     * @return boolean
     * @throws Exception
     */
    public function assignStaff($posId, $staffId)
    {
        $posList = $this->getCollection()
            ->addFieldToFilter('user_id', $staffId);
        if($posList->getSize()) {
            foreach ($posList as $pos) {
                $pos->setUserId(null);
                try {
                    $pos->save();
                }catch (\Exception $e) {

                }
            }
        }
        $pos = $this->load($posId);
        if(Mage::getStoreConfig('webpos/general/enable_session')) {
            if ($pos->getUserId() && $pos->getUserId() != $staffId) {
                throw new Exception(Mage::helper('webpos')->__('Can not connect to the pos'));
            }
        }
        $pos->setUserId($staffId);
        try {
            $pos->save();
        }catch (\Exception $e) {
            throw new Exception(Mage::helper('webpos')->__('Can not connect to the pos'));
        }
        if($pos->getId() || $pos->getId() == 0) {

        }
        return true;
    }

    /**
     * unassign staff in pos
     * @param string $posId
     *
     * @return mixed
     */
    public function unassignStaff($posId)
    {
        $pos = $this->load($posId);
        $pos->setUserId(null);
        try {
            $pos->save();
        }catch (\Exception $e) {

        }
    }
}