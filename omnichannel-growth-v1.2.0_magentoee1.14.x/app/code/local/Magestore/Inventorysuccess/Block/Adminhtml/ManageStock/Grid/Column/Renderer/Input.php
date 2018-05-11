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

class Magestore_InventorySuccess_Block_Adminhtml_ManageStock_Grid_Column_Renderer_Input
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $html = '<input type="text" ';
        $html .= 'name="'.$this->getColumn()->getName().'" ';
        $html .= 'value="' . $row->getStocktakingReason() . '"';
        $html .= 'style="width:160px !important"';
        $html .= 'class="input-text"/>';
        return $html;
    }

    /**
     * Render column for export
     *
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        return $row->getProductLocation();
    }
}
