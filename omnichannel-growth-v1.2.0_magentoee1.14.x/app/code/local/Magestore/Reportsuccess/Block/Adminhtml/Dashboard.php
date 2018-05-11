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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 *
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template {


    const INVENTORY_REPORT = 'stockonhand';
    const SALES_REPORT = 'sales';
    /**
     * Get reports by type
     *
     * @param string $type
     * @return array
     */
    public function getReports($type = null) {
        return $this->helper('reportsuccess')->getReports($type);
    }

    /**
     * Get report url
     *
     * @param array $report
     * @return string
     */
    public function getReportUrl($type, $report) {
        if($type == self::INVENTORY_REPORT) {
            $code = $report['code'];
            $report_header = Mage::helper('reportsuccess')->base64Encode($code);
            $Url = Mage::helper('adminhtml')->getUrl('adminhtml/inventoryreport_' . $code . '/index',array("report"=>$report_header));
            return $Url;
        }
        if($type == self::SALES_REPORT) {
            $code = $report['code'];
            $Url = Mage::helper('adminhtml')->getUrl('adminhtml/salesreport_index/index/type/'.$code);
            return $Url;
        }
        return '#';
    }
}