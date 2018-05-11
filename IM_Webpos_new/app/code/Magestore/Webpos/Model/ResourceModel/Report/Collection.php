<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Report;

/**
 * Report order collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection
{
    /**
     * Period format
     *
     * @var string
     */
    protected $_periodFormat;

     /**
     * time column group
     *
     * @var string
     */
    protected $_timeColumnGroup;

    /**
     * first column group
     *
     * @var string
     */
    protected $_firstColumnGroup;

    /**
     * first column group value
     *
     * @var string
     */
    protected $_firstColumnGroupValue;

    /**
     * second column group
     *
     * @var string
     */
    protected $_secondColumnGroup;

    /**
     * second column group
     *
     * @var string
     */
    protected $_thirdColumnGroup;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'sales_order';

    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns = [];

    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_reports_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $resource->init($this->_aggregationTable);
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }

    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases
        if ($this->_from !== null) {
            $this->getSelect()->where('created_at >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->_to = $this->_to.' 23:59:00';
            $this->getSelect()->where('created_at <= ?', $this->_to);
        }

        if($this->_period){
            $this->getSelect()->where($this->_firstColumnGroup.'='.$this->_period);
        }

        return $this;
    }


    /**
     * Get selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $connection = $this->getConnection();
        if($this->_timeColumnGroup){
            if ('month' == $this->_period) {
                $this->_periodFormat = $connection->getDateFormatSql($this->_timeColumnGroup, '%Y-%m');
            } elseif ('year' == $this->_period) {
                $this->_periodFormat = $connection->getDateExtractSql(
                    $this->_timeColumnGroup,
                    \Magento\Framework\DB\Adapter\AdapterInterface::INTERVAL_YEAR
                );
            } else {
                $this->_periodFormat = $connection->getDateFormatSql($this->_timeColumnGroup, '%Y-%m-%d');
            }
        }
    
        if (!$this->isTotals()) {
            $this->_selectedColumns = [
                'created_at'            => $this->_periodFormat,
                'created_date'          => 'created_at',
                'webpos_staff_name'     => 'webpos_staff_name',
                'increment_id'          => 'increment_id',
                'location.display_name' => 'location.display_name',
                'location.location_id'  => 'location.location_id',
                'payment.method_title'  => 'payment.method_title',
                'order_status.label'    => 'order_status.label',
                'entity_id'             => 'count(entity_id)',
                'base_real_amount'   => 'sum(base_real_amount)'
            ];              
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }

    /**
     * Apply custom columns before load
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());        
        $this->getSelect()->join(array('location' => $this->getTable('webpos_staff_location')),
                                    $this->getTable('sales_order').'.location_id = location.location_id',
                                    array());
        $this->getSelect()->joinLeft(array('payment' => $this->getTable('webpos_order_payment')),
                                    $this->getTable('sales_order').'.entity_id = payment.order_id',
                                    array()
                                    );
        $this->getSelect()->join(array('order_status' => $this->getTable('sales_order_status')),
                                    $this->getTable('sales_order').'.status = order_status.status',
                                    array('order_status'=>'order_status.status')
                                    );
        if (!$this->isTotals()) {
            if($this->_periodFormat){               
                $this->getSelect()->group($this->_periodFormat);                        
            }
            if($this->_firstColumnGroup){
                $this->getSelect()->group($this->_firstColumnGroup);
            }
            if($this->_secondColumnGroup){               
                $this->getSelect()->group($this->_secondColumnGroup);
            }
            if($this->_thirdColumnGroup){               
                $this->getSelect()->group($this->_secondColumnGroup);
            }
            
        }

        $this->_applyAggregatedTable();
        $this->_applyDateRangeFilter();
        $this->_applyCustomFilter();
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return $this
     */
    protected function _applyOrderStatusFilter()
    {
        if ($this->_orderStatus === null) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = [$orderStatus];
        }
        $this->getSelect()->where('order_status.status IN(?)', $orderStatus);
        return $this;
    }
}
