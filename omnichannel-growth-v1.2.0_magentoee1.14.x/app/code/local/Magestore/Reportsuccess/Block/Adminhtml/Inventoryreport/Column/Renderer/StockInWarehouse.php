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
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Column_Renderer_StockInWarehouse extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if($row->getData('product_id')){
            $qty = 0;
            $stocks = Mage::getModel('cataloginventory/stock_item')->getCollection()
                ->addFieldToFilter('product_id',$row->getData('product_id'))
            ->addFieldToFilter('stock_id',array('neq'=>array(1)));
            foreach($stocks as $stock){
                $qty += $stock->getTotalQty();
            }
            return $qty;
        }
    }
}