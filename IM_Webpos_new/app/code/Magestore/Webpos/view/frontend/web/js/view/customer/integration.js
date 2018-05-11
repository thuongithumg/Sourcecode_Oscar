/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'underscore',
        'uiComponent'
    ],
    function ($, ko, _, Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/customer/integration'
            },
            enableChilds: ko.observableArray([]),
            initialize: function () {
                this._super();
                if(window.webposConfig.plugins){
                    this.enableChilds(window.webposConfig.plugins);
                }
            },
            hasChilds: function(){
                var self = this;
                var visibleChilds = [];
                if(self.elems().length > 0){
                    _.forEach(self.elems(), function(child){
                        if(!self.isChildEnable(child.index)){
                            self.removeChild(child.index);
                        }
                        if(child.visible()){
                            visibleChilds.push(child.index);
                        }
                    });
                }
                return (self.elems().length > 0 && visibleChilds.length > 0)?true:false;
            },
            isChildEnable: function(index){
                return ($.inArray(index, this.enableChilds()) !== -1);
            }
        });
    }
);