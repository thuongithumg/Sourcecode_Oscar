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

class Magestore_Webpos_Model_Config_Tax extends Magestore_Webpos_Model_Abstract
{
    public function getConfig()
    {
        $output = array();

        $paths = array(
            'tax/classes/shipping_tax_class',
            'tax/classes/default_product_tax_class',
            'tax/classes/default_customer_tax_class',
            'tax/calculation/algorithm',
            'tax/calculation/based_on',
            'tax/calculation/price_includes_tax',
            'tax/calculation/shipping_includes_tax',
            'tax/calculation/apply_after_discount',
            'tax/calculation/discount_tax',
            'tax/calculation/apply_tax_on',
            'tax/calculation/cross_border_trade_enabled',
            'tax/cart_display/price',
            'tax/cart_display/subtotal',
            'tax/cart_display/shipping',
            'tax/display/type',
            'tax/display/shipping',
            'tax/sales_display/price',
            'tax/sales_display/subtotal',
            'tax/sales_display/shipping'
        );
        $data = array();
        if(count($paths)) {
            foreach($paths as $path) {
                $value = Mage::helper('webpos/config')->getStoreConfig($path);
                $data[$path] = $value;
            }
        }

        if(!empty($data)){
            $output = $data;
        }
        return $output;
    }

}
