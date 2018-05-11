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
 * Purchaseorder Code Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Code extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_CODE_ID = 'purchase_order_code_id';

    const CODE = 'code';

    const CURRENT_ID = 'current_id';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchaseordersuccess_purchaseorder_code';

    /**
     * Initialization
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchaseordersuccess/purchaseorder_code');
    }

    /**
     * Get purchase order code id
     *
     * @return int
     */
    public function getPurchaseOrderCodeId(){
        return $this->_getData(self::PURCHASE_ORDER_CODE_ID);
    }

    /**
     * Set purchase order code id
     *
     * @param int $purchaseOrderCodeId
     * @return $this
     */
    public function setPurchaseOrderCodeId($purchaseOrderCodeId){
        return $this->setData(self::PURCHASE_ORDER_CODE_ID, $purchaseOrderCodeId);
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode(){
        return $this->_getData(self::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get current id
     *
     * @return int
     */
    public function getCurrentId(){
        return $this->_getData(self::CURRENT_ID);
    }

    /**
     * Set current id
     *
     * @param int $currentId
     * @return $this
     */
    public function setCurrentId($currentId){
        return $this->setData(self::CURRENT_ID, $currentId);
    }
}