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

class Magestore_Inventorysuccess_Model_Observer_Integration_Webpos_LocationPrepareForm
{
    /**
     *
     * @param type $observer
     * @return $this
     */
    public function execute($observer)
    {
        $fieldset = $observer->getFieldSet();
        $modelData = $observer->getModelData();
        $warehouseLocationMap = Mage::getModel('inventorysuccess/warehouseLocationMap')
                                ->load($modelData->getData('location_id'), 'location_id');
        if($warehouseLocationMap->getWarehouseId()){
            $modelData->setData('warehouse_id', $warehouseLocationMap->getWarehouseId());
        }
        $fieldset->addField('warehouse_id', 'select', array(
            'label'  => Mage::helper('inventorysuccess')->__('Warehouse'),
            'name'   => 'warehouse_id',
            'values' => Magestore_Coresuccess_Model_Service::locationService()->toOptionArray($modelData->getData('location_id'))
        ));
        return $this;
    }
}