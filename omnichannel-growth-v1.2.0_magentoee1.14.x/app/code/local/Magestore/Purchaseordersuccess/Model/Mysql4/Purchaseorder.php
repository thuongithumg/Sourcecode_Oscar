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
class Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Purchase order table
     */
    const TABLE_NAME = 'os_purchase_order';

    public function _construct()
    {
        $this->_init('purchaseordersuccess/purchaseorder', 'purchase_order_id');
    }


    /**
     * Perform actions before object save
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $object
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
            $object->setStatus(Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status::STATUS_PENDING);
            $object->setUserId($user->getId());
            $object->setCreatedBy($user->getUserName());
        } else {
            Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_config_shippingMethod')
                ->saveConfig($object);
            Magestore_Coresuccess_Model_Service::getService('purchaseordersuccess/service_config_paymentTerm')
                ->saveConfig($object);
            if ($object->getItems()->getSize() == 0 || !is_numeric($object->getShippingCost())) {
                $object->setShippingCost(0);
            }
            $object->setGrandTotalExclTax(
                $object->getSubtotal() + $object->getShippingCost() + $object->getTotalDiscount()
            );
            $object->setGrandTotalInclTax(
                $object->getGrandTotalExclTax() + $object->getTotalTax()
            );
        }
        Magestore_Coresuccess_Model_Service::purchaseorderService()->getPurchaseCode($object);

        if($object->getPurchaseKey() == '') {
            $tmp = [
                'id' => $object->getPurchaseCode(),
                'supplier_id' => $object->getSupplierId()
            ];
            $key = hash('sha256', serialize($tmp));
            $object->setPurchaseKey($key);
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
        if (is_null($object->getData('supplier_id')) || is_null($object->getData('purchased_at'))) {
            return false;
        }
        return true;
    }
}