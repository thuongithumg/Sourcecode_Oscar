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


class Magestore_Webpos_Block_Adminhtml_Cashdenomination_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('denominationGrid');
        $this->setDefaultSort('denomination_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('webpos/denomination')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('denomination_id', array(
            'header' => Mage::helper('webpos')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'type'  => 'number',
            'index' => 'denomination_id',
        ));

        $this->addColumn('denomination_name', array(
            'header'    => Mage::helper('webpos')->__('Name'),
            'align'     =>'left',
            'index'     => 'denomination_name',
        ));

        $this->addColumn('denomination_value', array(
            'header'    => Mage::helper('webpos')->__('Value'),
            'align'     =>'left',
            'type'  => 'number',
            'index'     => 'denomination_value',
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('webpos')->__('Sort Order'),
            'align'     =>'left',
            'type'  => 'number',
            'index'     => 'sort_order',
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
        $this->setMassactionIdField('denomination_id');
        $this->getMassactionBlock()->setFormFieldName('denomination');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('webpos')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('webpos')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}