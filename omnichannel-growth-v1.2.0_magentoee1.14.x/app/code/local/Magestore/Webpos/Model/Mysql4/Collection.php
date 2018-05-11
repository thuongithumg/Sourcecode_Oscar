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

class Magestore_Webpos_Model_Mysql4_Collection extends Mage_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * Period format
     *
     * @var string
     */
    protected $_periodFormat;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'sales/order';

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
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * Initialize custom resource model
     *
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Get selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();
        if($this->_timeColumnGroup){
            if ('month' == $this->_period) {
                $this->_periodFormat = $adapter->getDateFormatSql($this->_timeColumnGroup, '%Y-%m');
            } elseif ('year' == $this->_period) {
                $this->_periodFormat = $adapter->getDateExtractSql('created_at', Varien_Db_Adapter_Interface::INTERVAL_YEAR);
            } else {
                $this->_periodFormat = $adapter->getDateFormatSql($this->_timeColumnGroup, '%Y-%m-%d');
            }
        }

        if (!$this->isTotals()) {
            if ($this->_firstColumnGroup == 'payment.method' || $this->_secondColumnGroup == 'payment.method' || $this->_thirdColumnGroup == 'payment.method') {
                $this->_selectedColumns = array(
                    'period'                         => $this->_periodFormat,
                    'user.display_name'              => '(user.display_name)',
                    'grand_total'                    => 'SUM(grand_total)',
                    'base_grand_total'               => 'SUM(base_grand_total)',
                    'created_at'                     => 'created_at',
                    'increment_id'                   => 'increment_id',
                    $this->getTable('sales/order').'.status'        => $this->getTable('sales/order').'.status',
                    'location.display_name'          => 'location.display_name',
                    'payment.method_title'           => 'payment.method_title',
                    'payment.method'                 => 'payment.method',
                    'entity_id'                      => 'count(entity_id)',
                    'base_real_amount'               => 'sum(base_real_amount)',
                    'base_total_paid'                => 'sum(base_total_paid)',
                    'total_paid'                     => 'sum(total_paid)'
                );
            } else {
                $this->_selectedColumns = array(
                    'period'                         => $this->_periodFormat,
                    'user.display_name'              => '(user.display_name)',
                    'grand_total'                    => 'SUM(grand_total)',
                    'base_grand_total'               => 'SUM(base_grand_total)',
                    'created_at'                     => 'created_at',
                    'increment_id'                   => 'increment_id',
                    $this->getTable('sales/order').'.status'        => $this->getTable('sales/order').'.status',
                    'location.display_name'          => 'location.display_name',
                    'entity_id'                      => 'count(entity_id)',
                    'base_total_paid'                => 'sum(base_total_paid)',
                    'total_paid'                     => 'sum(total_paid)'
                );
            }

        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        $this->_selectedColumns['store_id'] = 'store_id';

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Resource_Report_Order_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        $this->getSelect()->join(array('user' => $this->getTable('webpos/user')),
            $this->getTable('sales/order').'.webpos_staff_id = user.user_id',
            array());
        $this->getSelect()->join(array('location' => $this->getTable('webpos/userlocation')),
            $this->getTable('sales/order').'.location_id = location.location_id',
            array());
        if ($this->_firstColumnGroup == 'payment.method' || $this->_secondColumnGroup == 'payment.method' || $this->_thirdColumnGroup == 'payment.method')
            $this->getSelect()->joinLeft(array('payment' => $this->getTable('webpos/orderpayment')),
                $this->getTable('sales/order').'.entity_id = payment.order_id',
                array()
            );
//        $this->getSelect()->group('webpos_staff_name');
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
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        $currentDate = Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);

        /* @var  $diffTimeQuery string ~ DATE_ADD(`created_at`, INTERVAL -25200 SECOND) */
        $diffTimeQuery = $this->getStoreTZOffsetQuery(
            $this->getTable('sales/order'),
            'created_at',
            $date, null
        );
        $diffTimeQueryArr = explode(' ', $diffTimeQuery);
        $oz = $diffTimeQueryArr[2];


        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases
        if ($this->_from !== null) {
            $this->_from = $this->_from.' 00:00:00';
            $this->getSelect()->where("DATE_FORMAT(DATE_ADD(created_at, INTERVAL $oz SECOND),'%Y-%m-%d %H:%i:%s') >= ?", $this->_from);
        }
        if ($this->_to !== null) {
            $this->_to = $this->_to.' 23:59:59';
            $this->getSelect()->where("DATE_FORMAT(DATE_ADD(created_at, INTERVAL $oz SECOND),'%Y-%m-%d %H:%i:%s') <= ?", $this->_to);
        }

        if($this->_period){
            $this->getSelect()->where($this->_firstColumnGroup.'='. '"' . $this->_period . '"');
        }

        return $this;
    }

    protected function _applyOrderStatusFilter()
    {
        if ($this->_orderStatus === null) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where($this->getTable('sales/order').'.status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Retrieve query for attribute with timezone conversion
     *
     * @param string|array $table
     * @param string $column
     * @param mixed $from
     * @param mixed $to
     * @param int|string|Mage_Core_Model_Store|null $store
     * @return string
     */
    public function getStoreTZOffsetQuery($table, $column, $from = null, $to = null, $store = null)
    {
        $column = $this->getConnection()->quoteIdentifier($column);

        if (is_null($from)) {
            $selectOldest = $this->getConnection()->select()
                ->from(
                    $table,
                    array("MIN($column)")
                );
            $from = $this->getConnection()->fetchOne($selectOldest);
        }

        $periods = $this->_getTZOffsetTransitions(
            Mage::app()->getLocale()->storeDate($store)->toString(Zend_Date::TIMEZONE_NAME), $from, $to
        );
        if (empty($periods)) {
            return $column;
        }

        $query = "";
        $periodsCount = count($periods);

        $i = 0;
        foreach ($periods as $offset => $timestamps) {
            $subParts = array();
            foreach ($timestamps as $ts) {
                $subParts[] = "($column between {$ts['from']} and {$ts['to']})";
            }

            $then = $this->getConnection()
                ->getDateAddSql($column, $offset, Varien_Db_Adapter_Interface::INTERVAL_SECOND);

            $query .= (++$i == $periodsCount) ? $then : "CASE WHEN " . join(" OR ", $subParts) . " THEN $then ELSE ";
        }

        return $query . str_repeat('END ', count($periods) - 1);
    }

    /**
     * Retrieve transitions for offsets of given timezone
     *
     * @param string $timezone
     * @param mixed $from
     * @param mixed $to
     * @return array
     */
    protected function _getTZOffsetTransitions($timezone, $from = null, $to = null)
    {
        $tzTransitions = array();
        try {
            if (!empty($from)) {
                $from = new Zend_Date($from, Varien_Date::DATETIME_INTERNAL_FORMAT);
                $from = $from->getTimestamp();
            }

            $to = new Zend_Date($to, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $nextPeriod = $this->getConnection()->formatDate($to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $to = $to->getTimestamp();

            $dtz = new DateTimeZone($timezone);
            $transitions = $dtz->getTransitions();
            $dateTimeObject = new Zend_Date('c');
            for ($i = count($transitions) - 1; $i >= 0; $i--) {
                $tr = $transitions[$i];
                if (!$this->_isValidTransition($tr, $to)) {
                    continue;
                }

                $dateTimeObject->set($tr['time']);
                $tr['time'] = $this->getConnection()
                    ->formatDate($dateTimeObject->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                $tzTransitions[$tr['offset']][] = array('from' => $tr['time'], 'to' => $nextPeriod);

                if (!empty($from) && $tr['ts'] < $from) {
                    break;
                }
                $nextPeriod = $tr['time'];
            }
        } catch (Exception $e) {
            $this->_logException($e);
        }

        return $tzTransitions;
    }

    /**
     * Logs the exceptions
     *
     * @param Exception $exception
     */
    protected function _logException($exception)
    {
        Mage::logException($exception);
    }

    /**
     * Verifies the transition and the "to" timestamp
     *
     * @param array      $transition
     * @param int|string $to
     * @return bool
     */
    protected function _isValidTransition($transition, $to)
    {
        $result         = true;
        $timeStamp      = $transition['ts'];
        $transitionYear = date('Y', $timeStamp);

        if ($transitionYear > 10000 || $transitionYear < -10000) {
            $result = false;
        } else if ($timeStamp > $to) {
            $result = false;
        }

        return $result;
    }

    /**
     * Retrieve store timezone offset from UTC in the form acceptable by SQL's CONVERT_TZ()
     *
     * @param unknown_type $store
     * @return string
     */
    protected function _getStoreTimezoneUtcOffset($store = null)
    {
        return Mage::app()->getLocale()->storeDate($store)->toString(Zend_Date::GMT_DIFF_SEP);
    }
}
