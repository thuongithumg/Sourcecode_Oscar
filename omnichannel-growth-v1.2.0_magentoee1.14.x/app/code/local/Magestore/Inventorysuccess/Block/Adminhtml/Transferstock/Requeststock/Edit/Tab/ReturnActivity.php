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
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit_Tab_ReturnActivity
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     */
    protected $returnedCollection;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Edit_Tab_Receiving constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('returned');
        $this->setDefaultSort('transferstock_product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->getReturnedCollection();
        $this->setCollection($this->returnedCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Mysql4_Transferstock_Product_Collection
     * @throws Exception
     */
    protected function getReturnedCollection()
    {
        if (!$this->returnedCollection) {
            /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
            $this->returnedCollection = Mage::getModel('inventorysuccess/transferstock_activity')->getCollection()
                ->addFieldToFilter(
                    Magestore_Inventorysuccess_Model_Transferstock_Activity::TRANSFERSTOCK_ID,
                    $this->getRequest()->getParam('id')
                )->addFieldToFilter(
                    Magestore_Inventorysuccess_Model_Transferstock_Activity::ACTIVITY_TYPE,
                    array('eq' => 'returning')
                );
        }
        return $this->returnedCollection;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('created_by', array(
            'header' => $this->__('Created By'),
            'width' => '350px',
            'align' => 'left',
            'index' => 'created_by',
        ));
        $this->addColumn('created_at', array(
            'header' => $this->__('Created At'),
            'width' => '350px',
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'created_at',
        ));
        $this->addColumn('total_qty', array(
            'header' => $this->__('Qty Returned'),
            'width' => '20px',
            'index' => 'total_qty',
            'type' => 'number',
            'name' => 'total_qty',
        ));
        $this->addColumn('note', array(
            'header' => $this->__('Note'),
            'width' => '350px',
            'align' => 'left',
            'index' => 'note',
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

    public function getSelectedProducts()
    {
        return array();
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
        return $this->getUrl('*/*/*', array(
            '_current' => true,
        ));
    }

    /**
     * @param $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }
}