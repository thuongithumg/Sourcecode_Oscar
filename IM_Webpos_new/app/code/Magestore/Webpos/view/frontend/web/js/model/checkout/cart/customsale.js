/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/checkout/taxclass',
        'mage/translate',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($,ko, modelAbstract, TaxClass, Translate, CartModel, PriceHelper, Helper) {
        "use strict";
        return modelAbstract.extend({
            productName: ko.observable(),
            productPrice: ko.observable(),
            taxClassId: ko.observable(Helper.getBrowserConfig('webpos/general/custom_sale_default_tax_class')),
            shipAble: ko.observable(false),
            taxClasses: ko.observableArray(),
            customSaleDescription: ko.observable(''),
            initialize: function () {
                this._super();
				var self = this;
				self.taxClassId.subscribe(function(value){
					if(typeof value == 'undefined' || (value === '')){
						self.taxClassId(Helper.getBrowserConfig('webpos/general/custom_sale_default_tax_class'));
					}
				});
            },
            resetData: function(){
                this.productName("");
                this.productPrice(0);
                this.customSaleDescription('');
                this.taxClassId(Helper.getBrowserConfig('webpos/general/custom_sale_default_tax_class'));
            },
            addToCart: function(){
                var price = PriceHelper.toNumber(this.productPrice());
                var data = {
                    product_id:"customsale",
                    qty:1,
                    product_name:this.productName()?this.productName():Translate("Custom Product"),
                    custom_sale_description: this.customSaleDescription(),
                    unit_price:PriceHelper.toBasePrice(price),
                    tax_class_id:this.taxClassId(),
                    is_virtual:this.shipAble()?false:true
                };
                CartModel.addProduct(data);
                this.resetData();
            },
            initTaxClasses: function(){
                var self = this;
                self.taxClasses([]);
                var deferred = $.Deferred();
                TaxClass().getProductTaxClasses(deferred);
                deferred.done(function(response){
                    if(response.items && response.items.length > 0){
                        var taxClasses = [{
                            tax_class_id: 0,
                            tax_class_name: Translate("None")   
                        }];
                        $.each(response.items, function(){
                            var taxclass = {
                                tax_class_id: this.class_id,
                                tax_class_name: this.class_name
                            };
                            taxClasses.push(taxclass);
                        });
                        self.taxClasses(taxClasses);
                    }
                });
				self.resetData();
            }
        });
    }
);