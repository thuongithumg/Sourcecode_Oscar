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

class Magestore_InventorySuccess_Block_Adminhtml_StockMovement_Grid_Column_Renderer_Sku 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $url = $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
        return '<a class="view_product" href="' . $url . '">' . $this->_getValue($row) . '</a>';
    }
}
