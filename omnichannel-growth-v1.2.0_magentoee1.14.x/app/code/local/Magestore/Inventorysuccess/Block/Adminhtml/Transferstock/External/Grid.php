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
 * Inventorysuccess Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('externalGrid');
        $this->setDefaultSort('transferstock_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventorysuccess/transferstock')->getCollection()
            ->addFieldToFilter('type', $this->getRequest()->getParam('type'));
        $resourceId = $this->getResourceId();
        $warehouse = Magestore_Coresuccess_Model_Service::transferStockService()->getAvailableWarehousesArray($resourceId);
        if(array_keys($warehouse)) {
            if($this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL) {
                $collection->addFieldToFilter('source_warehouse_id', array_keys($warehouse));
            }else{
                $collection->addFieldToFilter('des_warehouse_id', array_keys($warehouse));
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getResourceId(){
        $resourceId = '';
        if($this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL){
            $resourceId = 'admin/inventorysuccess/view_transferstock/view_toexternal';
        }
        if($this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL){
            $resourceId = 'admin/inventorysuccess/view_transferstock/view_fromexternal';
        }
        return $resourceId;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transferstock_id', array(
            'header' => Mage::helper('inventorysuccess')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transferstock_id'
        ));
        $this->addColumn('transferstock_code', array(
            'header' => Mage::helper('inventorysuccess')->__('Transfer code'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transferstock_code'
        ));
        if ($this->getRequest()->getParam('type') == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL) {
            $this->addColumn('source_warehouse_code', array(
                'header' => Mage::helper('inventorysuccess')->__('Source Warehouse'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'source_warehouse_code'
            ));
        } else {
            $this->addColumn('des_warehouse_code', array(
                'header' => Mage::helper('inventorysuccess')->__('Destination Warehouse'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'des_warehouse_code'
            ));
        }
        $this->addColumn('external_location', array(
            'header' => Mage::helper('inventorysuccess')->__('External Location'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'external_location'
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('inventorysuccess')->__('Qty Transfered'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'qty',
            'type' => 'number',
        ));
        $this->addColumn('created_at', array(
            'header' => Mage::helper('inventorysuccess')->__('Created At'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'created_at',
            'type' => 'datetime'
        ));
        $this->addColumn('created_by', array(
            'header' => Mage::helper('inventorysuccess')->__('Created By'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'created_by'
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('inventorysuccess')->__('Status'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'status',
            'type'      => 'options',
            'options'   => Magestore_Inventorysuccess_Model_Service_FilterCollection_Filter::_transfer_stock_status()
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
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventorysuccess')->__('CSV'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_External_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('transferstock_id');
        $this->getMassactionBlock()->setFormFieldName('transferstock');
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get grid url (use for ajax load)
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }


}