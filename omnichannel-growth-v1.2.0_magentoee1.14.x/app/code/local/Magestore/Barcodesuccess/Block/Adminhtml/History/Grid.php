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
class Magestore_Barcodesuccess_Block_Adminhtml_History_Grid extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_History_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('barcodesuccess/history')->getCollection();
        $collection->getSelect()->joinLeft(
            array('admin' => $collection->getTable('admin/user')),
            'main_table.created_by = admin.user_id',
            array('username')
        );
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
        $this->addColumn('history_id', array(
            'header' => Mage::helper('barcodesuccess')->__('ID'),
            'type'   => 'text',
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'history_id',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('barcodesuccess')->__('Date'),
            'type'   => 'datetime',
            'align'  => 'left',
            'width'  => '250px',
            'index'  => 'created_at',
        ));

        $this->addColumn('username', array(
            'header' => Mage::helper('barcodesuccess')->__('User'),
            'type'   => 'text',
            'width'  => '150px',
            'index'  => 'username',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('barcodesuccess')->__('Barcode Qty'),
            'type'   => 'number',
            'width'  => '100px',
            'index'  => 'total_qty',
        ));

        $this->addColumn('type', array(
            'header'  => Mage::helper('barcodesuccess')->__('Type'),
            'align'   => 'center',
            'width'   => '100px',
            'type'    => 'options',
            'index'   => 'type',
            'options' => Magestore_Coresuccess_Model_Service::barcodeHistoryService()->getOptionHash(),
        ));


        $this->addColumn('reason', array(
            'header' => Mage::helper('barcodesuccess')->__('Reason'),
            'type'   => 'text',
            'width'  => '550px',
            'index'  => 'reason',
        ));

        $this->addColumn('action',
                         array(
                             'header'    => Mage::helper('barcodesuccess')->__('Detail'),
                             'width'     => '100',
                             'type'      => 'action',
                             'getter'    => 'getId',
                             'actions'   => array(
                                 array(
                                     'caption' => Mage::helper('barcodesuccess')->__('View'),
                                     'url'     => array('base' => '*/*/view'),
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
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl( $row )
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
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