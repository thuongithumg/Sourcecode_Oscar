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
 * API Global ACL model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Webpos_Model_Acl_Global
{
    /**
     * Check if the operation is allowed on resources of given type type for given user type/role
     *
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @param string $resourceType
     * @param string $operation
     * @return boolean
     * @throws Mage_Api2_Exception
     */
    public function isAllowed(Mage_Api2_Model_Auth_User_Abstract $apiUser, $resourceType, $operation)
    {
        return true;
    }
}
