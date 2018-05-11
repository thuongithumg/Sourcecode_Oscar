/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magestore_Webpos/js/lib/jquery.toaster',
    'mage/translate'
], function ($, Alert, $toaster, Translate) {
    'use strict';
    return function(data) {
        if(data && data.priority){
            $.toaster({
                priority: data.priority,
                title: Translate(data.title),
                message: Translate(data.message)
            });
        }else{
            Alert({
                title: Translate(data.title),
                content: Translate(data.content),
                autoOpen: true,
                clickableOverlay: true,
                focus: "",
                actions: {
                    always: function(){
                    }
                }
            });
        }
    }
});
