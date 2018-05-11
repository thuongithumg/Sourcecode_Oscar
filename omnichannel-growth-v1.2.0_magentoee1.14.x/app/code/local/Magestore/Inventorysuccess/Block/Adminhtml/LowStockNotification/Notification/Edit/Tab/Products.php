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

class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Notification_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {
        $id = $this->getRequest()->getParam('id');
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Notification_Product_Collection $collection */
        $collection = Mage::getResourceModel('inventorysuccess/lowStockNotification_notification_product_collection');
        $collection->addFieldToFilter('notification_id', $id);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @throws \Exception
     */
    protected function _prepareColumns() {
        $notificationService = Magestore_Coresuccess_Model_Service::notificationService();
        $notification = $notificationService->getCurrentNotification();

        $this->addColumn(
            'product_id',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Id'),
                'align' => 'left',
                'index' => 'product_id',
                'width' => '50px'
            )
        );

        $this->addColumn(
            'product_sku',
            array(
                'header' => Mage::helper('inventorysuccess')->__('SKU'),
                'index' => 'product_sku'
            )
        );

        $this->addColumn(
            'product_name',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Name'),
                'index' => 'product_name'
            )
        );

        $this->addColumn(
            'current_qty',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Current Qty'),
                'index' => 'current_qty',
                'type' => 'number'
            )
        );
        if ($notification->getData('lowstock_threshold_type') == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY) {
            $this->addColumn(
                'sold_per_day',
                array(
                    'header' => Mage::helper('inventorysuccess')->__('Qty. Sold/day'),
                    'type' => 'number',
                    'index' => 'sold_per_day',
                )
            );

            $this->addColumn(
                'total_sold',
                array(
                    'header' => Mage::helper('inventorysuccess')->__('Total Sold'),
                    'type' => 'number',
                    'index' => 'total_sold'
                )
            );

            $this->addColumn(
                'availability_days',
                array(
                    'header' => Mage::helper('inventorysuccess')->__('Availability Days'),
                    'type' => 'number',
                    'index' => 'availability_days'
                )
            );

            $this->addColumn(
                'availability_date',
                array(
                    'header' => Mage::helper('inventorysuccess')->__('Available Date'),
                    'type' => 'date',
                    'index' => 'availability_date'
                )
            );
        }

        $this->addColumn(
            'qty_needs_more',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Qty Needed'),
                'type' => 'number',
                'index' => 'qty_needs_more',
                'filter' => false,
                'sortable' => false,
                'width' => '50px',
                'renderer' => 'inventorysuccess/adminhtml_lowStockNotification_notification_edit_renderer_qtyNeedsMore',
            )
        );
        $this->addExportType('*/*/exportListProductsCsv', Mage::helper('inventorysuccess')->__('CSV'));
        $this->addExportType('*/*/exportListProductsXml', Mage::helper('inventorysuccess')->__('Excel XML'));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/productgrid', array(
                    '_current' => true,
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->getRequest()->getParam('store')
                ));
    }

    public function getRowUrl($row) {
        return false;
    }
}
