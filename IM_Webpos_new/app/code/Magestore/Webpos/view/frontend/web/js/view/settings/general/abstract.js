/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'Magestore_Webpos/js/model/config/config'
    ],
    function ($, Component, Config) {
        "use strict";

        return Component.extend({

            initialize: function () {
                this._super();
                this.loadConfig();
            },

            saveConfig: function (data) {
                var value = $('select[name="' + data.elementName + '"]').val();
                if (value) {
                    var deferred = $.Deferred();
                    Config().setData({
                        scope: 'default',
                        scope_id: null,
                        path: 'webpos/config/' + data.elementName,
                        value: value
                    }).save(deferred);
                    deferred.done(function (data) {
                    });
                }
            },

            loadConfig: function () {
                var self = this;
                var deferred = $.Deferred();
                var id = 'webpos/config/' + self.elementName;
                Config().load(id, deferred);
                deferred.done(function (data) {
                    if (data.value) {
                        self.value(data.value);
                    }
                });
            }
        });
    }
);