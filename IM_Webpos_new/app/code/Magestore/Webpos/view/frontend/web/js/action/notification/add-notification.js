/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'Magestore_Webpos/js/view/notification/list',
        'Magestore_Webpos/js/lib/jquery.toaster',
        'mage/translate'

    ],
    function($, notificationList, Toaster, Translate) {
        'use strict';
        /*
            priority: danger, success
                
         */
        return function (message, isShowToaster, priority, title) {
            if (isShowToaster == true) {
                $.toaster(
                    {
                        priority: priority,
                        title: Translate(title),
                        message: Translate(message)
                    }
                );
            }
            notificationList().addLog(Translate(message));
        }
    }
);
