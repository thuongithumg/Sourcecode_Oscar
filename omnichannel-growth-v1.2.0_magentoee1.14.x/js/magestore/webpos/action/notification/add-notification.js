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

/*global define*/
define(
    [
        'jquery',
        'ui/components/notification/list',
        'lib/jquery/jquery.toaster'
        // 'mage/translate'

    ],
    function($, notificationList, Toaster
             // Translate
    ) {
        'use strict';
        /*
            priority: danger, success
                
         */
        return function (message, isShowToaster, priority, title) {
            if (isShowToaster == true) {
                $.toaster(
                    {
                        priority: priority,
                        title: (title),
                        message: (message)
                    }
                );
            }
            notificationList().addLog((message));
        }
    }
);
