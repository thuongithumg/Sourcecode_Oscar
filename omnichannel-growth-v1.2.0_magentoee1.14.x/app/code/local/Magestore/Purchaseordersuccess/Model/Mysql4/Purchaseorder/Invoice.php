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
 * Purchaseorder Invoice Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Invoice extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Purchase order invoice table
     */
    const TABLE_NAME = 'os_purchase_order_invoice';
    
    const CODE_LENGTH = 8;

    public function _construct()
    {
        $this->_init('purchaseordersuccess/purchaseorder_invoice', 'purchase_order_invoice_id');
    }

    /**
     * Perform actions after object save
     *
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder_Invoice $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if(!$object->getInvoiceCode()){
            $formatId = pow(10, self::CODE_LENGTH + 1) + $object->getId();
            $formatId = (string) $formatId;
            $formatId = substr($formatId, 0-self::CODE_LENGTH);
            $object->setInvoiceCode($formatId);
        }
        return parent::_afterSave($object);
    }
}