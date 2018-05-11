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
$installer = $this;
$installer->startSetup();
if (Mage::helper('core')->isModuleEnabled('Magestore_Inventorysuccess')){
    $warehouseCollection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
    foreach ($warehouseCollection as $warehouse){
        /** @var Magestore_Storepickup_Model_Store $store */
        $store = Mage::getModel('storepickup/store');
        $storeCollection = $store->getCollection()
            ->addFieldToFilter('warehouse_id',$warehouse->getId());
        if(!$storeCollection->getSize()){
            $store->convertWarehouseToStore($warehouse)->save();
        }
    }
}
$installer->endSetup();
