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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Mysql4_Return extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Purchase order table
     */
    const TABLE_NAME = 'os_return_order';

    public function _construct()
    {
        $this->_init('purchaseordersuccess/return', 'return_id');
    }


    /**
     * Perform actions before object save
     *
     * @param Magestore_Purchaseordersuccess_Model_Return $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$this->isValidPostData($object)) {
            throw new \Exception(
                Mage::helper('purchaseordersuccess')->__('Required field is null')
            );
        }
        if (!$object->getId()) {
            $object->isObjectNew(true);
            $user = Mage::getSingleton('admin/session')->getUser();
            $object->setStatus(Magestore_Purchaseordersuccess_Model_Return_Options_Status::STATUS_PENDING);
            $object->setUserId($user->getId());
            $object->setCreatedBy($user->getUserName());
        }

        if(!$object->getReturnCode()) {
            Mage::getSingleton('purchaseordersuccess/service_returnService')->getReturnCode($object);
        }

        return parent::_beforeSave($object);
    }

    /**
     *  Check whether post data is valid
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    protected function isValidPostData(Mage_Core_Model_Abstract $object)
    {
        if (is_null($object->getData('supplier_id')) || is_null($object->getData('returned_at'))
                || is_null($object->getData('warehouse_id'))) {
            return false;
        }
        return true;
    }
}