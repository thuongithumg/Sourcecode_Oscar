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
 * Adjuststock Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Adjuststock_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Magestore_Inventorysuccess_Model_Mysql4_Adjuststock_Collection constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/adjuststock');
    }


//    public function addFieldToFilter($field, $condition = null)
//    {
//        if($field == 'created_at'){
//            $condition['to'] = $this->_convertDate($condition['to'], $condition['locale'])->addDay(1)->subSecond(1);
//            $condition['from'] = $condition['orig_from'];
//            $condition['to'] = $condition['orig_to'];
//            Zend_Debug::dump($condition);
////            Zend_Debug::dump($this->getSelect()->__toString());
//            die();
//        }
//    }
}