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
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Product_Import
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Product_Import
    extends Magestore_Coresuccess_Block_Adminhtml_Modal_Import_Ajax
{

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->__('Please choose a CSV file to import products to this supplier. You can download the sample CSV file below.');
    }
    
    /**
     *
     * @return string
     */
    public function getCsvSampleLink()
    {
        $url = $this->getUrl('adminhtml/suppliersuccess_product/downloadsample', array(
            '_secure' => true,
            'id' => $this->getRequest()->getParam('id')
        ));
        return $url;
    }    

    /**
     * Get import url
     *
     * @return string
     */
    public function getImportLink()
    {
        return $this->getUrl('adminhtml/suppliersuccess_product/import', array(
                    '_secure' => true,
                    'id' => $this->getRequest()->getParam('id')
        ));
    }
    
    /**
     * get id of left tab container
     * 
     * @return string|nulll
     */
    public function getTabContainerId()
    {
        return 'supplier_tabs';
    }
    
    /**
     * get id of tab which contains product grid
     * 
     * @return string|nulll
     */
    public function getTabId()
    {
        return 'supplier_tabs_products_section';
    }    

}
