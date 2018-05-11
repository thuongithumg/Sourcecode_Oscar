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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseorder Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Mysql4_Catalog_Product_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder */
    protected $purchaseOrder;

    /**
     * Initialize collection
     */
    protected function _construct()
    {
        parent::_construct();
        $this->purchaseOrder = Mage::registry('current_purchase_order');
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
        if ($this->purchaseOrder && $this->purchaseOrder->getId()) {
            if ($field == 'cost')
                $field = "ROUND(supplier_product.cost * {$this->purchaseOrder->getRate()}, 2)";
        }
        if ($field == 'current_cost')
            $field = 'supplier_product.cost';
        if (!is_array($field)) {
            $resultCondition = $this->_translateCondition($field, $condition);
        } else {
            $conditions = array();
            foreach ($field as $key => $currField) {
                $conditions[] = $this->_translateCondition(
                    $currField,
                    isset($condition[$key]) ? $condition[$key] : null
                );
            }

            $resultCondition = '(' . join(') ' . Zend_Db_Select::SQL_OR . ' (', $conditions) . ')';
        }

        $this->_select->where($resultCondition);

        return $this;
    }


    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        if ($this->purchaseOrder && $this->purchaseOrder->getId()) {
            if ($attribute == 'cost')
                $attribute = "ROUND(supplier_product.cost * {$this->purchaseOrder->getRate()}, 2)";
        }
        if ($attribute == 'current_cost')
            $attribute = 'supplier_product.cost';
        if ($attribute == 'price') {
            $this->addAttributeToSort($attribute, $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }
}