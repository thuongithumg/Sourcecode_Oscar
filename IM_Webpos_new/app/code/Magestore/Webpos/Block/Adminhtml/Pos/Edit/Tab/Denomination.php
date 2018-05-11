<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab;
/**
 * Class Denomination
 * @package Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab
 */
class Denomination extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory
     */
    protected $denominationCollectionFactory;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosRepository
     */
    protected $posRepository;

    /**
     * Denomination constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory $denominationCollectionFactory
     * @param \Magestore\Webpos\Model\Pos\PosRepository $posRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Webpos\Model\ResourceModel\Denomination\Denomination\CollectionFactory $denominationCollectionFactory,
        \Magestore\Webpos\Model\Pos\PosRepository $posRepository,
        array $data = array()
    )
    {
        $this->denominationCollectionFactory = $denominationCollectionFactory;
        $this->posRepository = $posRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setId('denomination_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_pos' => 1));
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_pos') {
            $denominationIds = $this->getSelectedDenominations();

            if (empty($denominationIds)) {
                $denominationIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('denomination_id', array('in' => $denominationIds));
            } else {
                if ($denominationIds) {
                    $this->getCollection()->addFieldToFilter('denomination_id', array('nin' => $denominationIds));
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
        $collection = $this->denominationCollectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns() {
        $this->addColumn('in_pos', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_pos',
            'align' => 'center',
            'index' => 'denomination_id',
            'values'=> $this->getSelectedDenominations()
        ));

        $this->addColumn('denomination_id', array(
            'header' => __('ID'),
            'width' => '50px',
            'index' => 'denomination_id',
            'type' => 'number'
        ));

        $this->addColumn('denomination_name', array(
            'header' => __('Denomination Name'),
            'index' => 'denomination_name'
        ));

        $this->addColumn('denomination_value', array(
            'header' => __('Denomination Value'),
            'index' => 'denomination_value',
            'type' => 'number'
        ));

        $this->addColumn('sort_order', array(
            'header' => __('Sort Order'),
            'index' => 'sort_order',
            'type' => 'number'
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return mixed|string
     */
    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') :
            $this->getUrl('*/*/denominationsGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
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
    public function getSelectedDenominations()
    {
        $posId = $this->getRequest()->getParam('id');
        $pos =  $this->posRepository->get($posId);
        return ($pos)?explode(',', $pos->getDenominationIds()):[];
    }

}