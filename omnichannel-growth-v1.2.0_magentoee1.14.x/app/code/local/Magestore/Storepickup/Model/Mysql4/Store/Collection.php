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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Mysql4_Store_Collection
 */
class Magestore_Storepickup_Model_Mysql4_Store_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * @var null
     */
    protected $_store_id = null;
    /**
     * @var array
     */
    protected $_addedTable = array();

    /**
     * @param $value
     * @return $this
     */
    public function setStoreId($value){
		$this->_store_id = $value;
		return $this;
	}

    /**
     * @return null
     */
    public function getStoreId(){
		return $this->_store_id;
	}
    
    public function _construct()
    {
        parent::_construct();
        if ($storeId = Mage::app()->getStore()->getId()) {
            $this->setStoreId($storeId);
        }
        $this->_init('storepickup/store');
    }

    /**
     * @return $this
     */
    protected function _afterLoad(){
    	parent::_afterLoad();
    	if ($storeId = $this->getStoreId()) {
            foreach ($this->_items as $item){
                $item->setStoreId($storeId)->loadStoreValue();
            }
        }
    	return $this;
    }

    /**
     * @param $field
     * @param null $condition
     * @return Magestore_Storepickup_Model_Mysql4_Store_Collection
     */
    public function addFieldToFilter($field, $condition=null) {
        $attributes = array(
            'store_name',
            'status',
            'description',
            'address',
            'city',
        );
        $storeId = $this->getStoreId();
        if (in_array($field, $attributes) && $storeId) {
            if (!in_array($field, $this->_addedTable)) {
                $this->getSelect()
                    ->joinLeft(array($field => $this->getTable('storepickup/value')),
                        "main_table.store_id = $field.storepickup_id" .
                        " AND $field.store_id = $storeId" .
                        " AND $field.attribute_code = '$field'",
                        array()
                    );
                $this->_addedTable[] = $field;
            }
            return $this->addNonReturnedFilter($field, $condition);
        }
        if ($field == 'store_id') {
            $field = 'main_table.store_id';
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param $field
     * @param $condition
     * @return $this
     */
    public function addNonReturnedFilter($field, $condition)
	{
		/** @var Namespace_Module_Model_Resource_Model_Collection $this */
		$expression = 'IF('.$field.'.value IS NULL, main_table.'.$field.', '.$field.'.value)';
		$condition = $this->_getConditionSql($expression, $condition);
		$this->_select->where($condition);

		return $this;
	}
}