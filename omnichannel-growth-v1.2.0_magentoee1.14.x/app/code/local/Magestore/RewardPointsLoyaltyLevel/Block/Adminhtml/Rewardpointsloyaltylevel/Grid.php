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
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsloyaltylevel Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('loyaltylevelGrid');
        $this->setDefaultSort('level_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('customer_group_id', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'customer_group_id',
        ));

        $this->addColumn('level_name', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Group Name'),
            'align' => 'left',
            'index' => 'level_name',
        ));
        
        $this->addColumn('condition_type', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Condition Type'),
            'index' => 'condition_type',
            'type' => 'options',
            'width' => '170px',
            'options' => Mage::getSingleton('rewardpointsloyaltylevel/system_config_source_conditiontype')->getOptionArray()
        ));
        
        $this->addColumn('condition_value', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Condition Value'),
            'index' => 'condition_value',
            'type' => 'number',
        ));
        $this->addColumn('demerit_points', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Exchange Point'),
            'index' => 'demerit_points',
            'type' => 'number',
        ));
        
        $this->addColumn('auto_join', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Auto Join'),
            'index' => 'auto_join',
            'type' => 'options',
            'width' => '50px',
            'options' => array(
                '0' => "No",
                '1' => "Yes"
            )
        ));
        
        $this->addColumn('retention_period', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Retention Period'),
            'width' => '150px',
            'index' => 'retention_period',
            'type' => 'number',
            'align' => 'right',
            'renderer'  => 'rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_renderer_level',
        ));
        $this->addColumn('priority', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Priority'),
            'width' => '50',
            'index' => 'priority',
            'type' => 'number',
            'align' => 'right',
        ));
//        $this->addColumn('created_time', array(
//            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Created Time'),
//            'align' => 'left',
//            'index' => 'to_date',
//            'format' => 'dd/MM/yyyy',
//            'type' => 'date',
//        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
        
        $this->addColumn('action', array(
            'header' => Mage::helper('rewardpointsloyaltylevel')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getCustomerGroupId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('rewardpointsloyaltylevel')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('rewardpointsloyaltylevel')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('rewardpointsloyaltylevel')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('rewardpointsloyaltylevel_id');
        $this->getMassactionBlock()->setFormFieldName('rewardpointsloyaltylevel');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('rewardpointsloyaltylevel')->__('Are you sure?')
        ));
        $statuses = Mage::getSingleton('rewardpointsloyaltylevel/system_config_source_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('rewardpointsloyaltylevel')->__('Status'),
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
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getCustomerGroupId()));
    }

}
