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
 * Warehouse Edit Block
 *
 * @category     Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'warehouse_id';
        $this->_controller = 'adminhtml_warehouse';
        $this->_blockGroup = 'inventorysuccess';

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $saveLabel = 'Save';

        $warehouseId = $this->getRequest()->getParam('id');
        if ($warehouseId) {
            $currentWarehouse = Mage::registry('current_warehouse');
            $warehouse = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
                ->getTotalQtysFromWarehouse($warehouseId);
            if ($warehouse->getSumTotalQty() <= 0 && $warehouse->getSumQtyToShip() <= 0 &&
                (Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
                    'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/delete_warehouse', $currentWarehouse
                ))
                
            ) {
                $this->_addButton('delete', array(
                    'class' => 'delete',
                    'label' => $this->__('Delete Warehouse'),
                    'onclick' => sprintf("deleteConfirm(
                        'Are you sure you want to delete this warehouse?', 
                        '%s'
                    )", $this->getUrl('*/*/delete', array('_current' => true))),
                ));
            }
            $this->removeButton('save');
            if (Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
                'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/edit_general_information',
                $currentWarehouse
            )
            )
                $this->_addButton('saveandcontinue', array(
                    'label' => Mage::helper('adminhtml')->__('Save General Information'),
                    'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                    'class' => 'save',
                ), -100);

        } else {
            $this->_updateButton('save', 'label', $this->__('Save'));
            $this->_addButton('saveandcontinue', array(
                'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                'class' => 'save',
            ), -100);
        }

    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        $currentWarehouse = Mage::registry('current_warehouse');
        if ($currentWarehouse && $currentWarehouse->getId()
        ) {
            return $this->__("View Warehouse (%s)",
                $this->escapeHtml($currentWarehouse->getWarehouseCode())
            );
        }
        return $this->__('Add New Warehouse');
    }
}