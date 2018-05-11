<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Staff\Role\Edit\Tab;
/**
 * Class Staff
 * @package Magestore\Webpos\Block\Adminhtml\Staff\Role\Edit\Tab
 */
class Staff extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Role\CollectionFactory
     */
    protected $_staffCollectionFactory;

    /**
     * Staff constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\Webpos\Model\ResourceModel\Staff\Role\CollectionFactory $staffCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        array $data = array()
    )
    {
        $this->_staffCollectionFactory = $staffCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setId('staff_grid');
        $this->setDefaultSort('staff_id');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_staff' => 1));
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_staff') {
            $staffIds = $this->getSelectedStaffs();

            if (empty($staffIds)) {
                $staffIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('staff_id', array('in' => $staffIds));
            } else {
                if ($staffIds) {
                    $this->getCollection()->addFieldToFilter('staff_id', array('nin' => $staffIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_staffCollectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns() {
        $this->addColumn('in_staff', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_staff',
            'align' => 'center',
            'index' => 'staff_id',
            'values'=> $this->getSelectedStaffs(),
        ));

        $this->addColumn('staff_id', array(
            'header' => __('ID'),
            'width' => '50px',
            'index' => 'staff_id',
            'type' => 'number',
        ));

        $this->addColumn('username', array(
            'header' => __('User Name'),
            'index' => 'username'
        ));

        $this->addColumn('user_display_name', array(
            'header' => __('Display Name'),
            'index' => 'display_name'
        ));

        $this->addColumn('email', array(
            'header' => __('Email'),
            'index' => 'email'
        ));

        $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enable',
                2 => 'Disable',
            ),

        ));

        return parent::_prepareColumns();
    }

    /**
     * @return mixed|string
     */
    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') :
            $this->getUrl('*/*/staffgrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row) {
        return '';
    }

    /**
     * @return array
     */
    public function getSelectedStaffs()
    {
        $staffIds=array();
        $collection = $this->_staffCollectionFactory->create()
            ->addFieldToFilter('role_id', $this->getRequest()->getParam('id'));
        foreach ($collection as $item){
            $staffIds[] = $item->getStaffId();
        }
        return $staffIds;
    }

}