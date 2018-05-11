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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Column_Renderer_Supplier extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $actionExport =  $this->getRequest()->getActionName();
        if($row->getData('supplier_id')){
            $supplier = Mage::getModel('suppliersuccess/supplier')->getCollection()->addFieldToFilter('supplier_id',$row->getData('supplier_id'));
            $data = $supplier->getColumnValues('supplier_name');
            return "<span>".$data[0]."</span>" . "<br/>";
        }

        $product_id = $row->getEntityId();
        $supplier = Mage::getModel('suppliersuccess/supplier_product')->getCollection()->addFieldToFilter('product_id',$product_id);
        $supplier->getSelect()->joinLeft(
            array('supplier' => $supplier->getTable('suppliersuccess/supplier')), 'main_table.supplier_id = supplier.supplier_id',
            array('supplier_name')
        );
        $data = $supplier->getColumnValues('supplier_name');
        $content = '';
        if($data)
           foreach($data as $value){
               if($value) {
                   if(($actionExport == 'exportXml') || ($actionExport == 'exportCsv')){
                       $content .= $value."\r\n";
                   }else{
                       $content .= "<a>" . $value . "<a/>" . "<br/>";
                   }
               }
           }
        return $content;
    }

}