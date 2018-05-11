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
        'posComponent',
        'underscore'
    ],
    function ($,ko, Component, _) {
        "use strict";

        return Component.extend({
            defaults:{
                template:'ui/settings/general/content'
            },
            enableExtensions: ko.observableArray([]),
            initialize: function () {
                this._super();
                if(window.webposConfig.plugins){
                    this.enableExtensions(window.webposConfig.plugins);
                }
            },
            hasExtensions: function(){
                var self = this;
                if(self.elems().length > 0){
                    _.forEach(self.elems(), function(child){
                        if(!self.isExtensionEnable(child.index) && child.index.indexOf('os_') >= 0){
                            self.removeChild(child.index);
                        }
                    });
                }
                return (self.elems().length > 0)?true:false;
            },
            isExtensionEnable: function(index){
                return ($.inArray(index, this.enableExtensions()) !== -1);
            }
        });
    }
);