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

class Magestore_Webpos_Model_Mysql4_Pos_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webpos/pos');
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
        $collection = Mage::getModel('webpos/user')->getCollection();
        if(Mage::getStoreConfig('webpos/general/enable_session')) {
            $posCollection = $this;
            if ($posId != 0) {
                $posCollection->addFieldToFilter('pos_id', array('nin' => array($posId)))
                    ->addFieldToFilter('user_id', array('neq' => null));
            }
            $staffIds = array();
            if ($posCollection->getSize() > 0) {
                foreach ($posCollection as $pos) {
                    $staffIds[] = $pos->getUserId();
                }
            }
            if (count($staffIds) > 0) {
                $collection->addFieldToFilter('user_id', array('nin' => array($staffIds)));
            }
        }
        return $collection;
    }
    /**
     * Get Available Staff
     *
     * @param string $staffId
     * @return $this
     */
    public function getAvailablePos($staffId = null)
    {
        if(Mage::getStoreConfig('webpos/general/enable_session')) {
            if ($staffId) {
                $staff = Mage::getModel('webpos/user')->load($staffId);
                $staffIds = array($staffId, NULL, 0);
                $posIds = $staff->getPosIds();
                $locationIds = $staff->getLocationId();
                if ($posIds) {
                    $posIds = explode(',', $posIds);
                    $locationIds = explode(',', $locationIds);
                    $this->addFieldToFilter('pos_id', array('in' => array($posIds)));
                    $this->addFieldToFilter('main_table.location_id', array('in' => array($locationIds)));
                }
                $this->addFieldToFilter('user_id', array(
                        array('in' => array($staffIds)),
                        array('null' => true)
                    )
                );
            }
        }
        $this->addFieldToFilter('status', 1);
        return $this;
    }

    /**
     * join to webpos_user_location table
     *
     * @return $this
     */
    public function joinToLocation()
    {
        $this->getSelect()
            ->joinLeft(
                array("location" => $this->getTable('webpos/userlocation')),
                "main_table.location_id = location.location_id",
                array("location_store_id" => "location.location_store_id",
                    "location_name" => "location.display_name")
            );
        $websiteId = Mage::app()->getRequest()->getParam('website_id');
        $website = Mage::app()->getWebsite($websiteId);
        $storeIds = $website->getStoreIds();
        if(count($storeIds)) {
            $this->addFieldToFilter('location.location_store_id', array('in' => $storeIds));
        }
    }
}