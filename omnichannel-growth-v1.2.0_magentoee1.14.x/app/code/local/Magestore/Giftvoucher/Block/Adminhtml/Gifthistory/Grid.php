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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Adminhtml Giftvoucher Gifthistory Grid Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Adminhtml_Gifthistory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Gifthistory_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gifthistoryGrid');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('giftvoucher/history')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_id', array(
            'header' => Mage::helper('giftvoucher')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'history_id',
        ));
        $this->addColumn('created_at', array(
            'header' => Mage::helper('giftvoucher')->__('Created Time'),
            'align' => 'left',
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('giftvoucher')->__('Action'),
            'align' => 'left',
            'index' => 'action',
            'type' => 'options',
            'options' => Mage::getSingleton('giftvoucher/actions')->getOptionArray(),
        ));

        $this->addColumn('amount', array(
            'header' => Mage::helper('giftvoucher')->__('Value'),
            'align' => 'left',
            'index' => 'amount',
            'type' => 'currency',
            'currency' => 'currency',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('giftvoucher')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('giftvoucher/status')->getOptionArray(),
        ));

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('giftvoucher')->__('Order'),
            'align' => 'left',
            'index' => 'order_increment_id',
        ));

        $this->addColumn('comments', array(
            'header' => Mage::helper('giftvoucher')->__('Comment'),
            'align' => 'left',
            'index' => 'comments',
        ));

        $this->addColumn('extra_content', array(
            'header' => Mage::helper('giftvoucher')->__('Action Created by'),
            'align' => 'left',
            'index' => 'extra_content',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('giftvoucher')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('giftvoucher')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('history_id');
        $this->getMassactionBlock()->setFormFieldName('gifthistory');
        return $this;
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

}
