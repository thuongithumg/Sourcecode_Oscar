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
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Coresuccess Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
class Magestore_Coresuccess_Model_Mysql4_ProductSelection extends Magestore_Coresuccess_Model_Mysql4_Base
{
    
    /**
     * Prepare adding products to Selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $data
     * @return Magestore_Coresuccess_Model_Mysql4_ProductSelection_ProductSelection
     */
    public function prepareAddProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $data)
    {
        /* update existed products in Selection */
        $newProductData = $this->prepareUpdateExistedProducts($selection, $data);
        /* add new products to Selection */
        $this->prepareAddNewProducts($selection, $newProductData);

        return $this;
    }
    
    
    /**
     * Get products from Selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $productIds
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $productIds = array())
    {
        $selectionResource = $selection->getResource();
        $collection = $selection->getSelectionProductModel()->getCollection();

        $selectionProducts = $collection->addFieldToFilter($selectionResource->getIdFieldName(), $selection->getId());
        if (count($productIds)) {
            $selectionProducts->addFieldToFilter('product_id', array('in' => $productIds));
        }
        return $selectionProducts;
    }    

    /**
     * Prepare query data to update existed products in selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $data
     * @return array ["new_products", "query_data"]
     */
    public function prepareUpdateExistedProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $data)
    {
        $productIds = array_keys($data);
        $selectionProducts = $this->getProducts($selection, $productIds);
        $returnData = array(
            'new_products' => $data,
            'query_data' => null,
        );
        /* update existed products in Selection */
        if (!$selectionProducts->getSize()) {
            return $returnData;
        }
        $newProducts = $data;
        $selectionProductResource = $selection->getSelectionProductModel()->getResource();
        $existedProductIds = array();
        foreach ($selectionProducts as $selectionProduct) {
            $existedProductIds[] = $selectionProduct->getProductId();
            /* remove existed products from $data */
            unset($newProducts[$selectionProduct->getProductId()]);
        }
        /* prepare updateValues for using in CASE query of Mysql */
        $values = $this->_prepateUpdateValues($selectionProducts, $data);
        $where = array(
            'product_id IN (?)' => $existedProductIds,
            $selection->getResource()->getIdFieldName().'=?' => $selection->getId()
        );
        
        $returnData['new_products'] = $newProducts;
        /* add query to $returnData */
        if (count($values)) {
            $returnData['query_data'] = array(
                'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
                'values' => $values,
                'condition' => $where,
                'table' => $selectionProductResource->getMainTable()
            );
        }
        return $returnData;
    }

    /**
     * Prepare query data to add new products to selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $data
     * @return array
     */
    public function prepareAddNewProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $data)
    {
        /* add new products to Selection */
        if (!count($data)) {
            return array();
        }
        $selectionProductResource = $selection->getSelectionProductModel()->getResource();
        $insertData = array();
        foreach ($data as $productId => $productData) {
            $productData['product_id'] = $productId;
            $productData[$selection->getResource()->getIdFieldName()] = $selection->getId();
            $insertData[] = $productData;
        }

        return array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_INSERT,
            'values' => $insertData,
            'table' => $selectionProductResource->getMainTable()
        );
    }   
    
    /**
     * prepare query data to remove products from selection
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $productIds
     * @return array
     */
    public function prepareRemoveProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $productIds = array())
    {
        $selectionProductResource = $selection->getSelectionProductModel()->getResource();
        $conditions = array($selection->getResource()->getIdFieldName() . ' = ?' => $selection->getId());
        if (count($productIds)) {
            $conditions['product_id IN (?)'] = $productIds;
        }
        return array(
            'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_DELETE,
            'condition' => $conditions,
            'table' => $selectionProductResource->getMainTable()
        );
    }
    
    /**
     * prepare queries to:
     * remove existed products not in $data
     * update existed products in $data
     * add new products
     * add queries to Processor
     * 
     * @param Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection
     * @param array $data
     * @return array
     */
    public function prepareSetProducts(Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface $selection, $data , $fromCatalog = false)
    {
        $queries = array();
        /* load existed products from Selection */
        $existedProducts = array();
        $existedProductIds = array();
        $newProducts = $data;
        $selectionProducts = $this->getProducts($selection);
        if($selectionProducts->getSize()) {
            foreach($selectionProducts as $selectionProduct) {
                $productId = $selectionProduct->getProductId();
                $existedProducts[$productId] = $selectionProduct->getData();
                /* remove existed product from $newProducts */
                if(isset($newProducts[$productId])) {
                    $existedProductIds[$productId] = $productId;
                    unset($newProducts[$productId]);
                }
            }
        }
        /* remove existed products but not in post data */
        $deleteProducts = array_diff_key($existedProducts, $data);
        if(count($deleteProducts) && !$fromCatalog) {
            $queries[] = $this->prepareRemoveProducts($selection, array_keys($deleteProducts));
        }
        
        /* update existed products in the Selection */
        $values = $this->_prepateUpdateValues($selectionProducts, $data);
        $where = array(
            'product_id IN (?)' => $existedProductIds,
            $selection->getResource()->getIdFieldName().'=?' => $selection->getId()
        );

        if (count($values)) {
            $queries[] = array(
                'type' => Magestore_Coresuccess_Model_Mysql4_QueryProcessor::QUERY_TYPE_UPDATE,
                'values' => $values,
                'condition' => $where,
                'table' =>  $selection->getSelectionProductModel()->getResource()->getMainTable()
            );
        }        
        
        /* add new products to the Selection */
        $queries[] = $this->prepareAddNewProducts($selection, $newProducts);

        return $queries;
    }    
    
    /**
     * Build updateValues for using in CASE query of Mysql
     * 
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $selectionProducts
     * @param array $data
     * @return array
     */
    protected function _prepateUpdateValues($selectionProducts, $data)
    {
        $updateValues = array();
        $conditions = array();
        $connection = $this->_getConnection('core_write');
        foreach ($selectionProducts as $selectionProduct) {
            $productId = $selectionProduct->getProductId();            
            if(!isset($data[$productId])) 
                continue;
            $case = $connection->quoteInto('?', $productId);
            /* scan all fields in $data */
            foreach ($data[$productId] as $field => $value) {
                if ($selectionProduct->getData($field) != $value) {
                    /* if change the data of $field */
                    $conditions[$field][$case] = $connection->quoteInto('?', $value);
                }
            }
        }
        /* bind conditions to $updateValues */
        foreach ($conditions as $field => $condition) {
            $updateValues[$field] = $connection->getCaseSql('product_id', $condition, $field);
        }
        return $updateValues;
    }    
    
}