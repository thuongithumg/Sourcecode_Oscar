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
class Magestore_Inventorysuccess_Block_Adminhtml_Permissions_User_Edit_Tab_Warehouse_Warehouse_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_warehouses';
    
    protected $editFields = array('role_id');

    public function __construct()
    {
        parent::__construct();
        $this->setId('user_permission_warehouse_warehouse_list');
        $this->setDefaultSort('warehouse_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField()
    {
        return $this->hiddenInputField;
    }

    /**
     * @return string
     */
    public function getSelectedItems()
    {
        return Zend_Json::encode(array());
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        return Zend_Json::encode($this->editFields);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Inventorysuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $staffId = $this->getRequest()->getParam('staff_id');
        if ($staffId) {
            $collection->joinPermissionByUserId($staffId);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare warehouse product grid columns
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('warehouse_permission',
            array(
                "type" => "checkbox",
                "name" => "warehouse_permission",
                "filter" => false,
                "index" => "warehouse_id",
                'use_index' => true,
            )
        )->addColumn('warehouse',
            array(
                'header' => $this->__('Warehouse'),
                'align' => 'left',
                'index' => 'warehouse',
            )
        )->addColumn("role_id",
            array(
                "header"    => $this->__("Warehouse Roles"),
                "index"     => "role_id",
                "editable"  => true,
                "sortable"  => false,
                "filter"    => false,
                'align'     => 'left',
                'type'      => 'select',
                'options'   => Mage::getModel('inventorysuccess/roles_options_roles')->getOptionArray()
            )
        )->addColumn('action',
            array(
                'header' => $this->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $this->__('View'),
                        'url' => array('base' => '*/inventorysuccess_warehouse/edit'),
                        'field' => 'id'
                    )),
                'filter' => false,
                'sortable' => false,
            )
        );
        parent::_prepareColumns();
        $this->_exportTypes = array();
        return $this;
    }

    /**
     * Get params url for grid url and save url
     *
     * @return array
     * @throws Exception
     */
    protected function getParamsUrl()
    {
        $params = array('_current' => true);
        if (!$this->getRequest()->getParam('staff_id')) {
            $params['staff_id'] = $this->getRequest()->getParam('user_id');
        }
        return $params;
    }

    /**
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_permission_user_warehouse_warehouse/grid', $this->getParamsUrl());
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/inventorysuccess_permission_user_warehouse_warehouse/save', $this->getParamsUrl());
    }
}