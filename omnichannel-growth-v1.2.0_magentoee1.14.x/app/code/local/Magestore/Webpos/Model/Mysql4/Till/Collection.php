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

class Magestore_Webpos_Model_Mysql4_Till_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/till');
    }

    /**
     * Get Available Staff
     *
     * @param string $tillId
     * @return $this
     */
    public function getAvailabeStaff($tillId)
    {
        $collection = Mage::getModel('webpos/user')->getCollection();
        if(Mage::getStoreConfig('webpos/general/enable_session')) {
            $tillCollection = $this;
            if ($tillId != 0) {
                $tillCollection->addFieldToFilter('till_id', array('nin' => array($tillId)))
                    ->addFieldToFilter('user_id', array('neq' => null));
            }
            $staffIds = array();
            if ($tillCollection->getSize() > 0) {
                foreach ($tillCollection as $till) {
                    $staffIds[] = $till->getUserId();
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
    public function getAvailableTill($staffId = null)
    {
        if(Mage::getStoreConfig('webpos/general/enable_session')) {
            if ($staffId) {
                $staff = Mage::getModel('webpos/user')->load($staffId);
                $staffIds = array($staffId, NULL, 0);
                $tillIds = $staff->getTillIds();
                if ($tillIds) {
                    if(!in_array('all', $tillIds)) {
                        $this->addFieldToFilter('till_id', array('in' => array($tillIds)));
                    }
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

}