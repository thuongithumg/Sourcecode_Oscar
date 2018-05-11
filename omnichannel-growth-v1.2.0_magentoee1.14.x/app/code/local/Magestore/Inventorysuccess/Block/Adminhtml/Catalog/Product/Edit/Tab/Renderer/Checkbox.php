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
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Renderer_Checkbox
    extends Mage_Adminhtml_Block_Template
{
    /**
     *
     * @return string
     */
    protected function _toHtml()
    {
        $namePrefix = $this->getInputName();
        $inherit = 'check';
        $checkboxLabel = '';
        $defText = 'Force Edit';
        $html = '';

        $permission = $this->permission() ? '' : 'disabled="disabled"';

        $html.= '<input '.$permission.' name="'
            . $namePrefix . ' " type="checkbox" value="0"  class="checkbox config-inherit" '
            . $inherit . ' onclick="forceEdit(this, Element.previous(this.parentNode))" title="'
            . htmlspecialchars($defText) . '" /> ';
        return $html;
    }

    public function permission(){
        return Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/catalog_force_edit');
    }

}