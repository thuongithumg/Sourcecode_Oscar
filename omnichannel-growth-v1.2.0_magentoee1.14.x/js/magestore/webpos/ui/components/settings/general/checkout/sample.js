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

define([
    'ko',
    'jquery',
    'ui/components/order/detail',
    'helper/test'
], function (ko, $, OrderDetail, Helper) {

    'use strict';

    return OrderDetail.extend({
        defaults: {
            template: 'ui/settings/general/element/select',
            elementName: 'os_checkout.auto_check_promotion_rules',
            configPath: 'os_checkout/auto_check_promotion_rules',
            defaultValue: 0,
            optionsArray: ko.observableArray([])
        },
        value: ko.observable(''),
        initialize: function () {
            this._super();
            var self = this;
            if(self.optionsArray().length == 0){
                self.optionsArray([{value: 0, text: Helper.__('No')},
                    {value: 1, text: Helper.__('Yes')}
                ]);
            }
            // var savedConfig = Helper.getLocalConfig(self.configPath);
            if(typeof savedConfig == 'undefined'){
                // Helper.saveLocalConfig(self.configPath, self.defaultValue());
            }
            self.value = ko.pureComputed(function(){
                // return Helper.getLocalConfig(self.configPath);
            });
        },
        saveConfig: function (data, event) {
            var value = $('select[name="' + data.elementName + '"]').val();
            // Helper.saveLocalConfig(this.configPath, value);
        }
    });
});