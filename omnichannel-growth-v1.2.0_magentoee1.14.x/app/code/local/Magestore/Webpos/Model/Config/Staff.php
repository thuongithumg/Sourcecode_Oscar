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

class Magestore_Webpos_Model_Config_Staff extends Magestore_Webpos_Model_Abstract
{
    public function getConfig()
    {
        $output = array();

        $staffModel = Mage::helper('webpos/permission')->getCurrentStaffModel();
        $output['staffId'] = $staffModel->getId();
        $output['posId'] = Mage::helper('webpos/permission')->getCurrentPosId();
        $posModel = Mage::getModel('webpos/pos')->load($output['posId']);
        $output['posName'] =  $posModel->getData('pos_name');
        $output['staffName'] = $staffModel->getDisplayName();
        $output['customerGroupOfStaff'] = $staffModel->getCustomerGroup();
        $output['maximum_discount_percent'] = Mage::helper('webpos/permission')->getMaximumDiscountPercent();
        $resourceAccess = array();
        $output['currentLocationName'] = '';
        if (Mage::helper('webpos/permission')->getCurrentUser()) {
            $staffId = Mage::helper('webpos/permission')->getCurrentUser();
            $staffModel = Mage::getModel('webpos/user')->load($staffId);
            $roleId = $staffModel->getRoleId();

            $roleModel =  Mage::getModel('webpos/role')->load($roleId);

            $authorizeRuleCollection = explode(',',$roleModel->getPermissionIds());
            $roleOptionsArray = $roleModel->getOptionArray();
            foreach ($authorizeRuleCollection as $authorizeRule) {
                if (array_key_exists($authorizeRule,$roleOptionsArray))
                {
                    $resourceAccess[] = $roleOptionsArray[$authorizeRule];
                }
            }
            $locationModel = Mage::helper('webpos/permission')->getCurrentLocationObject();
            $output['locationId'] = $locationModel->getId();
            $output['currentLocationName'] = $locationModel->getDisplayName() ? $locationModel->getDisplayName() : '';
        }

        $output['staffResourceAccess'] = $resourceAccess;

        $configObject = new Varien_Object();
        $configObject->setData($output);

        $output = $configObject->getData();

        return $output;
    }

}
