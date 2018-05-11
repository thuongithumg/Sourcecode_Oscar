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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_ImportType extends Mage_Core_Model_Abstract
{
    const TYPE_ADJUST_STOCK                     = 1;
    const TYPE_TRANSFER_STOCK_SEND              = 2;
    const TYPE_TRANSFER_STOCK_SEND_RECEIVING    = 3;
    const TYPE_TRANSFER_STOCK_REQUEST           = 4;
    const TYPE_TRANSFER_STOCK_REQUEST_DELIVERY  = 5;
    const TYPE_TRANSFER_STOCK_REQUEST_RECEIVING = 6;
    const TYPE_TRANSFER_STOCK_EXTERNAL_TO       = 7;
    const TYPE_TRANSFER_STOCK_EXTERNAL_FROM     = 8;
    const TYPE_STOCKTAKING                      = 9;

    const INVALID_ADJUST_STOCK                     = 'import_product_to_adjust_stock_invalid.csv';
    const INVALID_TRANSFER_STOCK_SEND              = 'import_product_to_transfer_stock_send_invalid.csv';
    const INVALID_TRANSFER_STOCK_SEND_RECEIVING    = 'import_product_to_transfer_stock_send_receiving_invalid.csv';
    const INVALID_TRANSFER_STOCK_REQUEST           = 'import_product_to_transfer_stock_request_invalid.csv';
    const INVALID_TRANSFER_STOCK_REQUEST_DELIVERY  = 'import_product_to_transfer_stock_delivery_invalid.csv';
    const INVALID_TRANSFER_STOCK_REQUEST_RECEIVING = 'import_product_to_transfer_stock_receiving_invalid.csv';
    const INVALID_TRANSFER_STOCK_EXTERNAL_TO       = 'import_product_to_transfer_stock_external_invalid.csv';
    const INVALID_TRANSFER_STOCK_EXTERNAL_FROM     = 'import_product_to_transfer_stock_external_invalid.csv';
    const INVALID_STOCKTAKING                      = 'import_product_to_stocktake_invalid.csv';
}

