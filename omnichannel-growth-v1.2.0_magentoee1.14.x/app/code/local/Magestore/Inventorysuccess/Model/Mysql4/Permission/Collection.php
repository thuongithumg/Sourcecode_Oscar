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
 * Permission Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Permission_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/permission');
    }

    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            array('user' => Mage::getSingleton('core/resource')->getTableName('admin_user')),
            'main_table.user_id = user.user_id',
            array('username')
        );
        return $this;
    }

    /**
     * Join permission table with warehouse table
     *
     * @return $this
     */
    public function joinWarehouse()
    {
        $warehouse = Mage::getModel('inventorysuccess/warehouse');
        $this->getSelect()->joinLeft(
            array('warehouse' => $warehouse->getResource()->getMainTable()),
            'main_table.object_id = warehouse.warehouse_id AND main_table.object_type = ' . $warehouse->getPermissionType(),
            array()
        );
        return $this;
    }

    /**
     * Filter permission by staff
     * 
     * @param $staffId
     * @return $this
     */
    public function setStaffToFilter($staffId){
        return $this->addFieldToFilter('main_table.user_id', $staffId);
    }

    /**
     * @return array
     */
    public function getAllObjectIDs()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('main_table.object_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Add field filter to collection
     *
     * @param   string|array $field
     * @param   null|string|array $condition
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'user_id')
            $field = new Zend_Db_Expr('main_table.user_id');
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'user_id')
            $field = new Zend_Db_Expr('main_table.user_id');
        return parent::setOrder($field, $direction);
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return Varien_Data_Collection_Db
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'user_id')
            $field = new Zend_Db_Expr('main_table.user_id');
        return parent::addOrder($field, $direction);
    }
}