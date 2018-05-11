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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('supplierGrid');
        $this->setDefaultSort('supplier_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Suppliersuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('suppliersuccess/supplier')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Suppliersuccess_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('supplier_id', array(
            'header'    => Mage::helper('suppliersuccess')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'supplier_id',
        ));

        $this->addColumn('supplier_name', array(
            'header'    => Mage::helper('suppliersuccess')->__('Supplier'),
            'align'     =>'left',
            'index'     => 'supplier_name',
        ));

        $this->addColumn('supplier_code', array(
            'header'    => Mage::helper('suppliersuccess')->__('Supplier Code'),
            'align'     =>'left',
            'index'     => 'supplier_code',
        ));

        $this->addColumn('contact_email', array(
            'header'    => Mage::helper('suppliersuccess')->__('Contact Email'),
            'align'     =>'left',
            'index'     => 'contact_email',
        ));        
        
        /**
         * allow to add more columns by other extensions
         */
        Mage::dispatchEvent('suppliersuccess_supplier_grid_add_column', array('grid' => $this));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('suppliersuccess')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'        => 'options',
            'options'     => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('suppliersuccess')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('suppliersuccess')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('suppliersuccess')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('suppliersuccess')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Suppliersuccess_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('supplier_id');
        $this->getMassactionBlock()->setFormFieldName('supplier');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('suppliersuccess')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('suppliersuccess')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('suppliersuccess/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('suppliersuccess')->__('Change status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('suppliersuccess')->__('Status'),
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
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}