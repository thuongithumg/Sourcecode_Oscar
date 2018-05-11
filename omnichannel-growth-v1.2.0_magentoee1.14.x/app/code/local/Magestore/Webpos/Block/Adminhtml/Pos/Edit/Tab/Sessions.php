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

class Magestore_Webpos_Block_Adminhtml_Pos_Edit_Tab_Sessions extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('sessiongrid');
        $this->setDefaultSort('opened_at');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('shift_id' => 1));
        }
    }

    protected function _prepareCollection() {
        $posId = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('webpos/shift')->getCollection();
        $collection->addFieldToFilter('pos_id', $posId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('shift_id', array(
            'header' => __('ID'),
            'width' => '50px',
            'index' => 'shift_id'
        ));

        $this->addColumn('user_id', array(
            'header' => Mage::helper('webpos')->__('User'),
            'align' => 'left',
            'index' => 'user_id',
            'type' => 'options',
            'options' => Mage::getModel('webpos/user')->toOptionArray()
        ));
        $this->addColumn('opened_at', array(
            'header' => Mage::helper('webpos')->__('Open From'),
            'index' => 'opened_at',
            'type' => 'datetime'
        ));
        $this->addColumn('closed_at', array(
            'header' => Mage::helper('webpos')->__('Closed At'),
            'index' => 'closed_at',
            'type' => 'datetime'
        ));
        $this->addColumn('float_amount', array(
            'header' => Mage::helper('webpos')->__('Opening Amount'),
            'index' => 'float_amount',
            'type' => 'currency'
        ));
        $this->addColumn('closed_amount', array(
            'header' => Mage::helper('webpos')->__('Closed Amount'),
            'index' => 'closed_amount',
            'type' => 'currency'
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
            ->addFieldToFilter('location_id', $this->getRequest()->getParam('id'));
        foreach ($collection as $item){
            $userIds[] = $item->getUserId();
        }
        return $userIds;
    }

}