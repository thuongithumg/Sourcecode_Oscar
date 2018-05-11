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
 * Adjuststock Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('lowStockRuleGrid');
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventorysuccess/lowStockNotification_rule')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id',
            array(
                'header' => Mage::helper('inventorysuccess')->__('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'rule_id',
            ));

        $this->addColumn('rule_name',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Rule'),
                'align' => 'left',
                'index' => 'rule_name',
            ));

        $this->addColumn('description',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Description'),
                'index' => 'description',
            ));

        $this->addColumn('from_date',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Start'),
                'index' => 'from_date',
                //'gmtoffset' => true,
                'type' => 'date',
                'width' => '80px',
                'filter_condition_callback' => array($this, '_filterDateCallback')
            ));

        $this->addColumn('to_date',
            array(
                'header' => Mage::helper('inventorysuccess')->__('End'),
                'index' => 'to_date',
                //'gmtoffset' => true,
                'type' => 'date',
                'width' => '80px',
                'filter_condition_callback' => array($this, '_filterDateCallback')
            ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_Apply $sourceApply */
        $sourceApply = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_apply');
        $this->addColumn('apply',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Apply'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'apply',
                'type' => 'options',
                'options' => $sourceApply->toOptionArray(),
            ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_Status $sourceStatus */
        $sourceStatus = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_status');
        $this->addColumn('status',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Status'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'status',
                'type' => 'options',
                'options' => $sourceStatus->toOptionArray(),
            ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('inventorysuccess')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventorysuccess')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventorysuccess')->__('XML'));

        return parent::_prepareColumns();
    }


    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterDateCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if ($column->getType() == 'date') {
            if (isset($value['from'])) {
                $collection->addFieldToFilter(
                    $column->getIndex(),
                    array(
                        'gteq' => $value['from']->set(
                                $value['orig_from'], Zend_Date::DATE_SHORT, $value['locale']
                            )->toString('Y-M-d') . ' 00:00:00'
                    )
                );
            }
            if (isset($value['to'])) {
                $collection->addFieldToFilter(
                    $column->getIndex(),
                    array(
                        'lteq' => $value['to']->set(
                                $value['orig_to'], Zend_Date::DATE_SHORT, $value['locale']
                            )->toString('Y-M-d') . ' 23:59:59'
                    )
                );
            }
        }
        return $collection;
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Inventorysuccess_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('inventorysuccess_id');
        $this->getMassactionBlock()->setFormFieldName('rule_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('inventorysuccess')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('inventorysuccess')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('inventorysuccess/lowStockNotification_source_rule_status')->getOptionHash();

        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('inventorysuccess')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('inventorysuccess')->__('Status'),
                    'values' => $statuses
                ))
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}