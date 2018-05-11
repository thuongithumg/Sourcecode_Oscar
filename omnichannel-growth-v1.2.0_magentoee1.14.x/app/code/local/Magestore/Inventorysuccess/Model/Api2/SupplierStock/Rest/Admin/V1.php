<?php

/**
 * Class Magestore_Inventorysuccess_Model_Api2_WarehouseStock_Rest_Admin_V1
 */
class Magestore_Inventorysuccess_Model_Api2_SupplierStock_Rest_Admin_V1 extends
    Magestore_Inventorysuccess_Model_Api2_Abstract
{
    /**
     *
     */
    const ACTION_TYPE_GETLIST_UPDATE = 'getlist_update';
    /**
     *
     */
    public function dispatch()
    {
        switch ( $this->getActionType() ) {
            case self::ACTION_TYPE_GETLIST_UPDATE:
                /** PUT = update */
                if ( $this->getRequest()->isPut() ) {
                    $data   = $this->getRequest()->getBodyParams();
                    $result = $this->updateSupplierStock($data);
                }
                break;

            default:
                $result = array();
        }
        $this->_render($result);
        $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
    }
    /**
     * @param $datas
     */
    public function updateSupplierStock($datas){
        if($this->checkSupplierModule()){
            foreach($datas as $data){
                $supplierId = $data['supplier_id'];
                $supplier = Mage::getModel('suppliersuccess/supplier')->load($supplierId);
                unset($data['supplier_id']);
                Magestore_Coresuccess_Model_Service::supplierService()->setProductsToSupplier($supplier, $data , true);
            }
        }
    }
    /**
     * @return bool
     */
    public function checkSupplierModule(){
        $result = false;
           if (Mage::helper('core')->isModuleEnabled('Magestore_Suppliersuccess')) {
                    $result = true;
           }
        return $result;
    }
}
