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

class Magestore_Webpos_Block_Adminhtml_Pos_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('posGrid');
        $this->setDefaultSort('pos_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('webpos/pos')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('pos_id', array(
            'header' => Mage::helper('webpos')->__('Pos ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'pos_id',
        ));

        $this->addColumn('pos_name', array(
            'header'    => Mage::helper('webpos')->__('Pos Name'),
            'align'     =>'left',
            'index'     => 'pos_name',
        ));

        $this->addColumn('location_id', array(
            'header' => Mage::helper('webpos')->__('Location'),
            'align' => 'left',
            'index' => 'location_id',
            'renderer'  => 'Magestore_Webpos_Block_Adminhtml_Pos_Renderer_Location',
            'type' => 'options',
            'options' => $this->getLocations()
        ));
        $this->addColumn('user_id', array(
            'header' => Mage::helper('webpos')->__('Current Staff'),
            'align' => 'left',
            'index' => 'user_id',
            'type' => 'options',
            'options' => Mage::getModel('webpos/user')->toOptionArray()
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
        $this->setMassactionIdField('pos_id');
        $this->getMassactionBlock()->setFormFieldName('webpos');

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

    /**
     * @return array
     */
    public function getLocations(){
        $locations = array();
        $collection = Mage::getModel('webpos/userlocation')->getCollection();
        if($collection->getSize() > 0){
            foreach ($collection as $location){
                $locations[$location->getId()] = $location->getDisplayName();
            }
        }
        return $locations;
    }
}