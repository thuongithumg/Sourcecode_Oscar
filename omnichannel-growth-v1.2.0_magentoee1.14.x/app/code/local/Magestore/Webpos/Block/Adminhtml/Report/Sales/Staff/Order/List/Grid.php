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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Webpos_Block_Adminhtml_Report_Sales_Staff_Order_List_Grid extends Magestore_Webpos_Block_Adminhtml_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    public function getResourceCollectionName()
    {
        return 'webpos/sales_staff_order_list_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('user.display_name', array(
            'header'    => Mage::helper('sales')->__('Staff'),
            'index'     => 'user.display_name',
            'type'      => 'text',
            'total'     => '',
            'sortable'  => false
        ));

        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Increment Id'),
            'index'     => 'increment_id',
            'type'      => 'text',
            'total'     => '',
            'sortable'  => false
        ));

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);
        $this->addColumn('base_total_paid', array(
            'header'    => Mage::helper('sales')->__('Sales Total'),
            'index'     => 'base_total_paid',
            'type'      => 'currency',
            'total'     => 'sum',
            'sortable'  => false,
            'currency_code' => $currencyCode,
            'rate'          => $rate
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Purchased On'),
            'index'     => 'created_at',
            'type'      => 'date',
            'total'     => '',
            'renderer'  => 'Magestore_Webpos_Block_Adminhtml_Report_Renderer_PurchasedOn',
            'sortable'  => false
        ));
        $this->addColumn(Mage::getResourceModel('webpos/collection')->getTable('sales/order').'.status', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => Mage::getResourceModel('webpos/collection')->getTable('sales/order').'.status',
            'type'      => 'text',
            'total'     => '',
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);



        $this->addExportType('*/*/exportSalesCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
