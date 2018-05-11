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
    'lib/jquery/posAlert',
    'lib/jquery/jquery.toaster'
], function ($, Alert) {
    'use strict';
    return function(data) {
        if(data && data.priority){
            $.toaster({
                priority: data.priority,
                title: data.title,
                message: data.message
            });
        }else{
            if(data.type == 'confirm'){
                Alert({
                    title:data.title,
                    content:data.content,
                    hasSubmit: true,
                    overlayDismiss: true,
                    onSubmit: function(popup){
                        popup.hideOverlay();
                        popup.close();
                    }
                });
            }else{
                Alert({
                    content:data,
                    hasSubmit: false,
                    overlayDismiss: true
                });
            }
        }
    }
});
