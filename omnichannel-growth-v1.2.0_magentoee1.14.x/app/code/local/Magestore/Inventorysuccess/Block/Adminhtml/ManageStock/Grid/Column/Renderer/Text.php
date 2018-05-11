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

class Magestore_InventorySuccess_Block_Adminhtml_ManageStock_Grid_Column_Renderer_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($this->getColumn()->getEditable()) {
            $result = '<div class="admin__grid-control">';
            $result .= $this->getColumn()->getEditOnly() ? ''
                : '<span class="admin__grid-control-value">' . $this->_getValue($row) . '</span>';

            return $result . $this->_getInputHiddenValueElement($row) .$this->_getInputValueElement($row) . '</div>' ;
        }
        return $this->_getValue($row);
    }
    
    
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */    
    public function renderExport(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    /**
     * @param Object $row
     * @return string
     */
    public function _getInputValueElement(Varien_Object $row)
    {
        return '<input type="text" style="display: none" class="input-text ' .
        $this->getColumn()->getValidateClass() .
        '" name="' .
        $this->getColumn()->getId() .
        '" value="' .
        $this->_getInputValue(
            $row
        ) . '"/>';
    }

    /**
     * @param Object $row
     * @return string
     */
    public function _getInputHiddenValueElement(Varien_Object $row)
    {
        return '<input type="text" style="display: none" class="input-text ' .
        $this->getColumn()->getValidateClass() .
        '" name="' .
        $this->getColumn()->getId() . '_old' .
        '" value="' .
        $this->_getInputValue(
            $row
        ) . '"/>';
    }
}
