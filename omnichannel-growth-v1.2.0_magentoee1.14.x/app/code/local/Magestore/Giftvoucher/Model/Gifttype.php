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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher Gifttype Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */

class Magestore_Giftvoucher_Model_Gifttype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    const GIFT_TYPE_FIX = 1;
    const GIFT_TYPE_RANGE = 2;
    const GIFT_TYPE_DROPDOWN = 3;

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('giftvoucher')->__('Fixed value'),
                    'value' => self::GIFT_TYPE_FIX
                ),
                array(
                    'label' => Mage::helper('giftvoucher')->__('Range of values'),
                    'value' => self::GIFT_TYPE_RANGE
                ),
                array(
                    'label' => Mage::helper('giftvoucher')->__('Dropdown values'),
                    'value' => self::GIFT_TYPE_DROPDOWN
                ),
            );
        }
        return $this->_options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param string $dir
     * @return Magestore_Giftvoucher_Model_Gifttype
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $valueTable1 = $this->getAttribute()->getAttributeCode() . '_t1';
        $valueTable2 = $this->getAttribute()->getAttributeCode() . '_t2';
        $collection->getSelect()->joinLeft(
            array($valueTable1 => $this->getAttribute()->getBackend()->getTable()), 
                "`e`.`entity_id`=`{$valueTable1}`.`entity_id`"
                . " AND `{$valueTable1}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                . " AND `{$valueTable1}`.`store_id`='{$adminStore}'", array()
        );
        if ($collection->getStoreId() != $adminStore) {
            $collection->getSelect()->joinLeft(
                array($valueTable2 => $this->getAttribute()->getBackend()->getTable()), 
                    "`e`.`entity_id`=`{$valueTable2}`.`entity_id`"
                    . " AND `{$valueTable2}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                    . " AND `{$valueTable2}`.`store_id`='{$collection->getStoreId()}'", array()
            );
            $valueExpr = new Zend_Db_Expr("IF(`{$valueTable2}`.`value_id`>0, `{$valueTable2}`.`value`, "
                . "`{$valueTable1}`.`value`)");
        } else {
            $valueExpr = new Zend_Db_Expr("`{$valueTable1}`.`value`");
        }
        $collection->getSelect()
            ->order($valueExpr, $dir);
        return $this;
    }

    /**
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array(
            $this->getAttribute()->getAttributeCode() => array(
                'type' => 'int',
                'unsigned' => false,
                'is_null' => true,
                'default' => null,
                'extra' => null
            )
        );
        return $columns;
    }

    /**
     * @param int $store
     * @return mixed
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
                ->getFlatUpdateSelect($this->getAttribute(), $store);
    }

}
