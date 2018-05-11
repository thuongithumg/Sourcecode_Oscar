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
class Magestore_Suppliersuccess_Model_Service_Supplier_ProductService
{
    /**
     * 
     * @param int $productId
     * @return Magestore_Suppliersuccess_Model_Mysql4_Supplier_Product_Collection
     */
    public function getProductSuppliers($productId)
    {
        $productSuppliers = Mage::getModel('suppliersuccess/supplier_product')
                                ->getCollection()
                                ->addFieldToFilter('product_id', $productId);
        return $productSuppliers;
    }
}