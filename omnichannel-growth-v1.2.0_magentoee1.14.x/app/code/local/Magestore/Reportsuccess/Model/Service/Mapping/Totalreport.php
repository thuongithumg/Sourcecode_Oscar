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

class Magestore_Reportsuccess_Model_Service_Mapping_Totalreport
{
    /**
     * table inventory_report_collection
     */
    const TEMP_TABLE = 'inventory_report_collection';

    /**
     * on hand fields
     */
    static function _mapping_field_on_hand(){
        return array(
            'Total Qty' => 'sum_total_qty',
            'Inventory Value' => 'total_inv_value',
            'Potential Revenue' => 'total_retail_value',
            'Potential Profit' => 'total_profit',
        );
    }

    /**
     * sales fields
     */
    static function _mapping_field_on_sales(){
        return array(
            'Total Actual Sold Qty' => 'sum_realized_sold_qty',
            'Total Potential Sold Qty' => 'sum_potential_sold_qty',
            'Total Actual Profit' => 'sum_realized_profit',
            'Total Potential Profit' => 'sum_potential_profit',
            'Total Sales' => 'sum_total_sale',

        );
    }

    /**
     * details fileds
     */
    static function _mapping_field_details(){
        return array(
            'Total Qty' => 'sum_total_qty',
            'Total Stock Available' => 'available_qty',
            'Total Qty To Ship' => 'sum_qty_to_ship',
            'Total Qty On Purchase' => 'qty_in_order',
        );
    }
    /**
     * incoming fields
     */
    static function _mapping_field_incoming(){
        return array(
            'Incoming Stock' => 'total_in_coming_group',
            'Total Cost by Supplier' => 'total_cost_group',
        );
    }

    /**
     *   on hand report
     */
    const FIELD_ON_HAND = 'sum(sum_total_qty) as sum_total_qty,
                                sum(total_inv_value) as total_inv_value,
                                sum(total_retail_value) as total_retail_value,
                                sum(total_profit) as total_profit';
    /**
     *   details report
     */
    const FIELD_DETAILS = 'sum(sum_total_qty) as sum_total_qty,
                                sum(available_qty) as available_qty,
                                sum(sum_qty_to_ship) as sum_qty_to_ship,
                                sum(qty_in_order) as qty_in_order';
    /**
     *   incoming report
     */
    const FIELD_INCOMING = 'sum(total_cost_group) as total_cost_group,
                                sum(total_in_coming_group) as total_in_coming_group,
                                supplier_name as supplier';
    /**
     *  sales report
     */
    const FIELD_ON_SALES = 'sum(realized_sold_qty) as sum_realized_sold_qty,
                                sum(potential_sold_qty) as sum_potential_sold_qty,
                                sum(realized_profit) as sum_realized_profit,
                                sum(potential_profit) as sum_potential_profit,
                                sum(total_sale) as sum_total_sale';

    /**
     *
     */
    const ON_HAND = Magestore_Reportsuccess_Helper_Data::STOCK_ON_HAND;
    /**
     *
     */
    const DETAILS = Magestore_Reportsuccess_Helper_Data::DETAILS;
    /**
     *
     */
    const INCOMING_STOCK = Magestore_Reportsuccess_Helper_Data::INCOMING_STOCK;

    /**
     * css
     */
    const CSS = "<style>
                .data-well-level1 {
                padding: 0 20px 20px;
                width:50%;
                color:#5A7FA2;
                margin-top : 20px;
                margin-bottom : 40px;
                float: left;
                text-align: center;
                border: 1px solid #E9E9E9;
                float: left;
            }
            .data-well-level2 {
            /*color:#1b1119;
                padding: 0 10px 20px;*/
                text-align: center;
                display: inline-block;
            }
            h2{
              margin-left : 30px !important;
              margin-bottom: 0em !important;
            }
            .row-fluid {
              display: flex; /* equal height of the children */
            }

            .data-well-level1 {
              flex: 1; /* additionally, equal width */
               position: relative;
            }
            .translation_missing{
              /*position: absolute;
              bottom:0;*/
              text-align:center !important;
            }
            .translation_missing_lv2{
             font-size: 14px !important;
            }

            </style>
        ";
}