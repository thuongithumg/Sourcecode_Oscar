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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Location_Edit_Tab_Warehouse
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Location_Edit_Tab_Warehouse extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Location_Edit_Tab_Warehouse constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('locationGrid');
        $this->setDefaultSort('location_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('filter');
    }


    /**
     * prepare product collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('inventorysuccess/warehouseLocationMap')->getCollection()
                        ->getLocationCollection();
        $this->setDefaultFilter(array('in_locations' => 1));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_locations', array(
            'header_css_class' => 'a-center',
            'type'   => 'checkbox',
            'name'   => 'in_locations',
            'values' => $this->_getSelectedLocations(),
            'align'  => 'center',
            'index'  => 'location_id',
        ));

        $this->addColumn('display_name', array(
            'header' => Mage::helper('inventorysuccess')->__('Location'),
            'index'  => 'display_name',
            'name'   => 'display_name',
        ));

        $this->addColumn('warehouse_id', array(
            'header' => Mage::helper('inventorysuccess')->__('Warehouse'),
            'type'   => 'text',
            'index'  => 'warehouse_id',
            'name'   => 'warehouse_id',
            'filter' => false,
            'renderer' => 'inventorysuccess/adminhtml_location_renderer_warehouse'
        ));
        /*
        $this->addColumn('warehouse_id', array(
            'header' => Mage::helper('inventorysuccess')->__('Warehouse'),
            'type'   => 'input',
            'index'  => 'warehouse_id',
            'name'   => 'warehouse_id',
            'column_css_class'=>'no-display',
            'header_css_class'=>'no-display'
        ));
         * 
         */
    }

    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/locationGrid', array(
            '_current' => true,
            'store' => $this->getRequest()->getParam('store')
        ));
    }

    /**
     * get row url
     *
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * prepare selected product for filter
     *
     * @return array|string
     */
    protected function _getSelectedLocations()
    {
        $locationArrays = $this->getLocationSelect();
        $locations = '';
        if ($locationArrays) {
            $locations = array();
            foreach ($locationArrays as $location) {
                $locations[] = $location->getLocationId();
            }
        }
        return $locations;
    }

    /**
     * get selected product collection for filter
     *
     * @return mixed
     */
    public function getLocationSelect()
    {
        $productCollection = Mage::getModel('inventorysuccess/warehouseLocationMap')->getCollection();
        return $productCollection;
    }

    /**
     * get all selected location to params
     *
     * @return array
     */
    public function getSelectedRelatedLocations()
    {
        $locations = array();
        $collection = $this->getLocationSelect();
        if ($collection) {
            foreach ($collection as $location) {
                $locations[$location->getData('location_id')] = array(
                    'warehouse_id' => $location->getData('warehouse_id')
                );
            }
        }
        return $locations;
    }

}
