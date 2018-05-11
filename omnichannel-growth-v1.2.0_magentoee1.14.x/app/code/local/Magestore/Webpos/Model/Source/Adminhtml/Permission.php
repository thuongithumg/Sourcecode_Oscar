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

class Magestore_Webpos_Model_Source_Adminhtml_Permission extends Varien_Object {

    const ALL_PERMISSION = 1;
    const CREATE_ORDER = 2;
    const VIEW_ORDER_THIS_USER = 3;
    const VIEW_ORDER_OTHER_STAFF = 4;
    const VIEW_ORDER_ALL_ORDER = 5;
    const MANAGE_ORDER_THIS_USER = 6;
    const MANAGE_ORDER_OTHER_STAFF = 7;
    const MANAGE_ORDER_ALL_ORDER = 8;
    const CAN_USE_CART_CUSTOM_DISCOUNT = 9;
    const CAN_USE_CART_COUPON_CODE = 10;
    const CAN_USE_DISCOUNT_PER_ITEM = 11;
    const CAN_USE_CUSTOM_PRICE = 12;
    const CAN_USE_All_DISCOUNT = 13;
    const CAN_USE_STORE_CREDIT_REFUND= 18;
    const CAN_USE_REFUND = 19;

    const MANAGE_ORDER_LOCATION = 20;
    const MANAGE_SHIFT_ADJUSTMENT = 21;
    const MANAGE_SHIFT_OPEN = 22;
    const MANAGE_SHIFT_CLOSE = 23;

    static public function getOptionArray() {

        return array(
            '1' => Mage::helper('webpos')->__('All Permissions'),
//            '2' => Mage::helper('webpos')->__('Create Orders'),
//            '3' => Mage::helper('webpos')->__('View orders created by this user'),
//            '4' => Mage::helper('webpos')->__('View orders created by other users'),
//            '5' => Mage::helper('webpos')->__('View all orders'),
            '6' => Mage::helper('webpos')->__('Manage orders created by this user'),
//            '7' => Mage::helper('webpos')->__('Manage orders created by other users'),
            '8' => Mage::helper('webpos')->__('Manage all orders'),
            '9' => Mage::helper('webpos')->__('Apply custom discount per cart'),
            '10' => Mage::helper('webpos')->__('Apply coupon codes'),
            '11' => Mage::helper('webpos')->__('Apply custom discount per item'),
            '12' => Mage::helper('webpos')->__('Apply custom price'),
            '13' => Mage::helper('webpos')->__('Apply all discounts'),
//            '18' => Mage::helper('webpos')->__('Refund by store credit(Store Credit extension)'),
            '19' => Mage::helper('webpos')->__('Refund'),

            self::MANAGE_SHIFT_ADJUSTMENT => Mage::helper('webpos')->__('Can Make Shift Adjustment'),
            self::MANAGE_SHIFT_OPEN => Mage::helper('webpos')->__('Open Shift'),
            self::MANAGE_SHIFT_CLOSE => Mage::helper('webpos')->__('Close Shift'),
        );
    }

    static public function toOptionArray() {
        $options = array(
            array('value' => '1', 'label' => Mage::helper('webpos')->__('All Permissions')),
//            array('value' => '2', 'label' => Mage::helper('webpos')->__('Create Orders')),
//            array('value' => '3', 'label' => Mage::helper('webpos')->__('View orders created by this user')),
//            array('value' => '4', 'label' => Mage::helper('webpos')->__('View orders created by other users')),
//            array('value' => '5', 'label' => Mage::helper('webpos')->__('View all orders')),
            array('value' => '6', 'label' => Mage::helper('webpos')->__('Manage orders created by this user')),
//            array('value' => '7', 'label' => Mage::helper('webpos')->__('Manage orders created by other users')),
            array('value' => '8', 'label' => Mage::helper('webpos')->__('Manage all orders')),
            array('value' => '9', 'label' => Mage::helper('webpos')->__('Apply custom discount per cart')),
            array('value' => '10', 'label' => Mage::helper('webpos')->__('Apply coupon codes')),
            array('value' => '11', 'label' => Mage::helper('webpos')->__('Apply custom discount per item')),
            array('value' => '12', 'label' => Mage::helper('webpos')->__('Apply custom price')),
            array('value' => '13', 'label' => Mage::helper('webpos')->__('Apply all discounts')),
            array('value' => '18', 'label' => Mage::helper('webpos')->__('Refund by store credit(Store Credit extension)')),
            array('value' => '19', 'label' => Mage::helper('webpos')->__('Refund')),
            array('value' => self::MANAGE_SHIFT_ADJUSTMENT, 'label' => Mage::helper('webpos')->__('Can Make Shift Adjustment')),
            array('value' => self::MANAGE_SHIFT_OPEN, 'label' => Mage::helper('webpos')->__('Open Shift')),
            array('value' => self::MANAGE_SHIFT_CLOSE, 'label' => Mage::helper('webpos')->__('Close Shift')),
        );
        return $options;
    }

    static public function getStoreValuesForForm() {
        $options = array(
            array('value' => '1', 'label' => Mage::helper('webpos')->__('All Permissions')),
//            array('value' => array(
//                    array('value' => '2', 'label' => Mage::helper('webpos')->__('Create Orders')),
//                ), 'label' => Mage::helper('webpos')->__('Create Orders')),
//            array('value' => array(
//                    array('value' => '3', 'label' => Mage::helper('webpos')->__('Created by this user')),
//                    array('value' => '4', 'label' => Mage::helper('webpos')->__('Created by other staff')),
//                    array('value' => '5', 'label' => Mage::helper('webpos')->__('All orders')),
//                ), 'label' => Mage::helper('webpos')->__('View Orders')),
            array('value' => array(
                    array('value' => '6', 'label' => Mage::helper('webpos')->__('Manage orders created by this user')),
//                    array('value' => '7', 'label' => Mage::helper('webpos')->__('Manage orders created by other users')),
                    array('value' => '8', 'label' => Mage::helper('webpos')->__('Manage all orders')),
                ), 'label' => Mage::helper('webpos')->__('Manage Orders')),
            array('value' => array(
                    array('value' => '9', 'label' => Mage::helper('webpos')->__('Apply custom discount per cart')),
                    array('value' => '10', 'label' => Mage::helper('webpos')->__('Apply coupon codes')),
                    array('value' => '11', 'label' => Mage::helper('webpos')->__('Apply custom discount per item')),
                    array('value' => '12', 'label' => Mage::helper('webpos')->__('Apply custom price')),
                    array('value' => '13', 'label' => Mage::helper('webpos')->__('Apply all discounts')),
                ), 'label' => Mage::helper('webpos')->__('Manage Discounts')),
            array('value' => array(
//                    array('value' => '18', 'label' => Mage::helper('webpos')->__('Refund by store credit(Store Credit extension)')),
                    array('value' => '19', 'label' => Mage::helper('webpos')->__('Refund')),
                ), 'label' => Mage::helper('webpos')->__('Manage Refunds')),
            array('value' => array(
                array('value' => self::MANAGE_SHIFT_ADJUSTMENT, 'label' => Mage::helper('webpos')->__('Can Make Shift Adjustment')),
                array('value' => self::MANAGE_SHIFT_OPEN, 'label' => Mage::helper('webpos')->__('Open Shift')),
                array('value' => self::MANAGE_SHIFT_CLOSE, 'label' => Mage::helper('webpos')->__('Close Shift')),
            ), 'label' => Mage::helper('webpos')->__('Manage Shift')),
        );
        return $options;
    }

}
