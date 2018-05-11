/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/settings/general/abstract'
    ],
    function ($, ko, select) {
        "use strict";

        return select.extend({
            defaults: {
                template: 'Magestore_Webpos/settings/general/timeout'
            },
            elementName: 'timeout',
            value: ko.observable(''),
            optionsArray: ko.observableArray(
                [
                    {
                        value: 1,
                        text: '1 Minute'
                    },
                    {
                        value: 2,
                        text: '2 Minutes'
                    }
                ]
            )
        });
    }
);