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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Mysql4_Store
 */
class Magestore_Storepickup_Model_Mysql4_Store extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        // Note that the storepickup_id refers to the key field in your database table.
        $this->_init('storepickup/store', 'store_id');
    }

    /**
     * @param $date
     * @param $store_id
     * @param $spfrom
     * @param $spto
     * @return array
     */
    public function getValidSpecialTime($date, $store_id, $spfrom, $spto) {
        
        //$listTime = array();
        if (Mage::getModel('storepickup/specialday')->isSpecialday($date, $store_id)) {

            /*$dateformat = Mage::helper('storepickup')->getDateFormat();

            switch ($dateformat) {
                case '%Y-%m-%d':
                    $yyy = substr($date, 0, 4);
                    $mm = substr($date, 5, 2);
                    $dd = substr($date, 8, 2);
                    $date = $yyy . '-' . $mm . '-' . $dd;
                    break;
                case '%d-%m-%Y':
                    $yyy = substr($date, 6, 4);
                    $dd = substr($date, 3, 2);
                    $mm = substr($date, 0, 2);
                    $date = $yyy . '-' . $mm . '-' . $dd;
                    break;
                case '%Y-%d-%m':
                    $yyy = substr($date, 0, 4);
                    $mm = substr($date, 8, 2);
                    $dd = substr($date, 5, 2);
                    $date = $yyy . '-' . $mm . '-' . $dd;
                    break;
                default:
                    $date = substr($date, 6, 4) . '-' . substr($date, 0, 2) . '-' . substr($date, 3, 2);
                    break;
            }*/


            //$timestamp = strtotime($date);

            $prefixTable = Mage::helper('storepickup')->getTablePrefix();
            //$date_field = date('l', $timestamp);
            //$date_field = strtolower($date_field);

            $sql = $this->_getReadAdapter()->select()
                    ->distinct()
                    ->from(array('ss' => $prefixTable . 'storepickup_specialday'), array("specialday_time_open as open_time", "specialday_time_close as close_time", "specialday_time_interval as interval_time"))
                    ->where('date = ?',$spfrom)
                    ->where('specialday_date_to = ?',$spto)
                    ->where('FIND_IN_SET(?, store_id)', $store_id);
            //'store_id = ?',$store_id);

            $options = $this->_getReadAdapter()->fetchAll($sql);

            return $options;
        } else {
            return array();
        }
    }

    /**
     * @param $date
     * @param $store_id
     * @return array
     */
    public function getValidTime($date, $store_id) {

        if (Mage::getModel('storepickup/holiday')->isHoliday($date, $store_id))
            return array();
        //$listTime = array();
        //prepare sql
        $date = substr($date, 6, 4) . '-' . substr($date, 0, 3) . substr($date, 3, 2);
        $timestamp = strtotime($date);
        $prefixTable = Mage::helper('storepickup')->getTablePrefix();
        $date_field = date('l', $timestamp);
        $date_field = strtolower($date_field);

        $sql = $this->_getReadAdapter()->select()
                ->distinct()
                ->from(array('ss' => $prefixTable . 'storepickup_store'), array($date_field . "_open as open_time",$date_field . "_open_break as open_time_break",$date_field . "_close_break as close_time_break", $date_field . "_close as close_time", $date_field . "_time_interval as interval_time", $date_field . "_status as status"))
                ->where('store_id=?', $store_id);

        $options = $this->_getReadAdapter()->fetchAll($sql);

        return $options;
    }

}
