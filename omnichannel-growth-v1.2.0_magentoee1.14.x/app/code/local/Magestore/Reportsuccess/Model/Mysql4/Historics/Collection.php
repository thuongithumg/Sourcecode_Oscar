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
 * Reportsuccess Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Mysql4_Historics_Collection extends
    Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * @var array
     */
    protected $MAPPING_FIELD_TO_FILTER = array(
        'name'=>'name_table.value',
        'mac' => 'main_table.mac',
        'total_qty' => 'Sum(main_table.total_qty)',
        'inv_value' => 'Sum(main_table.inv_value)',
    );

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('reportsuccess/historics');
    }

    /**
     * @return mixed
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        // Count doesn't work with group by columns keep the group by
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

    /**
     * @param $column
     * @param $value
     * @return $this
     */
    public function addFieldToFilterTextCallback($column, $value){
        //$this->getSelect()->having($this->MAPPING_FIELD_TO_FILTER[$column] . ' >= ?', $value['from']);
        $this->getSelect()->where($this->MAPPING_FIELD_TO_FILTER[$column].' like ?', "%".$value."%" );
        return $this;
    }

    /**
     * @param $column
     * @param $value
     * @return $this
     */
    public function addFieldToFilterNumberCallback($column, $value){
        if (isset($value['from'])) {
            $this->getSelect()->where($this->MAPPING_FIELD_TO_FILTER[$column] . ' >= ?', $value['from']);
        }
        if (isset($value['to'])) {
            $this->getSelect()->where($this->MAPPING_FIELD_TO_FILTER[$column] . ' <= ?', $value['to']);
        }
        return $this;
    }

}