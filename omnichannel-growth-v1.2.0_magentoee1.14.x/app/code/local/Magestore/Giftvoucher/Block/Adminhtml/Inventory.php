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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Inventory
 */
class Magestore_Giftvoucher_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory {

    /**
     * @return bool
     * @throws Exception
     */
    public function isNew() {
        if ($this->getRequest()->getParam('type') == 'giftvoucher' && Mage::app()->getRequest()->getActionName() == 'new') {
            return false;
        }
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

}