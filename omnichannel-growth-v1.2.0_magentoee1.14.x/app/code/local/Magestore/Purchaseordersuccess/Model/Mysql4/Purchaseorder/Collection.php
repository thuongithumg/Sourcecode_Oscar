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
 * Purchaseorder Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */

use Magestore_Purchaseordersuccess_Model_Purchaseorder as PurchaseorderModel;
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type as PurchaseorderType;

class Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder');
        $this->_setIdFieldName('purchase_order_id');
    }

    /**
     * Get all quotation
     * 
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getAllQuotation(){
        return $this->addFieldToFilter(PurchaseorderModel::TYPE, PurchaseorderType::TYPE_QUOTATION);
    }

    /**
     * Get all purchase order
     * 
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getAllPurchaseOrder(){
        return $this->addFieldToFilter(PurchaseorderModel::TYPE, PurchaseorderType::TYPE_PURCHASE_ORDER);
    }
}