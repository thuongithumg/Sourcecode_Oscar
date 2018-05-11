/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
    'Magestore_Webpos/js/lib/cookie',
    'mage/translate',
    'mage/url',
    'Magestore_Webpos/js/lib/jquery.toaster',
    'jquery/ui'
], function ($, restAbstract, Cookies, Translate, mageUrl) {
    $.widget("magestore.webposLogin", {
        _create: function () {
            var self = this, options = this.options;
            $.extend(this, {

            });

            $('#webpos-login').mage('validation', {
                submitHandler: function (form) {
                    self.ajaxLogin();
                }
            });

            $('#checkout-loader').hide();
        },

        ajaxLogin: function () {
            var self = this;
            var apiUrl = '/webpos/staff/login';
            var deferred = $.Deferred();
            var staff = {};
            staff.username = $(this.element).find('#username').val();
            staff.password = $(this.element).find('#pwd').val();
            var loginButton = $(this.element).find('button');
            loginButton.html(Translate('Please wait ...'));
            loginButton.find('button').prop("disabled",true);
            restAbstract().setPush(true).setLog(false).callRestApi(
                apiUrl,
                'post',
                {},
                {
                    'staff': staff
                },
                deferred
            );
            deferred.done(function (data) {
                // data = JSON.parse(data);
                if (data != false) {
                    Cookies.set('WEBPOSSESSION', data, { expires: parseInt(window.webposConfig.timeoutSession) });
                    Cookies.set('check_login', 1, { expires: parseInt(window.webposConfig.timeoutSession) });
                    // if(typeof data.location_id === 'undefined' || data.location_id === '') {
                        window.location.reload();
                    // } else {
                    //     var deleteRequest = window.indexedDB.deleteDatabase('magestore_webpos');
                    //     var url = mageUrl.build("webpos/index/changeStore?store_id=" + data.store_view_id);
                    //     window.location.href = url;
                    // }
                } else {
                    loginButton.html(Translate('Login'));
                    loginButton.prop("disabled",false);

                    $.toaster(
                        {
                            priority: 'danger',
                            title: Translate("Warning"),
                            message: Translate("Your login information is wrong!")
                        }
                    );

                }
            });
            deferred.fail(function (data) {
                var self = this;
                loginButton.html(Translate('Login'));
                loginButton.prop("disabled",false);
                $.toaster(
                    {
                        priority: 'danger',
                        title: Translate("Warning"),
                        message: Translate("Your login information is wrong!")
                    }
                );
            })
        }
    });

    return $.magestore.webposLogin;

});