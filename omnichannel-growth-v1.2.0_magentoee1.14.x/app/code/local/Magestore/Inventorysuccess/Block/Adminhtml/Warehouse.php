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
 * Inventorysuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_warehouse';
        $this->_blockGroup = 'inventorysuccess';
        $this->_headerText = $this->__('Manage Warehouse');
        $this->_addButtonLabel = $this->__('Add a New Warehouse');
        parent::__construct();
        
        if (!Magestore_Coresuccess_Model_Service::permissionService()
            ->checkPermission('admin/inventorysuccess/stocklisting/warehouse_list/create_warehouse')
        ) {
            $this->removeButton('add');
        }
        
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')
                && Magestore_Coresuccess_Model_Service::permissionService()
                        ->checkPermission('admin/inventorysuccess/stocklisting/warehouse_location_mapping')) {
            $this->addButton('location_mapping', array(
                'class' => '',
                'label' => Mage::helper('inventorysuccess')->__('Mapping Locations - Warehouses'),
                'onclick'   => 'setLocation(\''. $this->getUrl('*/inventorysuccess_warehouse_location/mapping') .'\')',
            ), 0, -1);
        }
    }
}