/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'uiElement',
            'Magestore_Webpos/js/model/event-manager',
        ],
        function ($, Element, eventManager) {
            "use strict";

            return Element.extend({
                data: {},
                config: {},
                setConfig: function (config) {
                    this.config = config;
                    var self = this;
                    this.defineConstants();
                    this.prepareCurrencyData();
                    eventManager.dispatch('webpos_innit_config_after', {'webpos': self});
                },
                defineConstants: function() {
                    this.config['general/min_decimal_value'] = 0.00005;
                },
                prepareCurrencyData: function () {
                    var currencies = {};
                    for (var i in this.config['currencies']) {
                        var currency = this.config['currencies'][i];
                        currencies[currency.code] = currency;
                    }
                    this.config['currencies'] = currencies;
                },
                /**
                 * Get config data
                 * 
                 * @param {string} path
                 */
                getConfig: function (path) {
                    if (typeof this.config[path] != 'undefined') {
                        return this.config[path];
                    }
                    for (var i in this.data) {
                        if (this.data[i]['path'] == path) {
                            this.config[path] = this.data[i]['value'];
                            return this.config[path];
                        }
                    }
                    return null;
                }
            });
        }
);