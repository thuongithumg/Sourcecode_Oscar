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

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Block_Adminhtml_Zreport_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('zreportGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('webpos/shift')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('webpos')->__('ID'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'entity_id',
        ));
        $this->addColumn('shift_id', array(
            'header' => Mage::helper('webpos')->__('Shift ID'),
            'align' => 'left',
            'index' => 'shift_id',
        ));
        $this->addColumn('staff_id', array(
            'header' => Mage::helper('webpos')->__('Staff'),
            'align' => 'left',
            'index' => 'staff_id',
            'renderer'  => 'Magestore_Webpos_Block_Adminhtml_Zreport_Renderer_Staff',
            'type' => 'options',
            'options' => $this->getStaff()
        ));
        $this->addColumn('pos_id', array(
            'header' => Mage::helper('webpos')->__('Pos'),
            'align' => 'left',
            'index' => 'pos_id',
            'renderer'  => 'Magestore_Webpos_Block_Adminhtml_Zreport_Renderer_Pos',
            'type' => 'options',
            'options' => $this->getPos()
        ));
        $this->addColumn('opened_at', array(
            'header' => Mage::helper('webpos')->__('Open from'),
            'align' => 'left',
            'index' => 'opened_at',
            'type' => 'datetime'
        ));
        $this->addColumn('closed_at', array(
            'header' => Mage::helper('webpos')->__('Closed at'),
            'align' => 'left',
            'index' => 'closed_at',
            'type' => 'datetime'
        ));
        $this->addColumn('float_amount', array(
            'header' => Mage::helper('webpos')->__('Opening amount'),
            'align' => 'left',
            'index' => 'float_amount',
            'type' => 'currency',
            'currency' => 'report_currency_code'
        ));
        $this->addColumn('closed_amount', array(
            'header' => Mage::helper('webpos')->__('Closed amount'),
            'align' => 'left',
            'index' => 'closed_amount',
            'type' => 'currency',
            'currency' => 'report_currency_code'
        ));
        $this->addColumn('cash_left', array(
            'header' => Mage::helper('webpos')->__('Cash left'),
            'align' => 'left',
            'index' => 'cash_left',
            'type' => 'currency',
            'currency' => 'report_currency_code'
        ));
        $this->addColumn('note', array(
            'header' => Mage::helper('webpos')->__('Note'),
            'align' => 'left',
            'index' => 'note',
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('webpos')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('webpos')->__('Print'),
                    'url' => array('base' => '*/*/print'),
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

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {

    }

    /**
     * @return array
     */
    public function getStaff(){
        $locations = array();
        $collection = Mage::getModel('webpos/user')->getCollection();
        if($collection->getSize() > 0){
            foreach ($collection as $user){
                $locations[$user->getId()] = ($user->getDisplayName())?$user->getDisplayName():$user->getUsername();
            }
        }
        return $locations;
    }

    /**
     * @return array
     */
    public function getPos(){
        $locations = array();
        $collection = Mage::getModel('webpos/pos')->getCollection();
        if($collection->getSize() > 0){
            foreach ($collection as $pos){
                $locations[$pos->getId()] = $pos->getPosName();
            }
        }
        return $locations;
    }
}
