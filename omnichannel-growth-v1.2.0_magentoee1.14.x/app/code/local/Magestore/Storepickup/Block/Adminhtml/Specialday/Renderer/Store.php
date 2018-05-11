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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Block_Adminhtml_Specialday_Renderer_Store
 */
class Magestore_Storepickup_Block_Adminhtml_Specialday_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $store = $row->getStoreId();
        $storeIds = explode(",", $store);
        $options = array();
        $store = Mage::getModel('storepickup/store');
            foreach($storeIds as $storeId){
                $store->load($storeId);
                $options[$store->getId()] = $store->getStoreName();
            }
        $result = implode(', ',$options);
        return $result;        
    }
}