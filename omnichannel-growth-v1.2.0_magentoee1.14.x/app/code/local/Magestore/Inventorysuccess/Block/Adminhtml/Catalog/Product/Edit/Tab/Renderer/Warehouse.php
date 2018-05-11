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
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Renderer_Warehouse 
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
        $warehouseOptions = Magestore_Coresuccess_Model_Service::warehouseOptionService()->getOptionHash();        
        if(count($warehouseOptions)) {
            foreach($warehouseOptions as $option) {
                $selectOptions .= '<option value="'.$option['value'].'" #{warehouse_selected_'.$option['value'].'}>'.$option['label'].'</option>';
            }
        }
                
        return '<select name="' . $this->getInputName() . '" value="#{' . $this->getColumnName() . '}" #{warehouse_disabled}' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '>' .
                $selectOptions.'</select>' .
                '<input type="hidden" name="'.str_replace('[warehouse]', '[warehouse_id]', $this->getInputName()).'" value="#{' . $this->getColumnName() . '}"/>';
    }
}