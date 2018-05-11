/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/helper/price'
    ],
    function ($,ko, modelAbstract, PriceHelper) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.itemFields = [
                    'isVisible','cssClass','title','value','baseValue','finalValue','baseFinalValue','code','valueFormated'
                ];
                this.initFields = [
                    'isVisible', 'cssClass', 'title', 'value','baseValue','finalValue','baseFinalValue','code', 'displayIncludeTax', 'includeTaxValue', 'removeAble', 'isPrice', 'formated'
                ];
            },
            init: function(data){
                var self = this;
                self.isVisible = (typeof data.isVisible != "undefined")?ko.observable(data.isVisible):ko.observable(true);
                self.cssClass = (typeof data.cssClass != "undefined")?ko.observable(data.cssClass):ko.observable();
                self.title = (typeof data.title != "undefined")?ko.observable(data.title):ko.observable();
                self.value = (typeof data.value != "undefined")?ko.observable(data.value):ko.observable();
                self.finalValue = (typeof data.finalValue != "undefined")?ko.observable(data.finalValue):ko.observable();
                self.baseFinalValue = (typeof data.baseFinalValue != "undefined")?ko.observable(data.baseFinalValue):ko.observable();
                self.baseValue = (typeof data.baseValue != "undefined")?ko.observable(data.baseValue):ko.observable();
                self.code = (typeof data.code != "undefined")?ko.observable(data.code):ko.observable();
                if(data.code == 'giftcardaccount')
                    self.giftcardCode = ko.observable();
                self.includeTaxValue = (typeof data.includeTaxValue != "undefined")?ko.observable(data.includeTaxValue):ko.observable();
                self.displayIncludeTax = (typeof data.displayIncludeTax != "undefined")?ko.observable(data.displayIncludeTax):ko.observable(false);
                self.removeAble = (typeof data.removeAble != "undefined")?((typeof data.removeAble == 'function')?data.removeAble:ko.observable(data.removeAble)):ko.observable(false);
                self.isPrice = (typeof data.isPrice != "undefined")?ko.observable(data.isPrice):ko.observable(true);
                self.formated = (typeof data.formated != "undefined")?ko.observable(data.formated):ko.observable(false);
                self.actions = (typeof data.actions != "undefined")?ko.observable(data.actions):ko.observable({
                    remove:function(){},
                    collect:function(){}
                });
                self.value.subscribe(function(value){
                    var newvalue = PriceHelper.correctPrice(value);
                    if(newvalue != value){
                        self.value(newvalue);
                    }
                });
                self.valueFormated = ko.pureComputed(function(){
                    var value = self.value();
                    if(self.displayIncludeTax() == true && value > 0){
                        value = self.includeTaxValue();
                    }
                    return (!self.isPrice())?((self.formated() !== false)?self.formated():value):PriceHelper.formatPrice(value);
                });
            },
            setData: function(key,value){
                if(typeof this[key] != "undefined"){
                    this[key](value); 
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
            }
        });
    }
);