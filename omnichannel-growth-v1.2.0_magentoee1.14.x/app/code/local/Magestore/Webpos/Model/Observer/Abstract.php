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

abstract class Magestore_Webpos_Model_Observer_Abstract extends Varien_Object
{

    /**
     * @var Magestore_Webpos_Helper_Data
     */
    protected $_helper = false;

    /**
     * @var Magestore_Webpos_Helper_Config
     */
    protected $_config = false;

    /**
     * Magestore_Webpos_Model_Api2_Abstract constructor.
     */
    public function __construct() {
        $this->_helper = $this->_getHelper('webpos');
        $this->_config = $this->_getHelper('webpos/config');
    }
    /**
     * @param $name
     * @param array $arg
     * @return bool | Service instance
     */
    protected function _createService($name, $arg = array()){
        return (!empty($name))?$this->_getModel('magestore_webpos_service_'.$name, true, $arg):false;
    }

    /**
     * @return mixed
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return mixed
     */
    public function getHelperConfig()
    {
        return $this->_config;
    }

    /**
     * @param $class
     * @return mixed
     */
    protected function _getHelper($class){
        return Mage::helper($class);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function __($string){
        return $this->_helper->__($string);
    }

    /**
     * @param $class
     * @param bool $isSingleton
     * @return mixed
     */
    protected function _getModel($class, $isSingleton = false, $args = array()){
        return ($isSingleton)?Mage::getSingleton($class, $args):Mage::getModel($class, $args);
    }

    /**
     * @param $class
     * @param array $args
     * @return mixed
     */
    protected function _getResource($class, $args = array()){
        return Mage::getResourceModel($class, $args);
    }

    /**
     * @param $eventName
     * @param $eventData
     */
    protected function _dispatchEvent($eventName, $eventData){
        Mage::dispatchEvent($eventName, $eventData);
    }

    /**
     * @param $args
     */
    protected function _throwException($args){
        Mage::throwException($args);
    }
}
