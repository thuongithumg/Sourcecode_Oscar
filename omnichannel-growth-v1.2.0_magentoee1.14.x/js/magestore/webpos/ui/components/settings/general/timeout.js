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