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
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Column_Renderer_PurchaseOrder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
        if($row->getData('po_id_group')){
            $ids = explode ( "," , $row->getData('po_id_group'));
            $po_order = Mage::getModel('purchaseordersuccess/purchaseorder')->getCollection()
                ->addFieldToFilter('purchase_order_id',array('in'=> $ids));
            $content = '';
            if($po_order)
                foreach($po_order as $value){
                    if(($actionExport == 'exportXml') || ($actionExport == 'exportCsv')){
                        $content .= $value->getData('purchase_code')."\r\n";
                    }else{
                        $Url = Mage::helper('adminhtml')->getUrl('adminhtml/purchaseordersuccess_purchaseorder/view/id/'.$value->getData('purchase_order_id'));
                        $content .= "<a href=".$Url." >".$value->getData('purchase_code')."<a/>" . "<br/>";
                    }
                }
            return $content;
        }
    }
}