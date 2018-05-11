/*
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

define(
    [
        'require',
        'model/url-builder',
        'model/checkout/cart/items/item',
        // 'model/catalog/product',
        // 'model/catalog/product/type/simple',
        // 'model/catalog/product/type/configurable',
        // 'model/catalog/product/type/bundle',
        // 'model/catalog/product/type/grouped',
        // 'model/catalog/product/type/downloadable',
        'model/catalog/category',
        'model/customer/customer',
        // 'model/customer/group',
        'model/inventory/stock-item',
        // 'model/inventory/location',
        // 'model/checkout/shipping',
        // 'model/checkout/payment',
        'model/sales/order',
        'model/directory/country',
        'model/directory/currency',
        'model/config/config',
        'model/config/local-config',
        // 'model/checkout/taxrate',
        'model/checkout/cart/totals',
        'model/checkout/cart/totals/total',
        // 'model/customer/complain',
        'model/synchronization/synchronization',
        // 'model/checkout/taxclass',
        'model/checkout/cart/customsale',
        'model/log/action-log',
        // 'model/checkout/taxrule',
        // 'model/shift/shift',
        'model/abstract',
        // 'model/checkout/integration/store-credit',
        // 'model/checkout/integration/reward-points',
        // 'model/checkout/integration/gift-card',
        // 'model/checkout/integration/rewardpoints/rate',
        // 'model/catalog/product/swatch',
        'model/checkout/cart/editpopup',
        // 'model/checkout/taxcalculator',
        // 'model/catalog/product/type/storecredit',
    ],
    function (  require,
                model_urlbuilder,
                model_cart_item,
                // model_catalog_product,
                // model_catalog_product_type_simple,
                // model_catalog_product_type_configurable,
                // model_catalog_product_type_bundle,
                // model_catalog_product_type_grouped,
                // model_catalog_product_type_downloadable,
                model_catalog_category,
                model_customer_customer,
                // model_customer_group,
                model_inventory_stockitem,
                // model_inventory_location,
                // model_checkout_shipping,
                // model_checkout_payment,
                model_sales_order,
                model_directory_country,
                model_directory_currency,
                model_config_config,
                model_config_localconfig,
                // model_checkout_taxrate,
                model_checkout_cart_totals,
                model_cart_total,
                // model_customer_complain,
                model_synchronization_synchronization,
                // model_checkout_taxclass,
                model_checkout_cart_customsale,
                model_log_actionlog,
                // model_checkout_taxrule,
                // model_shift_shift,
                model_abstract,
                // model_checkout_integration_storecredit,
                // model_checkout_integration_rewardpoints,
                // model_checkout_integration_giftcard,
                // model_checkout_integration_rewardpoints_rate,
                // model_catalog_product_swatch,
                model_checkout_cart_editpopup
                // model_checkout_taxcalculator,
                // model_catalog_product_type_storecredit
        ) {
        "use strict";

        return {

            /**
             * get object singleton
             *
             * @param string modelName
             * @returns {object}
             */
            get: function(modelName) {

                var model = this._convertModelPath(modelName);

                if(!window.webposObjects) {
                    window.webposObjects = {};
                }
                if(!window.webposObjects[model]) {
                     var modelClass = require(modelName);
                    window.webposObjects[model] = modelClass();
                }
                return window.webposObjects[model];
            },

            /**
             * create new object
             *
             * @param string modelName
             * @returns {object}
             */
            create: function(modelName) {
                var modelClass = require(modelName);
                return modelClass();
            },
            
            /**
             * convert model name to key
             * 
             * @param string modelName
             * @returns string
             */
            _convertModelPath: function(modelName) {
                for(var i=0; i<5; i++) {
                    modelName = modelName.replace('/', '_');
                    modelName = modelName.replace('-', '');
                }      
                return modelName;
            }
        };
    }
);