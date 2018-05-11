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
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Field_StockSummary
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';        
        $stockSummary = $element->getValue();
        if(!isset($stockSummary['available_qty'])
                || !isset($stockSummary['qty_to_ship'])
                || !isset($stockSummary['total_qty'])) {
            return $html;
        }
        $orderHistoryView = '';
        if($stockSummary['qty_to_ship'] > 0) {
            $orderHistoryView = '(<a href="javascript:void(0);" data-toggle="modal" data-target="#inventory_catalog_product_order_history">'. 
                                $this->__('view orders').'</a>)';
        }
        $html .= '<div class="fieldset"><table cellspacing="0" class="form-list"><tbody>'; 
        $html .= '<tr>';
        $html .= '<td class="label"><label>'. $this->__('Available qty') .'</label></td>';
        $html .= '<td class="value"><strong>'. $stockSummary['available_qty'] .'</strong><td/>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="label"><label>'. $this->__('Qty to ship') .'</label></td>';
        $html .= '<td class="value"><strong>'. $stockSummary['qty_to_ship'] .'</strong> '.$orderHistoryView.'<td/>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="label"><label>'. $this->__('Qty in warehouse') .'</label></td>';
        $html .= '<td class="value"><strong>'. $stockSummary['total_qty'] .'</strong><td/>';
        $html .= '</tr>';
        $html .= '</tbody></table></div>';
        return $html;
    }
}
