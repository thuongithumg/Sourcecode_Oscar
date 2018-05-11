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
 * Marketingautomation Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Block_Adminhtml_User_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('userGrid');
        $this->setDefaultSort('user_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Marketingautomation_Block_Adminhtml_Marketingautomation_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('webpos/user')->getCollection();
        $collection->addFieldToSelect('*');
        $collection->getSelect()->joinLeft(array('role_table' => Mage::getModel('core/resource')->getTableName('webpos/role')), 'main_table.role_id=role_table.role_id', array('role_table.display_name as role_table.display_name'))
            ->group('main_table.user_id');
        $collection->getSelectCountSql();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Marketingautomation_Block_Adminhtml_Marketingautomation_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('user_id', array(
            'header' => Mage::helper('webpos')->__('ID'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'user_id',
        ));
        $this->addColumn('username', array(
            'header' => Mage::helper('webpos')->__('User Name'),
            'align' => 'left',
            'index' => 'username',
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('webpos')->__('Email'),
            'align' => 'left',
            'index' => 'email',
        ));
        $this->addColumn('display_name', array(
            'header' => Mage::helper('webpos')->__('Display Name'),
            'align' => 'left',
            'index' => 'display_name',
            'filter_condition_callback' => array($this, '_filterDisplayNameConditionCallback')
        ));
        $this->addColumn('location', array(
            'header' => Mage::helper('webpos')->__('Location'),
            'align' => 'left',
            'index' => 'location_table.display_name',
            'renderer' => 'Magestore_Webpos_Block_Adminhtml_User_Renderer_Userlocation',
            'filter' => false,
        ));
        $this->addColumn('role', array(
            'header' => Mage::helper('webpos')->__('Role'),
            'align' => 'left',
            'index' => 'role_table.display_name',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('webpos')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enable',
                2 => 'Disable',
            ),
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('webpos')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('webpos')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('webpos')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('webpos')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _filterDisplayNameConditionCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        if (empty($value)) {
            $this->getCollection()->getSelect()->where(
                "main_table.display_name IS NULL");
        }
        else {
            $this->getCollection()->getSelect()->where(
                "main_table.display_name LIKE '%".$value."%'");
        }

        return $this;
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Marketingautomation_Block_Adminhtml_Marketingautomation_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('user_id');
        $this->getMassactionBlock()->setFormFieldName('user');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('webpos')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('webpos')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('webpos/status')->getOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('webpos')->__('Change status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('webpos')->__('Status'),
                    'values'=> $statuses
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
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
