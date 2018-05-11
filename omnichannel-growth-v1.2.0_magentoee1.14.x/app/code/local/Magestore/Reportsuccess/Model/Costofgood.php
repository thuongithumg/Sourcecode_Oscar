<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Reportsuccess_Model_Costofgood
 */
class Magestore_Reportsuccess_Model_Costofgood extends
    Mage_Core_Model_Abstract
{
    const ID     = 'id';
    const PRODUCT_ID     = 'product_id';
    const MAC     = 'mac';
    public function _construct()
    {
        parent::_construct();
        $this->_init('reportsuccess/costofgood');
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setId( $value )
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductId( $value )
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setMac( $value )
    {
        return $this->setData(self::MAC, $value);
    }

    
    /**
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }
    
    /**
     * @return string
     */
    public function getMac()
    {
        return $this->getData(self::MAC);
    }
    
}