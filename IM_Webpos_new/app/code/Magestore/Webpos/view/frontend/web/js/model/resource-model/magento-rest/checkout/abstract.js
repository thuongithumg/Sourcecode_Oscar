/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract'
    ],
    function ($, Helper, onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            initialize: function () {
                this._super();
            },
            getCallBackEvent: function(key){
            },
            setApiUrl: function(key,value){
            },
            getApiUrl: function(key){
            },
            /**
             * Function to process response data - update sections - online checkout
             * @param data
             */
            processResponseData: function(data){
                var self = this;
                if(data){
                    Helper.dispatchEvent('checkout_call_api_after', {
                        data: data
                    });
                    if (typeof data.quote_id != 'undefined' && !data.quote_id) {
                        data.quote_init = {quote_id: "", customer_id: ""};
                    }
                    if (data.quote_init && !data.quote_init.customer_id) {
                        data.quote_init.customer_id = "";
                    }
                    Helper.dispatchEvent('init_quote_online_after', {
                        data: data.quote_init
                    });
                    if(!data.increment_id) {
                        if (data.shipping) {
                            Helper.dispatchEvent('load_shipping_online_after', {
                                items: data.shipping
                            });
                        }
                        if (data.totals) {
                            Helper.dispatchEvent('load_totals_online_after', {
                                items: data.totals
                            });
                        }
                        if (data.payment) {
                            Helper.dispatchEvent('load_payment_online_after', {
                                items: data.payment
                            });
                        }
                        if (data.items&&data.quote_init) {
                            Helper.dispatchEvent('load_items_online_after', {
                                items: data.items
                            });
                            Helper.dispatchEvent('collect_totals', '');
                        }
                    }
                }

            }
        });
    }
);