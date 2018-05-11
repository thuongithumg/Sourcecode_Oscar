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
        'jquery',
        'uiComponent',
        'model/config/config'
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
                        //console.log(data);
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