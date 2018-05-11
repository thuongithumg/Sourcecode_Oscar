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
        'underscore',
        'posComponent',
        'helper/general'
    ],
    function ($, ko, _, Component, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/checkout/payment/before-methods'
            },
            enableChilds: ko.observableArray(Helper.getBrowserConfig('plugins')),
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