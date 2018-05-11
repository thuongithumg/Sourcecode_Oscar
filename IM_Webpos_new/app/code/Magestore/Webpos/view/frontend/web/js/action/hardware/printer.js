/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'mage/translate',
        'Magestore_Webpos/js/action/hardware/connect',
        'Magestore_Webpos/js/helper/alert'
    ],
    function ($, $t, connect, Alert) {
        'use strict';
        return function (html) {
            var deferred = connect('printer', html);
            deferred.done(function (response) {
                Alert({
                    priority: 'success',
                    title: $t('Success'),
                    message: $t('Send to POSHub successfully!')
                });

            });
            deferred.always(function () {
                $('.action-button .print').prop('disabled', false);
            });
        }
    }
);
