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
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Action_Js
    extends Mage_Adminhtml_Block_Template
{
    /**
     * 
     */
    protected function _construct()
    {
        $this->setTemplate('inventorysuccess/catalog/product/edit/action/js.phtml');
        parent::_construct();
    }
    
    /**
     * 
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array(
            'notice_edit_qty' => $this->__('Cannot edit directly product qty! You can update qty of product by %s',
                            '<a href="'. $this->getUrl('adminhtml/inventorysuccess_adjuststock/new') .'" target="_blank">'. 
                            $this->__('Stock adjusting') . '</a>'
                    ),
        );
        return Zend_Json::encode($config);
    }
    
}