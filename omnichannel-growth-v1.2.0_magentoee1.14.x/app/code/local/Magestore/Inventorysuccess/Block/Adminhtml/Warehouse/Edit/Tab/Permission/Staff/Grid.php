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
 * Warehouse Permission Staff Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Permission_Staff_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_staffs';

    /**
     * @var array
     */
    protected $editFields = array('role_id');

    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouse_permission_staff_list');
        $this->setDefaultSort('user_id');
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
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $collection = Mage::getResourceModel('inventorysuccess/roles_user_collection');
        if ($warehouseId) {
            $userIds = Magestore_Coresuccess_Model_Service::permissionService()->getListPermissionsByObject(
                Mage::getModel('inventorysuccess/warehouse')->load($warehouseId)
            )->getColumnValues(Magestore_Inventorysuccess_Model_Permission::USER_ID);
            if (!empty($userIds))
                $collection->addFieldToFilter('user_id', array('nin' => $userIds));
        } else {
            $collection = $collection->addFieldToFilter('user_id', 0);
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
        $this->addColumn(
            "staffs",
            array(
                "type" => "checkbox",
                "name" => "staffs",
                "filter" => false,
                "index" => "user_id",
                'use_index' => true,
                'header_css_class'  => 'a-center',
                'align'     => 'center'
            )
        )->addColumn("user_id",
            array(
                "header" => $this->__("ID"),
                "index" => "user_id",
                "sortable" => true,
                'align' => 'left',
            )
        )->addColumn("username",
            array(
                "header" => $this->__("Staff"),
                "index" => "username",
                "sortable" => true,
                'align' => 'left',
            )
        )->addColumn("fullname",
            array(
                "header" => $this->__("Staff Name"),
                "index" => "fullname",
                "sortable" => true,
                'align' => 'left',
            )
        )->addColumn("admin_role",
            array(
                "header" => $this->__("Admin Role"),
                "index" => "role_id",
                "sortable" => true,
                'align' => 'left',
                'type' => 'options',
                'options' => Mage::getModel('inventorysuccess/roles_options_roles')->getOptionArray()
            )
        )->addColumn("role_id",
            array(
                "header" => $this->__("Staff Role"),
                "index" => "role_id",
                "sortable" => true,
                'align' => 'left',
                'filter' => false,
                'sortable' => false,
                'type' => 'select',
                'options' => Mage::getModel('inventorysuccess/roles_options_roles')->getOptionArray()
            )
        );

        return parent::_prepareColumns();
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
        if (!$this->getRequest()->getParam('warehouse_id')) {
            $params['warehouse_id'] = $this->getRequest()->getParam('id');
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
        return $this->getUrl('*/inventorysuccess_warehouse_permission_staff/grid', $this->getParamsUrl());
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_permission_staff/save', $this->getParamsUrl());
    }
}