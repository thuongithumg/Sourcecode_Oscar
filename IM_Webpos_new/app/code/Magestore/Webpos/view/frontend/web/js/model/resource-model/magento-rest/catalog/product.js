/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
        'Magestore_Webpos/js/model/config/local-config',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, onlineAbstract, localConfig, Event) {
        "use strict";

        return onlineAbstract.extend({
            initialize: function () {
                this._super();
                this.setLoadApi('/webpos/products/:productId?productId=');
                //this.setCreateApiUrl('/webpos/products');
                //this.setUpdateApiUrl('/webpos/customers/');
                //this.setDeleteApiUrl('/webpos/customers/:customerId?customerId=');
                this.initSearchApiUrl();
                this.initEvents();
            },
            initEvents: function(){
                var self = this;
                Event.observer('webpos_config_change_after', function(event, data){
                    if(data.config && (data.config.configPath == 'catalog/outstock-display')){
                        self.initSearchApiUrl();
                    }
                });
            },
            initSearchApiUrl: function(){
                var searchApiUrl = "/webpos/products";
                if(localConfig.get('catalog/outstock-display') == 1){
                    searchApiUrl += "?show_out_stock=1";
                }else{
                    searchApiUrl += "?show_out_stock=0";
                }
                this.setSearchApiUrl(searchApiUrl);
            }
        });
    }
);