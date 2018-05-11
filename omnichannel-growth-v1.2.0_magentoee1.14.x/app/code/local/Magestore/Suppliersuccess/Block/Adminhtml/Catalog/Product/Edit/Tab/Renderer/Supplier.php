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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Renderer_Supplier
    extends Mage_Adminhtml_Block_Template
{
    /**
     * 
     * @return string
     */
    protected function _toHtml()
    {
        $column = $this->getColumn();
        $selectOptions = '';
        $options = Magestore_Coresuccess_Model_Service::supplierService()->getSupplierOptionHash();        
        if(count($options)) {
            foreach($options as $option) {
                $selectOptions .= '<option value="'.$option['value'].'" #{supplier_selected_'.$option['value'].'}>'.$option['label'].'</option>';
            }
        }
                
        return '<select name="' . $this->getInputName() . '" value="#{' . $this->getColumnName() . '}" #{supplier_disabled}' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '>' .
                $selectOptions.'</select>' .
                '<input type="hidden" name="'.str_replace('[supplier]', '[supplier_id]', $this->getInputName()).'" value="#{' . $this->getColumnName() . '}"/>';
    }
}