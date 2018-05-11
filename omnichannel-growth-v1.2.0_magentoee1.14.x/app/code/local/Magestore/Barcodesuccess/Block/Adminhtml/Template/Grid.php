<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_Template_Grid extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Template_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('templateGrid');
        $this->setDefaultSort('template_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('barcodesuccess/template')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('template_id', array(
            'header' => Mage::helper('barcodesuccess')->__('ID'),
            'type'   => 'number',
            'width'  => '50px',
            'index'  => 'template_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('barcodesuccess')->__('Name'),
            'width'  => '250px',
            'index'  => 'name',
        ));

        $this->addColumn('type', array(
            'header'  => Mage::helper('barcodesuccess')->__('Type'),
            'type'    => 'options',
            'options' => Magestore_Barcodesuccess_Model_Source_Template_Type::toOptionHash(),
            'width'   => '150px',
            'index'   => 'type',
        ));

        $this->addColumn('status', array(
            'header'  => Mage::helper('barcodesuccess')->__('Status'),
            'type'    => 'options',
            'options' => Magestore_Barcodesuccess_Model_Source_Template_Status::toOptionHash(),
            'width'   => '100px',
            'index'   => 'status',
        ));
        $this->addColumn('action',
                         array(
                             'header'    => Mage::helper('barcodesuccess')->__('Detail'),
                             'width'     => '100',
                             'type'      => 'action',
                             'getter'    => 'getId',
                             'actions'   => array(
                                 array(
                                     'caption' => Mage::helper('barcodesuccess')->__('Edit'),
                                     'url'     => array('base' => '*/*/edit'),
                                     'field'   => 'id',
                                 ),
                             ),
                             'filter'    => false,
                             'sortable'  => false,
                             'index'     => 'stores',
                             'is_system' => true,
                         ));

        $this->addExportType('*/*/exportCsv', Mage::helper('barcodesuccess')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('barcodesuccess')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('template_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('barcodesuccess')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('barcodesuccess')->__('Are you sure?')
        ));

        $statuses = Magestore_Barcodesuccess_Model_Source_Template_Status::toOptionHash();

        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('barcodesuccess')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('barcodesuccess')->__('Status'),
                    'values' => $statuses
                ))
        ));
        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl( $row )
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }


    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array(
            '_current' => true,
        ));
    }
}