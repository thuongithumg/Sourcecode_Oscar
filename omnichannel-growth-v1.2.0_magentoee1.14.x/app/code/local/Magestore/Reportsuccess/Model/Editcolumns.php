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
 * Class Magestore_Reportsuccess_Model_Historics
 */
class Magestore_Reportsuccess_Model_Editcolumns extends
    Mage_Core_Model_Abstract
{
   

    const ID = 'id';
    const GRID = 'grid';
    const VALUE = 'value';

    
    public function _construct()
    {
        parent::_construct();
        $this->_init('reportsuccess/editcolumns');
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
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }
    
    
    
    /**
     * @param $value
     * @return Varien_Object
     */
    public function setGrid( $value )
    {
        return $this->setData(self::GRID, $value);
    }
    /**
     * @return string
     */
    public function getGrid()
    {
        return $this->getData(self::GRID);
    }
    

    
    /**
     * @param $value
     * @return Varien_Object
     */
    public function setValue( $value )
    {
        return $this->setData(self::VALUE, $value);
    }
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
        
    }
       

    
}