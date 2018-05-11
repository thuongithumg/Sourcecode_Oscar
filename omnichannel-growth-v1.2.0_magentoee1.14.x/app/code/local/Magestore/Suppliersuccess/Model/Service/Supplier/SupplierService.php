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
 * Suppliersuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Model_Service_Supplier_SupplierService 
    extends Magestore_Coresuccess_Model_Service_ProductSelection_ProductSelectionService
{
    /**
     * set products to selection
     * 
     * @param Magestore_Suppliersuccess_Model_Supplier $supplier
     * @param array $data
     * @return Magestore_Suppliersuccess_Model_Service_Supplier_SupplierService
     */
    public function setProductsToSupplier($supplier, $data , $fromCatalog = false)
    {
        if(count($data)) {
            $data = $this->verifyData($data);
        }

        parent::setProducts($supplier, $data , $fromCatalog);
        return $this;
    }

    /**
     * add products to selection
     *
     * @param Magestore_Suppliersuccess_Model_Supplier $supplier
     * @param array $data
     */
    public function addProductsToSupplier($supplier, $data) {
        $data = $this->verifyData($data);

        if(count($data)) {
            parent::addProducts($supplier, $data);
        }

        return $this;
    }

    protected function verifyData($data) {
        if(count($data)) {
            $productList = array();
            $products = Mage::getModel('catalog/product')->getCollection()
                            ->addFieldToFilter('entity_id', array('in' => array_keys($data)))
                            ->addAttributeToSelect('name');
            if($products->getSize()) {
                foreach($products as $product) {
                    $productList[$product->getId()] = $product;
                }
            }
            foreach($data as $productId => &$productData) {
                $productData['product_id'] = $productId;
                $productData['updated_at'] = now();
                if(!isset($productList[$productId])) {
                    continue;
                }
                $productData['product_name'] = $productList[$productId]->getName();
                $productData['product_sku'] = $productList[$productId]->getSku();
            }
        }
        return $data;
    }
    
    /**
     * 
     * @return array
     */
    public function getSupplierOption()
    {
        $options = array();
        $suppliers = Mage::getModel('suppliersuccess/supplier')->getCollection();
        if($suppliers->getSize()) {
            foreach($suppliers as $supplier) {
                $options[$supplier->getId()] = $supplier->getSupplierName();
            }
        }
        return $options;
    }
    
    /**
     * 
     * @return array
     */
    public function getSupplierOptionHash()
    {
        $options = $this->getSupplierOption();
        $optionHash = array();
        if(count($options)) {
            foreach($options as $value=>$label) {
                $optionHash[] = array(
                    'value' => $value,
                    'label' => $label,
                );
            }
        }
        return $optionHash;
    }
}