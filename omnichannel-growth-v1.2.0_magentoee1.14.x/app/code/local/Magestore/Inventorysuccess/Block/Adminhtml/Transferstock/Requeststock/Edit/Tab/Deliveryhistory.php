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

class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit_Tab_Deliveryhistory
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit_Tab_Deliveryhistory constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('deliveryhistory');
        $this->setDefaultSort('activity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getModel('inventorysuccess/transferstock_activity')->getCollection()
            ->addFieldToFilter('transferstock_id', $this->getRequest()->getParam('id'))
            ->addFieldToFilter('activity_type',
                Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE_DELIVERY);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('activity_id', array(
            'header' => Mage::helper('inventorysuccess')->__('ID'),
            'filter' => false,
            'align' => 'center',
            'type' => 'number',
            'width' => '150px',
            'index' => 'activity_id',
            'name' => 'activity_id'
        ));
        $this->addColumn('created_at', array(
            'header' => Mage::helper('inventorysuccess')->__('Created At'),
            'filter' => false,
            'align' => 'center',
            'width' => '150px',
            'index' => 'created_at',
            'name' => 'created_at',
            'type' => 'datetime'
        ));
        $this->addColumn('created_by', array(
            'header' => Mage::helper('inventorysuccess')->__('Created By'),
            'filter' => false,
            'align' => 'left',
            'width' => '350px',
            'index' => 'created_by',
            'name' => 'created_by'
        ));
        $this->addColumn('total_qty', array(
            'header' => Mage::helper('inventorysuccess')->__('Delivered Qty'),
            'filter' => false,
            'type' => 'number',
            'width' => '20px',
            'name' => 'total_qty',
            'index' => 'total_qty',
        ));
        $this->addColumn('action',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('inventorysuccess')->__('View'),
                        'url' => array('base' => '*/inventorysuccess_transferstock_activity/view'),
                        'field' => 'activity_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addColumn('action1',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('inventorysuccess')->__('Print'),
                        'url' => array('base' => '*/inventorysuccess_transferstock_actionprint/print'),
                        'field' => 'activity_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addColumn('action2',
            array(
                'header' => Mage::helper('inventorysuccess')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('inventorysuccess')->__('Export'),
                        'url' => array('base' => '*/inventorysuccess_transferstock_actionexport/export'),
                        'field' => 'activity_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));


        return parent::_prepareColumns();
    }


    /**
     * Grid url getter
     * Version of getGridUrl() but with parameters
     *
     * @param array $params url parameters
     * @return string current grid url
     */
    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/deliveryhistorygrid', array(
            '_current' => true
        ));
    }
}
