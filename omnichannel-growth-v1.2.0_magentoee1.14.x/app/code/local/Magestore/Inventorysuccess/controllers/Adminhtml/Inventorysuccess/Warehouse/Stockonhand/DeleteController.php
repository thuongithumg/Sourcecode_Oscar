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
 * Inventorysuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Warehouse_Stockonhand_DeleteController
    extends Mage_Adminhtml_Controller_Action
{    
    /**
     * Get stock on hand grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveAction(){
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $selectedProduct = json_decode($this->getRequest()->getParam('selected_items'), true);
        if($warehouseId && count($selectedProduct)>0){
            $result = Magestore_Coresuccess_Model_Service::warehouseStockService()
                ->removeProducts($warehouseId, array_keys($selectedProduct));
            if(count($result['success'])>0)
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('%s product(s) has been deleted from warehouse.', count($result['success']))
                );
            if(count($result['error'])>0)
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('%s product(s) has been failed to delete.', count($result['error']))
                );
        }
        return $this->_forward('grid');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed(
            'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand/delete_product'
        );
    }
}