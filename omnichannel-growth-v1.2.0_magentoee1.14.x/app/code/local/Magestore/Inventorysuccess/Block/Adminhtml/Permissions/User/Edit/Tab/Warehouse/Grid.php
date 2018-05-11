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
class Magestore_Inventorysuccess_Block_Adminhtml_Permissions_User_Edit_Tab_Warehouse_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_permission';

    /**
     * @var array
     */
    protected $editFields = array('role_id');

    public function __construct()
    {
        parent::__construct();
        $this->setId('user_permission_warehouse_list');
        $this->setDefaultSort('object_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField(){
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
    public function getEditFields(){
        return Zend_Json::encode($this->editFields);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Inventorysuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('inventorysuccess/permission_collection')
            ->joinWarehouse()
            ->setStaffToFilter($this->getRequest()->getParam('user_id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('warehouse_permission',
            array(
                "type"      => "checkbox",
                "name"      => "warehouse_permission",
                "filter"    => false,
                "index"     => "object_id",
                'use_index' => true,
            )
        )->addColumn("object_id",
            array(
                "header"    => $this->__("Warehouse"),
                "index"     => "object_id",
                'align'     => 'left',
                'type'      => 'options',
                'options'   => Mage::getModel('inventorysuccess/warehouse_options_warehouse')->getOptionArray()
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
        )->addColumn("delete",
            array(
                "header"    => $this->__("Delete"),
                "index"     => "object_id",
                "sortable"  => false,
                "filter"    => false,
                'align'     => 'left',
                'renderer'  => 'inventorysuccess/adminhtml_manageStock_grid_column_renderer_delete'
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_permission_user_warehouse/grid', array('_current' => true));
    }

    /**
     * Save grid url getter
     *
     * @return string save grid url
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/inventorysuccess_permission_user_warehouse/save', array('_current' => true));
    }

    /**
     * Delete row grid url getter
     *
     * @return string delete row grid url
     */
    public function getDeleteUrl(){
        return $this->getUrl('*/inventorysuccess_permission_user_warehouse/delete', array('_current' => true));
    }
}