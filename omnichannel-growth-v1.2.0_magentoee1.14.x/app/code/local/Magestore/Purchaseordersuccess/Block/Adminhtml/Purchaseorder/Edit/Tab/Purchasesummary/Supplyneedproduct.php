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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Supplyneedproduct
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/edit/tab/purchase_summary/supply_need_product.phtml';

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Supplyneedproduct_Grid
     */
    protected $blockGrid;

    /**
     * @var Magestore_Inventorysuccess_Block_Adminhtml_SupplyNeeds_Edit_Tab_Forecast
     */
    protected $form;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->purchaseOrder = Mage::registry('current_purchase_order');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess') &&
            $this->purchaseOrder->canAddProduct()
        )
            return parent::_toHtml();
        else
            return '';
    }

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Supplyneedproduct_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_supplyneedproduct_grid',
                'purchaseorder.purchasesummary.lowstockproduct.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getJsParentObjectName()
    {
        return $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_grid')
            ->getJsObjectName();
    }

    /**
     * Get list source sales period
     *
     * @return array
     */
    public function getSourceWarehouse()
    {
        return Mage::getSingleton('inventorysuccess/supplyNeeds_source_warehouse')
            ->toOptionArray();
    }

    /**
     * Get list source sales period
     *
     * @return array
     */
    public function getSourceSalesPeriod()
    {
        return Mage::getSingleton('inventorysuccess/supplyNeeds_source_salesPeriod')
            ->toOptionArray();
    }

    /**
     * Add Warehouse Field to Supply Need Modal
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addWarehouseField()
    {
        $sourceWarehouse = Mage::getSingleton('inventorysuccess/supplyNeeds_source_warehouse');
        $html = $this->addField('supply_need_warehouse_ids',
            'multiselect',
            array(
                'label' => $this->__('Warehouse(s)'),
                'name' => 'warehouse_ids',
                'values' => $sourceWarehouse->getOptionHash(),
                'style' => 'height: 100px',
                'required' => true,
            )
        );
        $sourceSalesPeriod = Mage::getSingleton('inventorysuccess/supplyNeeds_source_salesPeriod');
        $html .= $this->addField('supply_need_sales_period',
            'select',
            array(
                'label' => $this->__('Sales Record Period'),
                'name' => 'sales_period',
                'values' => $sourceSalesPeriod->getOptionHash(),
                'required' => true,
            )
        );
        $html .= $this->addField('supply_need_from_date',
            'date',
            array(
                'name' => 'from_date',
                'time' => false,
                'label' => $this->__('From'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
            )
        );
        $html .= $this->addField('supply_need_to_date',
            'date',
            array(
                'name' => 'to_date',
                'time' => false,
                'label' => $this->__('To'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
            )
        );
        $html .= $this->addField('supply_need_forecast_date_to',
            'date',
            array(
                'name' => 'forecast_date_to',
                'time' => false,
                'label' => $this->__('Forecast Supply Needs To'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
            )
        );
        return $html;
    }

    /**
     * Add child element
     *
     * if $after parameter is false - then element adds to end of collection
     * if $after parameter is null - then element adds to befin of collection
     * if $after parameter is string - then element adds after of the element with some id
     *
     * @param   string $elementId
     * @param   string $type
     * @param   array $config
     * @param   mixed $after
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after = false)
    {
        if (isset($this->_types[$type])) {
            $className = $this->_types[$type];
        } else {
            $className = 'Varien_Data_Form_Element_' . ucfirst(strtolower($type));
        }
        $element = new $className($config);
        $element->setId($elementId);
        $element->setForm($this->getForm());
        return $element->toHtml();
    }

    public function getForm()
    {
        if (!$this->form)
            $this->form = $this->getLayout()->createBlock('Magestore_Inventorysuccess_Block_Adminhtml_SupplyNeeds_Edit_Tab_Forecast');
        return $this->form;
    }
}