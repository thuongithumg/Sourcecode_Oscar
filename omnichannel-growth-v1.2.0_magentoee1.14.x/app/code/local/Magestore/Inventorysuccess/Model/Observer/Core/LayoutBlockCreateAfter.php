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
 * Class Magestore_Inventorysuccess_Model_Observer_Core_LayoutBlockCreateAfter
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Observer_Core_LayoutBlockCreateAfter
{
    /**
     *
     * @param type $observer
     */
    public function execute($observer)
    {
        /** @var Mage_Adminhtml_Block_Template $block */
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid ||
            $block->getId() == 'sales_order_grid'
        ) {
            $this->addWarehouseIdToOrderGrid($block);
            return $this;
        }
    }

    /**
     * Add warehouse id column to order grid
     *
     * @param Mage_Adminhtml_Block_Sales_Order_Grid $block
     * @return $this
     */
    protected function addWarehouseIdToOrderGrid($block)
    {
        $block->addColumnAfter('warehouse_id', array(
            'header' => Mage::helper('inventorysuccess')->__('Warehouse'),
            'index' => 'warehouse_id',
            'name' => 'warehouse_id',
            'type' => 'options',
            'value' => array(0 => '2'),
            'options' => Mage::getModel('inventorysuccess/warehouse_options_warehouse')->getOptionArray()
        ), 'status');
        return $this;
    }
}