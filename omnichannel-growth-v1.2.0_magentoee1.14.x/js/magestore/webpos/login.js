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
    'jquery',
    'model/resource-model/magento-rest/abstract',
    'lib/cookie',
    'helper/general',
    'lib/jquery.toaster',
    'jquery/ui',
    'mage/translate'
], function ($, restAbstract, Cookies, Helper) {
    $.widget("magestore.webposLogin", {
        _create: function () {
            var self = this,
                options = this.options;
            $.extend(this, {

            });

            $(this.element).mage('mage/validation', {
                submitHandler: function (form) {
                    self.ajaxLogin();
                }
            });

        },

        ajaxLogin: function () {
            var self = this;
            var apiUrl = '/webpos/staff/login';
            var deferred = $.Deferred();
            var staff = {};
            staff.username = $(this.element).find('#username').val();
            staff.password = $(this.element).find('#pwd').val();
            $(this.element).find('button').html(Helper.__('Please wait ...'));
            $(this.element).find('button').prop("disabled",true);
            restAbstract().setPush(true).setLog(false).callRestApi(
                apiUrl,
                'post',
                {},
                {
                    'staff': staff
                    // 'store': $(this.element).find('#select_store').val()
                },
                deferred
            );
            deferred.done(function (data) {

                if (data != false) {
                    var sessionId = data.session_id;
                    var storeUrl = data.store_url;
                    var locationId = data.location_id;
                    var tillId = data.till_id;
                    var tills = data.available_tills;
                    Helper.saveLocalConfig('current_store_full_name',
                        $(self.element).find('#select_store option:selected').text());
                    Helper.saveLocalConfig('current_location_id', locationId);
                    Helper.saveLocalConfig('current_till_id', tillId);
                    Helper.saveLocalConfig('available_tills', JSON.stringify(tills));
                    Cookies.set('WEBPOSSESSION', sessionId, { expires: parseInt(window.webposConfig.timeoutSession) });
                    Cookies.set('check_login', 1, { expires: parseInt(window.webposConfig.timeoutSession) });
                    if(storeUrl){
                        window.location.href = storeUrl;
                    }else{
                        window.location.reload();
                    }
                } else {
                    $(self.element).find('button').html(Helper.__('Login'));
                    $(self.element).find('button').prop("disabled",false);

                    $.toaster(
                        {
                            priority: 'danger',
                            title: Helper.__("Warning"),
                            message: Helper.__("Your login information is wrong!")
                        }
                    );

                }
            });
            deferred.fail(function (data) {
                var self = this;
                $(self.element).find('button').html(Helper.__('Login'));
                $(self.element).find('button').prop("disabled",false);
            })
        }
    });

    return $.magestore.webposLogin;

});