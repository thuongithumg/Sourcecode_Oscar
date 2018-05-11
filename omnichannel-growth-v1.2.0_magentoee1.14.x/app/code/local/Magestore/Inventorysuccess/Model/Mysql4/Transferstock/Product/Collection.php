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
 * Transferstock Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * @var array
     */
    protected $MAPPING_FIELD = array(
        'differences' => 'main_table.qty - qty_received',
        'qty_remaining' => 'main_table.qty - qty_received - qty_returned'
    );


    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/transferstock_product');
    }

    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->columns(array(
                'differences' => new Zend_Db_Expr($this->MAPPING_FIELD['differences']),
                'qty_remaining' => new Zend_Db_Expr($this->MAPPING_FIELD['qty_remaining'])
            ));
    }

    public function getDifferences()
    {

        return $this->addFieldToFilter(new Zend_Db_Expr($this->MAPPING_FIELD['differences']), array('neq' => 0));
    }

    public function getRemainingItems()
    {
        return $this->addFieldToFilter(new Zend_Db_Expr($this->MAPPING_FIELD['qty_remaining']), array('neq' => 0));
    }


    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     *
     * @param   string|array $field
     * @param   null|string|array $condition
     *
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, array_keys($this->MAPPING_FIELD)))
            $field = new Zend_Db_Expr($this->MAPPING_FIELD[$field]);
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
        if (in_array($field, array_keys($this->MAPPING_FIELD)))
            $field = new Zend_Db_Expr($this->MAPPING_FIELD[$field]);
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
        if (in_array($field, array_keys($this->MAPPING_FIELD)))
            $field = new Zend_Db_Expr($this->MAPPING_FIELD[$field]);
        return parent::addOrder($field, $direction);
    }
}