/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout-url-builder',
        'mage/storage',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate'
    ],
    function ($, ko, urlBuilder, storage, addNotification, $t) {
        'use strict';
        return {
            lockPos: function (pin, deferred) {
                var lockUrl = urlBuilder.createUrl('/webpos/pos/lockpos', {});
                if (!deferred) {
                    deferred = $.Deferred();
                }
                return storage.post(
                    lockUrl, JSON.stringify({pin: pin})
                ).done(function (response) {
                    response = JSON.parse(response);
                    if (response.success == true) {
                        deferred.resolve(true);
                    } else {
                        addNotification(response.message, true, 'danger', 'Error');
                        deferred.resolve(false);
                    }
                }).fail(function (response) {
                    console.log(response);
                    if (response) {
                        if (response.responseJSON && response.responseJSON.message) {
                            addNotification(response.responseJSON.message, true, 'danger', 'Error');
                        }
                        else if (response.status == 0) {
                            addNotification($t('Network connection failed.'), true, 'danger', 'Error');
                        }
                    }
                    deferred.resolve(false);
                });
                return deferred;
            }
        }
    }
);
