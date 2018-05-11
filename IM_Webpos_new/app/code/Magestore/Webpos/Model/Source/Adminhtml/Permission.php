<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Permission
 * 
 * Web POS Permission source model
 * Methods:
 *  getOptionArray
 *  getStoreValuesForForm
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Permission implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Full permission
     */
    const ALL_PERMISSION = 1;
    
    /**
     * Create order
     */
    const CREATE_ORDER = 2;
    
    /**
     * View order created by this user
     */
    const VIEW_ORDER_THIS_USER = 3;
    
    /**
     * View order created by other user
     */
    const VIEW_ORDER_OTHER_STAFF = 4;
    
    /**
     * View all order
     */
    const VIEW_ORDER_ALL_ORDER = 5;
    
    /**
     * Can manage orders of this user only
     */
    const MANAGE_ORDER_THIS_USER = 6;
    
    /**
     * Can manage orders of other user
     */
    const MANAGE_ORDER_OTHER_STAFF = 7;
    
    /**
     * Can manage all orders
     */
    const MANAGE_ORDER_ALL_ORDER = 8;
    
    /**
     * Can use cart custom discount
     */
    const CAN_USE_CART_CUSTOM_DISCOUNT = 9;
    
    /**
     * Can use cart coupon code
     */
    const CAN_USE_CART_COUPON_CODE = 10;
    
    /**
     * Can use discount per item
     */
    const CAN_USE_DISCOUNT_PER_ITEM = 11;
    
    /**
     * Can use cart item custom price
     */
    const CAN_USE_CUSTOM_PRICE = 12;
    
    /**
     * Can use all discount
     */
    const CAN_USE_All_DISCOUNT = 13;
    
    /**
     * Can use mid day report
     */
    const CAN_USE_XREPORT = 14;
    
    /**
     * Can use end of day report
     */
    const CAN_USE_ZREPORT = 15;
    
    /**
     * Can use daily report
     */
    const CAN_USE_EODREPORT = 16;
    
    /**
     * Can use all report
     */
    const CAN_USE_ALL_REPORT = 17;
    
    /**
     * Can refund by store credit
     */
    const CAN_USE_STORE_CREDIT_REFUND= 18;
    
    /**
     * Can refund order
     */
    const CAN_USE_REFUND = 19;


    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('value' => self::ALL_PERMISSION, 'label' => __('All Permissions')),
            array('value' => self::CREATE_ORDER, 'label' => __('Create Orders')),
            array('value' => self::VIEW_ORDER_THIS_USER, 'label' => __('View orders created by this user')),
            array('value' => self::VIEW_ORDER_OTHER_STAFF, 'label' => __('View orders created by other users')),
            array('value' => self::VIEW_ORDER_ALL_ORDER, 'label' => __('View all orders')),
            array('value' => self::MANAGE_ORDER_THIS_USER, 'label' => __('Manage orders created by this user')),
            array('value' => self::MANAGE_ORDER_OTHER_STAFF, 'label' => __('Manage orders created by other users')),
            array('value' => self::MANAGE_ORDER_ALL_ORDER, 'label' => __('Manage all orders')),
            array('value' => self::CAN_USE_CART_CUSTOM_DISCOUNT, 'label' => __('Apply custom discount per cart')),
            array('value' => self::CAN_USE_CART_COUPON_CODE, 'label' => __('Apply coupon codes')),
            array('value' => self::CAN_USE_DISCOUNT_PER_ITEM, 'label' => __('Apply custom discount per item')),
            array('value' => self::CAN_USE_CUSTOM_PRICE, 'label' => __('Apply custom price')),
            array('value' => self::CAN_USE_All_DISCOUNT, 'label' => __('Apply all discounts')),
            array('value' => self::CAN_USE_XREPORT, 'label' => __('X-Report (mid-day report)')),
            array('value' => self::CAN_USE_ZREPORT, 'label' => __('Z-Report (end-of-day report)')),
            array('value' => self::CAN_USE_EODREPORT, 'label' => __('Daily POS Orders Summary')),
            array('value' => self::CAN_USE_ALL_REPORT, 'label' => __('All Reports')),
            array('value' => self::CAN_USE_STORE_CREDIT_REFUND, 'label' => __('Refund by store credit(Store Credit extension)')),
            array('value' => self::CAN_USE_REFUND, 'label' => __('Refund'))
        );
        return $options;
    }

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        $options = array(
            self::ALL_PERMISSION  => __('All Permissions'),
            self::CREATE_ORDER  => __('Create Orders'),
            self::VIEW_ORDER_THIS_USER  => __('View orders created by this user'),
            self::VIEW_ORDER_OTHER_STAFF => __('View orders created by other users'),
            self::VIEW_ORDER_ALL_ORDER => __('View all orders'),
            self::MANAGE_ORDER_THIS_USER => __('Manage orders created by this user'),
            self::MANAGE_ORDER_OTHER_STAFF => __('Manage orders created by other users'),
            self::MANAGE_ORDER_ALL_ORDER => __('Manage all orders'),
            self::CAN_USE_CART_CUSTOM_DISCOUNT => __('Apply custom discount per cart'),
            self::CAN_USE_CART_COUPON_CODE => __('Apply coupon codes'),
            self::CAN_USE_DISCOUNT_PER_ITEM => __('Apply custom discount per item'),
            self::CAN_USE_CUSTOM_PRICE => __('Apply custom price'),
            self::CAN_USE_All_DISCOUNT => __('Apply all discounts'),
            self::CAN_USE_XREPORT => __('X-Report (mid-day report)'),
            self::CAN_USE_ZREPORT => __('Z-Report (end-of-day report)'),
            self::CAN_USE_EODREPORT => __('Daily POS Orders Summary'),
            self::CAN_USE_ALL_REPORT => __('All Reports'),
            self::CAN_USE_STORE_CREDIT_REFUND => __('Refund by store credit(Store Credit extension)'),
            self::CAN_USE_REFUND => __('Refund')
        );
        return $options;
    }
    
    /**
     * 
     * @return array
     */
    public static function getStoreValuesForForm() {
        $options = array(
            array('value' => '1', 'label' => __('All Permissions')),
            array('value' => array(
                array('value' => '2', 'label' => __('Create Orders')),
            ), 'label' => __('Create Orders')),
            array('value' => array(
                array('value' => '3', 'label' => __('Created by this user')),
                array('value' => '4', 'label' => __('Created by other staff')),
                array('value' => '5', 'label' => __('All orders')),
            ), 'label' => __('View Orders')),
            array('value' => array(
                array('value' => '6', 'label' => __('Manage orders created by this user')),
                array('value' => '7', 'label' => __('Manage orders created by other users')),
                array('value' => '8', 'label' => __('Manage all orders')),
            ), 'label' => __('Manage Orders')),
            array('value' => array(
                array('value' => '9', 'label' => __('Apply custom discount per cart')),
                array('value' => '10', 'label' => __('Apply coupon codes')),
                array('value' => '11', 'label' => __('Apply custom discount per item')),
                array('value' => '12', 'label' => __('Apply custom price')),
                array('value' => '13', 'label' => __('Apply all discounts')),
            ), 'label' => __('Manage Discounts')),
            array('value' => array(
                array('value' => '14', 'label' => __('X-Report (mid-day report)')),
                array('value' => '15', 'label' => __('Z-Report (end-of-day report)')),
                array('value' => '16', 'label' => __('Daily POS Orders Summary')),
                array('value' => '17', 'label' => __('All Reports')),
            ), 'label' => __('View Reports')),
            array('value' => array(
                array('value' => '18', 'label' => __('Refund by store credit(Store Credit extension)')),
                array('value' => '19', 'label' => __('Refund')),
            ), 'label' => __('Manage Refunds'))
        );
        return $options;
    }
}
