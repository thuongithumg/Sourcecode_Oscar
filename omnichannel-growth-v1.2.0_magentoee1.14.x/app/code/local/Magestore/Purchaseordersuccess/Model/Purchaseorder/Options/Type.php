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
 * Purchaseordersuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type 
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    /**
     * Purchase order type value
     */
    const TYPE_QUOTATION = 1;
    
    const TYPE_PURCHASE_ORDER = 2;
    
    const TYPE_QUOTATION_LABEL = 'Quotation';
    
    const TYPE_PURCHASE_ORDER_LABEL = 'Purchase Order';
    
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionHash()
    {
        return array(
            self::TYPE_QUOTATION => Mage::helper('purchaseordersuccess')->__(self::TYPE_QUOTATION_LABEL), 
            self::TYPE_PURCHASE_ORDER => Mage::helper('purchaseordersuccess')->__(self::TYPE_PURCHASE_ORDER_LABEL)
        );
    }

    /**
     * @param $type
     * @return string
     */
    public static function getTypeLabel($type){
        switch ($type){
            case self::TYPE_QUOTATION:
                return Mage::helper('purchaseordersuccess')->__(self::TYPE_QUOTATION_LABEL);
            case self::TYPE_PURCHASE_ORDER:
                return Mage::helper('purchaseordersuccess')->__(self::TYPE_PURCHASE_ORDER_LABEL);
        }
    }

}