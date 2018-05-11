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
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Column_Renderer_MacInline extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $_coreHelper = $this->helper('core');

        $reporttype =  $this->getRequest()->getParam('report');

        $actionExport =  $this->getRequest()->getActionName();

        if(isset($reporttype)){
            $type = Mage::helper('reportsuccess')->base64Decode($reporttype);
            if($type != 'stockonhand'){
                $value =  $row->getData($this->getColumn()->getIndex());
                return $_coreHelper->currency($value,true,false);
            }
        }
        if($row->getAction()){
            return;
        }
        $product_id = $row->getProductId();
        $value = $row->getData($this->getColumn()->getIndex());
        $value_show = $_coreHelper->currency($value,true,false);
        $showtransfer_url = Mage::helper('adminhtml')
            ->getUrl('adminhtml/inventoryreport_stockonhand/updateMac', array('product_id' => $product_id, 'qty' => $value_show));
        
        if(($actionExport == 'exportXml') || ($actionExport == 'exportCsv')){
            return $value_show;
        }

        return '<div id="sku-' . $product_id . '" height: 100%; >' . $value_show . '</div>
                <script type="text/javascript">
                     new Ajax.InPlaceEditor("sku-' . $product_id . '","' . $showtransfer_url . '",{okText:"",cancelText:"", highlightColor:"#9999FF"});
                </script>
                ';
    }
}