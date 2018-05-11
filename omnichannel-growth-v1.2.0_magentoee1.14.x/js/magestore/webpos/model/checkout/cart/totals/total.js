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
        'uiClass',
        'helper/price'
    ],
    function ($,ko, UiClass, PriceHelper) {
        "use strict";
        return UiClass.extend({
            initialize: function () {
                this._super();
                this.initFields = [
                    'isVisible', 'cssClass', 'title', 'value', 'finalValue', 'code', 'displayIncludeTax', 'includeTaxValue', 'removeAble', 'autoValue', 'isPrice', 'formated'
                ];
            },
            init: function(data){
                var self = this;
                self.cssClass = (typeof data.cssClass != "undefined")?ko.observable(data.cssClass):ko.observable();
                self.title = (typeof data.title != "undefined")?ko.observable(data.title):ko.observable();
                self.isRequired = (typeof data.isRequired != "undefined")?ko.observable(data.isRequired):ko.observable();
                self.autoValue = (typeof data.autoValue != "undefined")?ko.observable(data.autoValue):ko.observable();
                self.value = (typeof data.value != "undefined")?(self.autoValue() == true)?data.value:ko.observable(data.value):ko.observable();
                self.finalValue = (typeof data.finalValue != "undefined")?(self.autoValue() == true)?data.finalValue:ko.observable(data.finalValue):ko.observable();
                self.isVisible = (typeof data.isVisible != "undefined")?(self.autoValue() == true)?ko.pureComputed(function(){
                    return (self.value() || self.isRequired())?true:false;
                }):ko.observable(data.isVisible):ko.observable(true);
                self.code = (typeof data.code != "undefined")?ko.observable(data.code):ko.observable();
                self.includeTaxValue = (typeof data.includeTaxValue != "undefined")?(self.autoValue() == true)?data.includeTaxValue:ko.observable(data.includeTaxValue):ko.observable();
                self.displayIncludeTax = (typeof data.displayIncludeTax != "undefined")?ko.observable(data.displayIncludeTax):ko.observable(false);
                self.removeAble = (typeof data.removeAble != "undefined")?((typeof data.removeAble == 'function')?data.removeAble:ko.observable(data.removeAble)):ko.observable(false);
                self.isPrice = (typeof data.isPrice != "undefined")?ko.observable(data.isPrice):ko.observable(true);
                self.formated = (typeof data.formated != "undefined")?ko.observable(data.formated):ko.observable(false);
                self.actions = (typeof data.actions != "undefined")?ko.observable(data.actions):ko.observable({
                    remove:function(){},
                    collect:function(){}
                });
                self.valueFormated = ko.pureComputed(function(){
                    var value = self.value();
                    if(self.displayIncludeTax() == true){
                        value = self.includeTaxValue();
                    }
                    return (!self.isPrice())?((self.formated() !== false)?self.formated():value):PriceHelper.convertAndFormat(value);
                });
            },
            setData: function(key,value){
                if(typeof this[key] != "undefined"){
                    if(this.autoValue() == true){
                        if(key == 'value'){
                            this[key] = value;
                        }
                    }else{
                        this[key](value);
                    }
                }
            },
            getData: function(key){
                var self = this;
                var data = {};
                if(typeof key != "undefined"){
                    data = self[this]();
                }else{
                    var data = {};
                    $.each(this.initFields, function(){
                        data[this] = self[this]();
                    });
                }
                return data;
            },
        });
    }
);