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
        'model/checkout/cart',
        'helper/price',
        'helper/general'
    ],
    function ($, ko, CartModel, PriceHelper, Helper) {
        "use strict";
        var CustomSaleModel = {
            productName: ko.observable(),
            productPrice: ko.observable(),
            productQuantity: ko.observable(1),
            taxClassId: ko.observable(),
            shipAble: ko.observable(false),
            taxClasses: ko.observableArray(),
            initialize: function () {
                var self = this;
                self.initTaxClasses();
                return self;
            },
            resetData: function(){
                this.productName("");
                this.productPrice(0);
                this.productQuantity(1);
                this.taxClassId("");
            },
            addToCart: function(){
                var self = this;
                var price = PriceHelper.toNumber(self.productPrice());
                var data = {
                    product_id:"customsale",
                    sku:(self.taxClassId())?"webpos-customsale-"+self.taxClassId():'webpos-customsale',
                    qty: self.productQuantity(),
                    product_name:self.productName()?self.productName():Helper.__("Custom Product"),
                    unit_price:PriceHelper.toBasePrice(price),
                    tax_class_id:self.taxClassId(),
                    is_virtual:self.shipAble()?false:true
                };
                CartModel.addProduct(data);
                this.resetData();
            },
            initTaxClasses: function(){
                var self = this;
                var taxClass = window.webposConfig.tax_class;
                taxClass = $.merge([{'tax_class_id':'','tax_class_name':Helper.__('None')}], taxClass);
                self.taxClasses(taxClass);
            }
        };
        return CustomSaleModel.initialize();
    }
);