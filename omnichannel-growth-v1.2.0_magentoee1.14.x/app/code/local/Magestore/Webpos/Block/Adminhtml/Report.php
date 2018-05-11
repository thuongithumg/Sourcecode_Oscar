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

/**
 * webpos Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Marketingautomation
 * @author      Magestore Developer
 */
class Magestore_Webpos_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report';
        $this->_blockGroup = 'webpos';
        $this->_headerText = Mage::helper('webpos')->__('Web POS Sales Report');
        $this->_removeButton('add');
        parent::__construct();

    }

    /**
     * get a list of staff report controllers and names
     *
     * @return array
     */
    public function getStaffReportList(){
        return array(
            'sales_staff'                   => __('Sales by staff'),
            'sales_staff_daily'             => __('Sales by staff (Daily)'),
            'sales_staff_order_list'        => __('Order list by staff')
        );
    }

    /**
     * get a list of location report controllers and names
     *
     * @return array
     */
    public function getLocationReportList(){
        return  array(
            'sales_location'         => __('Sales by location'),
            'sales_location_daily'    => __('Sales by location (Daily)'),
            'sales_location_order_list'    => __('Order list by location')
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
                'sales_payment'          => __('Sales by payment method'),
                'sales_payment_daily'     => __('Sales by payment method (Daily)'),
                'sales_payment_order_list'     => __('Order list by payment method'),
                'sales_payment_location'  => __('Sales by payment method for location')
            );
        }
        return $this->_reportList;
    }

    public function getReportList(){
//        if(!$this->_reportList){
//            $this->_reportList = array_merge(
//                $this->getStaffReportList(),
//                $this->getLocationReportList(),
//                $this->getPaymentReportList()
//            );
//        }
//        return $this->_reportList;
    }

    /**
     * get report link from name
     *
     * @param string
     * @return string
     */
    public function getReportLink($controller){
        $path = 'adminhtml/report_'. $controller;
        return $this->getUrl($path, array('_forced_secure' => $this->getRequest()->isSecure()));
    }

    /**
     * get current report name
     *
     * @param
     * @return string
     */
    public function getCurrentReportName(){
//        $controller = $this->getRequest()->getControllerName();
//        $controller = str_replace('report_', '', $controller);
//        $reportList = $this->getReportList();
//        $reportName = '';
//        if(isset($reportList[$controller]))
//            $reportName = $reportList[$controller];
//        return $reportName;
    }
}