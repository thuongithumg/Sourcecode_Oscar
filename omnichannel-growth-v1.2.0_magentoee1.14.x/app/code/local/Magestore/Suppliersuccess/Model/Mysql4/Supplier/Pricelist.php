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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Model_Mysql4_Supplier_Pricelist extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('suppliersuccess/supplier_pricelist', 'supplier_pricelist_id');
    }
    
    /**
     * 
     * @param array $pricelist
     * @return array
     */        
    public function prepareMultipleInsertQuery($pricelist)
    {
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $pricelist, 
            'table' => $this->getMainTable()
        ); 
        return $query;         
    }
    
    /**
     * 
     * @param array $supplierProducts
     */
    public function prepareInsertProductToSupplier($supplierProducts)
    {
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $supplierProducts, 
            'table' => Mage::getModel('suppliersuccess/supplier_product')->getResource()->getMainTable(),
        ); 
        return $query;  
    }
    
    /**
     * 
     * @param array $pricelistIds
     * @return array
     */    
    public function prepareMassDeleteQuery($pricelistIds)
    {
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_DELETE,
            'condition' => array('supplier_pricelist_id IN (?)' => $pricelistIds),
            'table' => $this->getMainTable()
        );  
        return $query;        
    }
    
    /**
     * 
     * @param array $pricelist
     * @return array
     */
    public function prepareMassUpdateQuery($pricelist)
    {
        $query = array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
            'values' => $this->_prepateUpdateValues($pricelist),
            'condition' => array('supplier_pricelist_id IN (?)' => array_keys($pricelist)),
            'table' => $this->getMainTable()
        );   
        return $query;
    }
    
    /**
     * Build updateValues for using in CASE query of Mysql
     * 
     * @param array $data
     * @return array
     */
    protected function _prepateUpdateValues($pricelist)
    {
        $updateValues = array();
        $conditions = array();
        $connection = $this->_getConnection('core_write');
        foreach ($pricelist as $id=>$row) {
            $case = $connection->quoteInto('?', $id);
            /* scan all fields in $data */
            foreach ($row as $field => $value) {
                $conditions[$field][$case] = $connection->quoteInto('?', $value);
            }
        }
        /* bind conditions to $updateValues */
        foreach ($conditions as $field => $condition) {
            $updateValues[$field] = $connection->getCaseSql('supplier_pricelist_id', $condition, $field);
        }
        return $updateValues;
    }     
}