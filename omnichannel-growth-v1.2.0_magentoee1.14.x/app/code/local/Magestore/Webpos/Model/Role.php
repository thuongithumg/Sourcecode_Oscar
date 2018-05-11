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
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:53 SA
 *
 * Edited by NetBeans.
 * User: Daniel
 * Date: 21/01/2016
 * Time: 05:49 PM
 */
class Magestore_Webpos_Model_Role extends Mage_Core_Model_Abstract {

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
    const CAN_USE_XREPORT = 14;
    const CAN_USE_ZREPORT = 15;
    const CAN_USE_EODREPORT = 16;
    const CAN_USE_ALL_REPORT = 17;
    const CAN_USE_STORE_CREDIT_REFUND= 18;
    const CAN_USE_REFUND = 19;

    const MANAGE_ORDER_LOCATION = 20;
    const SESSION_MAKE_ADJUSTMENT = 21;
    const SESSION_CAN_OPEN = 22;
    const SESSION_CAN_CLOSE = 23;

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/role');
    }

    public function toOptionArray() {
        $options = array();
        $roleCollection = $this->getCollection()->addFieldToFilter('active', 1);
        if ($roleCollection->getSize() > 1) {
            $options = array('' => '---Select Role---');
        }
        foreach ($roleCollection as $role) {
            $key = $role->getId();
            $value = $role->getDisplayName();
            $options [$key] = $value;
        }
        return $options;
    }

    public function getMaximumDiscountPercent() {
        return $this->getData('maximum_discount_percent');
    }

    public function getOptionArray() {

        return array(
            self::ALL_PERMISSION => 'Magestore_Webpos::all',
            self::CREATE_ORDER => 'Magestore_Webpos::create_orders',
            self::VIEW_ORDER_THIS_USER => 'Magestore_Webpos::view_order_me',
            self::VIEW_ORDER_OTHER_STAFF => 'Magestore_Webpos::view_order_other_staff',
            self::VIEW_ORDER_ALL_ORDER => 'Magestore_Webpos::view_all_order',

            self::MANAGE_ORDER_THIS_USER => 'Magestore_Webpos::manage_order_me',
            self::MANAGE_ORDER_OTHER_STAFF => 'Magestore_Webpos::manage_order_other_staff',
            self::MANAGE_ORDER_ALL_ORDER => 'Magestore_Webpos::manage_all_order',
            self::MANAGE_ORDER_LOCATION => 'Magestore_Webpos::manage_order_location',

            self::CAN_USE_CART_CUSTOM_DISCOUNT => 'Magestore_Webpos::apply_discount_per_cart',
            self::CAN_USE_CART_COUPON_CODE => 'Magestore_Webpos::apply_coupon',
            self::CAN_USE_DISCOUNT_PER_ITEM => 'Magestore_Webpos::apply_discount_per_item',
            self::CAN_USE_CUSTOM_PRICE => 'Magestore_Webpos::apply_custom_price',
            self::CAN_USE_All_DISCOUNT => 'Magestore_Webpos::all_discount',

            self::CAN_USE_XREPORT => 'Magestore_Webpos::can_use_xreport',
            self::CAN_USE_ZREPORT => 'Magestore_Webpos::can_use_zreport',
            self::CAN_USE_EODREPORT => 'Magestore_Webpos::can_use_eodreport',
            self::CAN_USE_ALL_REPORT => 'Magestore_Webpos::can_use_all_report',
            self::CAN_USE_STORE_CREDIT_REFUND => 'Magestore_Webpos::can_use_store_credit_refund',
            self::CAN_USE_REFUND => 'Magestore_Webpos::can_use_refund',
            self::SESSION_MAKE_ADJUSTMENT => 'Magestore_Webpos::manage_shift_adjustment',
            self::SESSION_CAN_OPEN => 'Magestore_Webpos::open_shift',
            self::SESSION_CAN_CLOSE => 'Magestore_Webpos::close_shift'
        );
    }

}
