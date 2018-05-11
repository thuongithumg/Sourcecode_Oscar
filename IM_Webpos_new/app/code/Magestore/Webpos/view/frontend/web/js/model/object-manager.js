/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'require',
        'Magestore_Webpos/js/model/url-builder',
        'Magestore_Webpos/js/model/checkout/cart/items/item',
        'Magestore_Webpos/js/model/catalog/product',
        'Magestore_Webpos/js/model/catalog/product/type/simple',
        'Magestore_Webpos/js/model/catalog/product/type/configurable',
        'Magestore_Webpos/js/model/catalog/product/type/bundle',
        'Magestore_Webpos/js/model/catalog/product/type/grouped',
        'Magestore_Webpos/js/model/catalog/product/type/downloadable',
        'Magestore_Webpos/js/model/catalog/category',
        'Magestore_Webpos/js/model/customer/customer',
        'Magestore_Webpos/js/model/customer/group',
        'Magestore_Webpos/js/model/inventory/stock-item',
        'Magestore_Webpos/js/model/inventory/location',
        'Magestore_Webpos/js/model/checkout/shipping',
        'Magestore_Webpos/js/model/checkout/payment',
        'Magestore_Webpos/js/model/sales/order',
        'Magestore_Webpos/js/model/directory/country',
        'Magestore_Webpos/js/model/directory/currency',
        'Magestore_Webpos/js/model/config/config',
        'Magestore_Webpos/js/model/config/local-config',
        'Magestore_Webpos/js/model/checkout/taxrate',
        'Magestore_Webpos/js/model/checkout/cart/totals',
        'Magestore_Webpos/js/model/checkout/cart/totals/total',
        'Magestore_Webpos/js/model/customer/complain',
        'Magestore_Webpos/js/model/synchronization/synchronization',
        'Magestore_Webpos/js/model/checkout/taxclass',
        'Magestore_Webpos/js/model/checkout/cart/customsale',
        'Magestore_Webpos/js/model/log/action-log',
        'Magestore_Webpos/js/model/checkout/taxrule',
        'Magestore_Webpos/js/model/shift/shift',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/checkout/integration/store-credit',
        'Magestore_Webpos/js/model/checkout/integration/storecredit-ee',
        'Magestore_Webpos/js/model/checkout/integration/reward-points',
        'Magestore_Webpos/js/model/checkout/integration/gift-card',
        'Magestore_Webpos/js/model/checkout/integration/rewardpoints/rate',
        'Magestore_Webpos/js/model/checkout/integration/giftcard/giftvoucher-template',
        'Magestore_Webpos/js/model/catalog/product/swatch',
        'Magestore_Webpos/js/model/checkout/cart/editpopup',
        'Magestore_Webpos/js/model/checkout/taxcalculator',
        'Magestore_Webpos/js/model/catalog/product/type/storecredit',
    ],
    function (  require,
                model_urlbuilder,
                model_cart_item, 
                model_catalog_product,
                model_catalog_product_type_simple,
                model_catalog_product_type_configurable,
                model_catalog_product_type_bundle,
                model_catalog_product_type_grouped,
                model_catalog_product_type_downloadable,
                model_catalog_category,
                model_customer_customer,
                model_customer_group,
                model_inventory_stockitem,
                model_inventory_location,
                model_checkout_shipping,
                model_checkout_payment,
                model_sales_order,
                model_directory_country,
                model_directory_currency,
                model_config_config,
                model_config_localconfig,
                model_checkout_taxrate,
                model_checkout_cart_totals,
                model_cart_total,
                model_customer_complain,
                model_synchronization_synchronization,
                model_checkout_taxclass,
                model_checkout_cart_customsale,
                model_log_actionlog,
                model_checkout_taxrule,
                model_shift_shift,
                model_abstract,
                model_checkout_integration_storecredit,
                model_checkout_integration_storecreditee,
                model_checkout_integration_rewardpoints,
                model_checkout_integration_giftcard,
                model_checkout_integration_rewardpoints_rate,
                model_checkout_integration_giftcard_giftvoucher_template,
                model_catalog_product_swatch,
                model_checkout_cart_editpopup,
                model_checkout_taxcalculator,
                model_catalog_product_type_storecredit
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
                     var modelClass = require('Magestore_Webpos/js/'+modelName);
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
                var modelClass = require('Magestore_Webpos/js/'+modelName);
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