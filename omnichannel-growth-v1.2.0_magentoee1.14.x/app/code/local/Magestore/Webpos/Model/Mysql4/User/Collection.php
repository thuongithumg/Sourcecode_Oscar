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

class Magestore_Webpos_Model_Mysql4_User_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {	
        parent::_construct();
        $this->_init('webpos/user');
    }
    public function getSelectCountSql(){
    	$this->_renderFilters();
    	$countSelect = clone $this->getSelect();
    	$countSelect->reset(Zend_Db_Select::ORDER);
    	$countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
    	$countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
    	$countSelect->reset(Zend_Db_Select::COLUMNS);
    	
    	
    	if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
    		$countSelect->reset(Zend_Db_Select::GROUP);
    		$countSelect->distinct(true);
    		$group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
    		$countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
    	} else {
    		$countSelect->columns('COUNT(*)');
    	}
    	return $countSelect;
    }
}