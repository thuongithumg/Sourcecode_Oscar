/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function ($,ko, Component, _) {
        "use strict";

        return Component.extend({
            defaults:{
                template:'Magestore_Webpos/settings/general/group'
            },
        });
    }
);