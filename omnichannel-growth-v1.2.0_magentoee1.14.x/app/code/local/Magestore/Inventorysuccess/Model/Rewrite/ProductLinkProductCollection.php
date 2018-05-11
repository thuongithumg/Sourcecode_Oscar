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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */

class Magestore_Inventorysuccess_Model_Rewrite_ProductLinkProductCollection
    extends Mage_Catalog_Model_Resource_Product_Link_Product_Collection
{
    
    /**
     * Initialize collection parent product and add limitation join
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        parent::setProduct($product);
        if(Mage::getSingleton('admin/session')->getUser()) {
            return $this;
        }  
        
        Magestore_Coresuccess_Model_Service::productLimitationService()->filterProductByCurrentStock($this);

        return $this;
    }

}
