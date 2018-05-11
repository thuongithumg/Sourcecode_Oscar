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
 * Inventorysuccess Roles User Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Roles_User_Collection extends Mage_Admin_Model_Mysql4_Roles_User_Collection
{
    protected $MAPPING_FIELDS = array(
        'user_id' => 'main_table.user_id',
        'role_id' => 'role.parent_id',
        'fullname'=> 'CONCAT(main_table.firstname, " ",main_table.lastname)',
    );
    
    /**
     * Initialize select
     *
     * @return Mage_Admin_Model_Resource_Roles_User_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getMainTable()))
            ->joinLeft(
                array('role'=> Mage::getSingleton('core/resource')->getTableName('admin_role')),
                'main_table.user_id = role.user_id',
                array('')
            )
            ->columns(array(
                'role_id' => $this->MAPPING_FIELDS['role_id'],
                'fullname' => $this->MAPPING_FIELDS['fullname'],
            ))
            ->where('main_table.user_id > ?', 0)
            ->where('role.role_type = ?', 'U');
        return $this;
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
        foreach ($this->MAPPING_FIELDS as $alias => $realField) {
            if ($alias == $field)
                $field = new Zend_Db_Expr($this->MAPPING_FIELDS[$alias]);
        }
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
        foreach ($this->MAPPING_FIELDS as $alias => $realField) {
            if ($alias == $field)
                $field = new Zend_Db_Expr($this->MAPPING_FIELDS[$alias]);
        }
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
        foreach ($this->MAPPING_FIELDS as $alias => $realField) {
            if ($alias == $field)
                $field = new Zend_Db_Expr($this->MAPPING_FIELDS[$alias]);
        }
        return parent::addOrder($field, $direction);
    }
}
