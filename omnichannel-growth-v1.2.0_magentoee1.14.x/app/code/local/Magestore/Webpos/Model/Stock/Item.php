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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewrite Stock Item Model
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */


class Magestore_Webpos_Model_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item {

    /**
     * @return mixed
     */
    public function getBackorders() {
        $routeName = Mage::app()->getRequest()->getRouteName();
        $storeId = Mage::app()->getStore()->getId();
        if ($routeName == 'webpos' && Mage::getStoreConfig('webpos/general/ignore_checkout', $storeId)
        ) {
            return Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY;
        }

        return parent::getBackorders();
    }

    /**
     * @return bool
     */
    public function getIsInStock() {
        $routeName = Mage::app()->getRequest()->getRouteName();
        $storeId = Mage::app()->getStore()->getId();
        if ($routeName == 'webpos' && Mage::getStoreConfig('webpos/general/ignore_checkout', $storeId)) {
            return true;
        }
        return parent::getIsInStock();
    }

}
