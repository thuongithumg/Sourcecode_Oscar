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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_IncrementId extends Mage_Core_Model_Abstract
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const INCREMENT_ID = 'increment_id';    
    const CODE = 'code';    
    const CURRENT_ID = 'current_id';    
    
    
    /**
     * construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/incrementId');
    }
    
    /**
     * get id
     *
     * @return int|null
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * Set id
     *
     * @param int $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }   
    
    /**
     * get code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }   


    /**
     * get current id
     *
     * @return int|null
     */
    public function getCurrentId()
    {
        return $this->getData(self::CURRENT_ID);
    }

    /**
     * Set current id
     *
     * @param int $currentId
     * @return $this
     */
    public function setCurrentId($currentId)
    {
        return $this->setData(self::CURRENT_ID, $currentId);
    }       
}