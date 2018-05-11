<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab;
/**
 * Class Sessions
 * @package Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab
 */
class Sessions extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory
     */
    protected $shiftCollectionFactory;

    /**
     * @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
     */
    protected $shiftRepository;

    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Staff
     */
    protected $staffSource;

    /**
     * Sessions constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory $shiftCollectionFactory
     * @param \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Staff $staffSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory $shiftCollectionFactory,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository,
        \Magestore\Webpos\Model\Source\Adminhtml\Staff $staffSource,
        array $data = array()
    )
    {
        $this->shiftCollectionFactory = $shiftCollectionFactory;
        $this->shiftRepository = $shiftRepository;
        $this->staffSource = $staffSource;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setId('sessions_grid');
        $this->setDefaultSort('opened_at');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {
        $posId = $this->getRequest()->getParam('id');
        $collection = $this->shiftCollectionFactory->create();
        $collection->addFieldToFilter('pos_id', $posId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns() {
        $this->addColumn('shift_id', array(
            'header' => __('ID'),
            'width' => '50px',
            'index' => 'shift_id'
        ));

        $this->addColumn('staff_id', array(
            'header' => __('Staff'),
            'index' => 'staff_id',
            'type' => 'options',
            'options' => $this->getStaffList()
        ));

        $this->addColumn('opened_at', array(
            'header' => __('Open From'),
            'index' => 'opened_at',
            'type' => 'datetime'
        ));

        $this->addColumn('closed_at', array(
            'header' => __('Closed At'),
            'index' => 'closed_at',
            'type' => 'datetime'
        ));

        $this->addColumn('float_amount', array(
            'header' => __('Opening Amount'),
            'index' => 'float_amount',
            'type' => 'currency'
        ));

        $this->addColumn('closed_amount', array(
            'header' => __('Closed Amount'),
            'index' => 'closed_amount',
            'type' => 'currency'
        ));
        return parent::_prepareColumns();
    }

    /**
     * @return mixed|string
     */
    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') :
            $this->getUrl('*/*/sessionsGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/zreport/detail', array('_current' => true, 'id' => $row->getEntityId()));
    }
    /**
     * @return array
     */
    public function getSelectedSessions()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getStaffList(){
        $options = $this->staffSource->getOptionArray();
        if(isset($options[0])){
            unset($options[0]);
        }
        return $options;
    }

}