<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report;

/**
 * class \Magestore\Webpos\Block\Adminhtml\Report\Dashboard
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Report
 * @module      Webpos
 * @author      Magestore Developer
 */
class Dashboard extends \Magestore\Webpos\Block\Adminhtml\AbstractBlock
{
    /**
     * report list
     *
     * @var array
     */
    protected $_reportList;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {        
        parent::__construct($context, $objectManager, $messageManager, $data);
    }
    
    /**
     * get a list of staff report controllers and names
     * 
     * @return array
     */
    public function getStaffReportList(){
        return array(
                    'salestaff'            => __('Sales by staff'),
                    'salestaffdaily'       => __('Sales by staff (Daily)'),
                    'orderliststaff'       => __('Order list by staff')
                    );
    }

    /**
     * get a list of location report controllers and names
     *
     * @return array
     */
    public function getLocationReportList(){
        return  array(
                    'salelocation'         => __('Sales by location'),
                    'salelocationdaily'    => __('Sales by location (Daily)'),
                    'orderlistlocation'    => __('Order list by location')
            );
    }

    /**
     * get a list of payment report controllers and names
     *
     * @return array
     */
    public function getPaymentReportList(){
        if(!$this->_reportList){
            $this->_reportList = array(
                'salepayment'          => __('Sales by payment method'),
                'salepaymentdaily'     => __('Sales by payment method (Daily)'),
                'orderlistpayment'     => __('Order list by payment method'),
                'salepaymentlocation'  => __('Sales by payment method for location')
            );
        }
        return $this->_reportList;
    }

    public function getReportList(){
        if(!$this->_reportList){
            $this->_reportList = array_merge(
                $this->getStaffReportList(),
                $this->getLocationReportList(),
                $this->getPaymentReportList()
            );
        }
        return $this->_reportList;
    }

    /**
     * get report link from name 
     *
     * @param string
     * @return string
     */
    public function getReportLink($controller){
        $path = 'webposadmin/report_'. $controller;
        return $this->getUrl($path, array('_forced_secure' => $this->getRequest()->isSecure()));
    }

    /**
     * get current report name 
     *
     * @param 
     * @return string
     */
    public function getCurrentReportName(){
        $controller = $this->getRequest()->getControllerName();        
        $controller = str_replace('report_', '', $controller);
        $reportList = $this->getReportList();
        $reportName = '';
        if(isset($reportList[$controller]))
            $reportName = $reportList[$controller]; 
        return $reportName;
    }
}
