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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('lowStockNotificationGrid');
        $this->setDefaultSort('notification_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Notification_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventorysuccess/lowStockNotification_notification')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Notification_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('notification_id',
            array(
                'header'    => Mage::helper('inventorysuccess')->__('ID'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'notification_id',
        ));

        $this->addColumn('created_at',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Created At'),
                'index' => 'created_at',
                'gmtoffset' => true,
                'type'=>'datetime',
                'width' => '200px'
        ));

        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Source_Notification_UpdateType $sourceUpdateType */
        $sourceUpdateType = Mage::getSingleton('inventorysuccess/lowStockNotification_source_notification_updateType');
        $this->addColumn('update_type',
            array(
                'header'    => Mage::helper('inventorysuccess')->__('Update Type'),
                'align'     => 'left',
                'width'     => '120px',
                'index'     => 'update_type',
                'type'      => 'options',
                'options'   => $sourceUpdateType->toOptionArray(),
        ));

        $this->addColumn('warning_message',
            array(
                'header'    => Mage::helper('inventorysuccess')->__('Warning Message'),
                'align'     => 'left',
                'index'     => 'warning_message',
                'type'      => 'text'
        ));

        $this->addColumn('notifier_emails',
            array(
                'header'    => Mage::helper('inventorysuccess')->__('Notification recipients'),
                'align'     => 'left',
                'index'     => 'notifier_emails',
                'type'      => 'text'
            ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('inventorysuccess')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('inventorysuccess')->__('View'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        return parent::_prepareColumns();
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