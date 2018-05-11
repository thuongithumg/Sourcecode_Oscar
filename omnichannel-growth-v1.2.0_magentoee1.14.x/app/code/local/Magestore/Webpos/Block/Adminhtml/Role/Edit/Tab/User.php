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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Block_Adminhtml_Role_Edit_Tab_User extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('usergrid');
        $this->setDefaultSort('user_id');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_user' => 1));
        }
    }

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_user') {
            $userIds = $this->getSelectedUsers();

            if (empty($userIds)) {
                $userIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('user_id', array('in' => $userIds));
            } else {
                if ($userIds) {
                    $this->getCollection()->addFieldToFilter('user_id', array('nin' => $userIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('webpos/user')->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('in_user', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_user',
            'align' => 'center',
            'index' => 'user_id',
            'values'=> $this->getSelectedUsers(),
        ));

        $this->addColumn('user_id', array(
            'header' => Mage::helper('webpos')->__('ID'),
            'width' => '50px',
            'index' => 'user_id',
            'type' => 'number',
        ));

        $this->addColumn('username', array(
            'header' => Mage::helper('webpos')->__('User Name'),
            'index' => 'username'
        ));

        $this->addColumn('user_display_name', array(
            'header' => Mage::helper('webpos')->__('Display Name'),
            'index' => 'display_name'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('webpos')->__('Email'),
            'index' => 'email'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('webpos')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enable',
                2 => 'Disable',
            ),

        ));



        return parent::_prepareColumns();
    }

    //return url
    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/usergrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    public function getRowUrl($row) {
        return '';
    }

    public function getSelectedUsers()
    {
        $userIds=array();
        $collection = Mage::getModel('webpos/user')->getCollection()
            ->addFieldToFilter('role_id', $this->getRequest()->getParam('id'));
        foreach ($collection as $item){
            $userIds[] = $item->getUserId();
        }
        return $userIds;
    }

}