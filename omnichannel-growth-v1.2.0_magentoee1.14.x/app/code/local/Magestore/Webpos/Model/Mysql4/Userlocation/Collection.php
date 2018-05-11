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

/**
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:52 SA
 */
class Magestore_Webpos_Model_Mysql4_Userlocation_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webpos/userlocation');
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('location_id', 'display_name');
    }

    public function getAvailableLocation($staffId = null)
    {
        if ($staffId) {
            $staff = Mage::getModel('webpos/user')->load($staffId);
            $locationIds = $staff->getLocationId();
            if ($locationIds) {
                $locationIds = explode(',', $locationIds);
                $this->addFieldToFilter('location_id', array('in' => array($locationIds)));
            }
        }
        $websiteId = Mage::app()->getRequest()->getParam('website_id');
        $website = Mage::app()->getWebsite($websiteId);
        $storeIds = $website->getStoreIds();
        if(count($storeIds)) {
            $this->addFieldToFilter('location_store_id', array('in' => $storeIds));
        }
    }
}