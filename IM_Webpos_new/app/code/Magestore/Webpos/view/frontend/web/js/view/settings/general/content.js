/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore'
    ],
    function ($,ko, Component, _) {
        "use strict";

        return Component.extend({
            defaults:{
                template:'Magestore_Webpos/settings/general/content'
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