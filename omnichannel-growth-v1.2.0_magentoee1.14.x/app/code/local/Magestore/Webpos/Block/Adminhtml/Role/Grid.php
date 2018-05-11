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
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:58 SA
 */

class Magestore_Webpos_Block_Adminhtml_Role_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('roleGrid');
        $this->setDefaultSort('role_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('webpos/role')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('role_id', array(
            'header' => Mage::helper('webpos')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'role_id',
        ));

        $this->addColumn('display_name', array(
            'header'    => Mage::helper('webpos')->__('Display Name'),
            'align'     =>'left',
            'index'     => 'display_name',
        ));

        $this->addColumn('description', array(
            'header'    => Mage::helper('webpos')->__('Description'),
            'align'     =>'left',
            'index'     => 'description',
        ));

        $this->addColumn('active', array(
            'header'    => Mage::helper('webpos')->__('Active'),
            'align'     =>'left',
            'index'     => 'active',
            'type'      => 'options',
            'options' => Mage::getSingleton('webpos/source_adminhtml_status')->getOptionArray()
        ));


        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('webpos')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('webpos')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
            ));


        $this->addExportType('*/*/exportCsv', Mage::helper('webpos')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('webpos')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('role_id');
        $this->getMassactionBlock()->setFormFieldName('webpos');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('webpos')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('webpos')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status_id', array(
            'label' => Mage::helper('webpos')->__('Change Status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status_id',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('webpos')->__('Change Status'),
                    'values' =>Mage::getModel('webpos/source_adminhtml_status')->getOptionArray()
                ))
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}