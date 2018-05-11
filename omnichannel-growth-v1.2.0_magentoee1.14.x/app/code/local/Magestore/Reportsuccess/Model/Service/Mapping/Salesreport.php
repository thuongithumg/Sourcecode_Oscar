<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * ReportSuccess Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Model_Service_Mapping_Salesreport
{

    /**
     * session name
     */
    const _REMAIN_SIZE_SESSION = 'sale_remain_size_session';

    /**
     * variable for ajax
     */
    const _TOTAL_SIZE = 'totalSize';
    const _REMAIN_SIZE = 'remain_size';
    const _UPDATE_TIME = 'updated_time';


    /**
     * Sql condition
     */
    const _WHERE_CONDITION = "TIMESTAMPDIFF(SECOND,
                                            IFNULL(main_table.updated_at,'2000-03-15 07:01:35'),
                                            IFNULL(sales_report.updated_at,'2001-03-15 07:01:35'))";


    /**
     *  tables
     */
    const _ORDER_ITEM_TABLE = 'sales_flat_order_item';
    const _REPORT_TABLE = 'os_salesreport_report';
    const _PAYMENT_TABLE = 'sales_flat_order_payment';
    const _ORDER_TABLE = 'sales_flat_order';


    /**
     * joinLeft  : main_table and sales_report
     */
    const _JOINLEFT_MAINTABLE_SALESREPORT_CONDITION = 'main_table.product_id = sales_report.product_id
                                                        and main_table.order_id = sales_report.order_id
                                                        and main_table.item_id = sales_report.item_id';

    const _JOINLEFT_MAINTABLE_SALESREPORT_CONDITION_CONFIG = 'main_table.order_id = sales_report.order_id
                                                        and main_table.item_id = sales_report.item_id';

    /**
     * @return array
     */
    static function _joinleft_maintable_salesreport_array()
    {
       return array(
            'updated_at_main_table' => 'IFNULL(main_table.updated_at,"2000-03-15 07:01:35")',
            'updated_at_report_table' => 'IFNULL(sales_report.updated_at,"2001-03-15 07:01:35")',
            'sales_report_id' => 'IF(sales_report.id,sales_report.id,0)',
            'items_to_delete' => ('GROUP_CONCAT(DISTINCT sales_report.id SEPARATOR ",")')
        );
    }


    /**
     * joinLeft  : main_table and payment_method
     */
    const _JOINLEFT_MAINTABLE_PAYMENT_CONDITION = 'main_table.order_id = payment_method.parent_id';

    /**
     * @return array
     */
    static function _joinleft_maintable_payment_array(){
        return array(
            'payment_method' => ('GROUP_CONCAT(DISTINCT payment_method.method SEPARATOR ",")')
        );
    }


    /**
     * joinLeft  : main_table and order_table
     */
    const _JOINLEFT_MAINTABLE_ORDER_CONDITION = 'main_table.order_id = order.entity_id';

    /**
     * @return array
     */
    static function _joinleft_maintable_order_array() {
        return array(
            'increment_id' => 'order.increment_id',
            'status' => 'order.status',
            'shipping_method' => 'order.shipping_method',
            'shipping_description' => 'order.shipping_description',
            'customer_group_id' => 'order.customer_group_id',
            'customer_email' => 'order.customer_email',
            'customer_firstname' => 'order.customer_firstname',
            'customer_lastname' => 'order.customer_lastname',
            'customer_middlename' => 'order.customer_middlename',
            'created_at' =>  'order.created_at',
        );
    }

}