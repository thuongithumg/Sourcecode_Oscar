<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report;

/**
 * Report abstract grid container.
 * @category Magestore
 * @package  Magestore_Webpos
 * @module   Webpos
 * @author   Magestore Developer
 */
class AbstractGrid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
{    

    /**
     * check show number of orders
     *
     * @var bool
     */
    protected $_isShowOrderNumber = true;

    /**
     * column report key
     *
     * @var string
     */
    protected $_timeColumnReportKey;

    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy;

    /**
     * column report key
     *
     * @var string
     */
    protected $_firstColumnReportKey;


    /**
     * column report name
     *
     * @var string
     */
    protected $_firstColumnReportName;

    /**
     * column report key
     *
     * @var string
     */
    protected $_secondColumnReportKey;

    /**
     * column report name
     *
     * @var string
     */
    protected $_secondColumnReportName;

    /**
     * constructor
     * 
     */
    protected function _construct()
    {        
        parent::_construct();
        $this->setCountTotals(true);
    }

    /**
     * set columns
     *
     * @return this    
     */
    protected function _prepareColumns()
    {
        if($this->_timeColumnReportKey)
            $this->addColumn(
                'created_at',
                [
                    'header' => __('Day'),
                    'index' => 'created_at',
                    'sortable' => false,
                    'period_type' => $this->getPeriodType(),
                    'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
                    'totals_label' => __(''),
                    'html_decorators' => ['nobr'],
                    'header_css_class' => 'col-period',
                    'column_css_class' => 'col-period'
                ]
            );

        if($this->_firstColumnReportKey)
            $this->addColumn(
                $this->_firstColumnReportKey,
                [
                    'header' => __($this->_firstColumnReportName),
                    'index' => $this->_firstColumnReportKey,
                    'sortable' => false,
                    'totals_label' => __(''),                    
                    'header_css_class' => 'col-first',
                    'column_css_class' => 'col-first'
                ]
            );

        if($this->_secondColumnReportKey)
            $this->addColumn(
                $this->_secondColumnReportKey,
                [
                    'header' => __($this->_secondColumnReportName),
                    'index' => $this->_secondColumnReportKey,
                    'sortable' => false,                                        
                    'totals_label' => __(''),
                    'header_css_class' => 'col-second',
                    'column_css_class' => 'col-second'
                ]
            );        

        if($this->_isShowOrderNumber)
            $this->addColumn(
                'entity_id',
                [
                    'header' => __('Order Count'),
                    'index' => 'entity_id',
                    'type' => 'number',
                    'total' => 'count',
                    'sortable' => false,
                    'header_css_class' => 'col-orders',
                    'column_css_class' => 'col-orders'
                ]
          );        

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn(
            'base_real_amount',
            [
                'header' => __('Sales Total'),
                'type' => 'currency',
                'currency_code' => $currencyCode,
                'index' => 'base_real_amount',
                'total' => 'sum',
                'sortable' => false,
                'rate' => $rate,
                'header_css_class' => 'col-sales-total',
                'column_css_class' => 'col-sales-total'
            ]
        );    

        if(!$this->_isShowOrderNumber){
            $this->addColumn(
                'created_date',
                [
                    'header' => __('Purchased On'),
                    'index' => 'created_date',
                    'sortable' => false,                                       
                    'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',                                        
                    'header_css_class' => 'col-created-at',
                    'column_css_class' => 'col-created-at'
                ]
            );

            $this->addColumn(
                'order_status.label',
                [
                    'header' => __('Status'),
                    'index' => 'order_status.label',
                    'sortable' => false,
                    'header_css_class' => 'col-orders',
                    'column_css_class' => 'col-orders'
                ]
            );  
        }

        $this->addExportType('*/*/ExportCsv', __('CSV'));
        $this->addExportType('*/*/ExportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
