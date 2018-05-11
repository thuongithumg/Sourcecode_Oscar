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
 * Warehouse Permission Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Permission_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_permissions';

    /**
     * @var array
     */
    protected $editFields = array('role_id');

    /**
     * @var Mage_Core_Model_Abstract
     */
    protected $warehouse;

    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouse_permission_list');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->warehouse = Mage::getModel('inventorysuccess/warehouse')->load($this->getRequest()->getParam('id'));
    }

    /**
     * Set hidden input field name for selected products
     *
     * @param $name
     */
    protected function setHiddenInputField($name)
    {
        $this->hiddenInputField = $name;
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
     * Prepare collection for grid product
     *
     * @return $this
     */
    protected function _prepareCollection()
    {

        $collection = Magestore_Coresuccess_Model_Service::permissionService()->getListPermissionsByObject(
            $this->warehouse
        );
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
        $this->addColumn(
            "id",
            array(
                "type" => "checkbox",
                "name" => "id",
                "filter" => false,
                "index" => "user_id",
                'use_index' => true,
                'value' => '',
                'header_css_class' => 'a-center',
                'align' => 'center'
            )
        )->addColumn("user_id",
            array(
                "header" => $this->__("User ID"),
                "index" => "user_id",
                "sortable" => true,
                'align' => 'left',
            )
        )->addColumn("staff",
            array(
                "header" => $this->__("Staff"),
                "index" => "username",
                "sortable" => true,
                'align' => 'left',
            )
        );
        $editable = false;
        if (\Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
            'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/manage_permission', $this->warehouse)
        )
            $editable = true;
        $this->addColumn("role_id",
            array(
                "header" => $this->__("Warehouse Roles"),
                "index" => "role_id",
                "editable" => $editable,
                "sortable" => false,
                "filter" => false,
                'align' => 'left',
                'type' => 'select',
                'options' => Mage::getModel('inventorysuccess/roles_options_roles')->getOptionArray()
            )
        )->addColumn("delete",
            array(
                "header" => $this->__("Delete"),
                "index" => "id",
                "sortable" => false,
                "filter" => false,
                'align' => 'left',
                "index" => "user_id",
                'renderer' => 'inventorysuccess/adminhtml_manageStock_grid_column_renderer_delete'
            )
        );
        Mage::dispatchEvent('prepare_warehouse_permission_grid_columns', array('object' => $this));
        return parent::_prepareColumns();
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
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_permission/grid', array('_current' => true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_permission/save', array('_current' => true));
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_permission/delete', array('_current' => true));
    }
}