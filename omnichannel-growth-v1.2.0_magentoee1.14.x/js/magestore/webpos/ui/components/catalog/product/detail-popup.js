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
        'ui/components/catalog/product/type/configurable',
        'model/checkout/cart',
        'helper/price',
        'helper/alert',
        'helper/general',
        'model/catalog/product-factory',
        'mage/validation',
        'mage/validation/validation'
    ],
    function ($, ko, Component, configurable, CartModel, priceHelper, alertHelper, generalHelper, ProductFactory) {
        "use strict";
        window.timeout = 0;
        return Component.extend({
            itemData: ko.observable({}),
            styleOfPopup: ko.observable('view_detail'),
            focusQtyInput: true,
            qtyAddToCart: ko.observable(1),
            defaultPriceAmount: ko.observable(),
            basePriceAmount: ko.observable(),
            configurableProductIdResult: ko.observable(),
            configurableOptionsResult: ko.observable(),
            configurableOptionsAttributesInfoResult: ko.observable(),
            configurableLabelResult: ko.observable(),
            groupedProductResult: ko.observableArray([]),
            bundleOptionsValueResult: ko.observableArray([]),
            bundleOptionsQtyResult: ko.observableArray([]),
            bundleChildsQtyResult: ko.observableArray([]),
            bundleOptionsLableResult: ko.observableArray([]),
            customOptionsValueResult: ko.observableArray([]),
            customOptionsLableResult: ko.observableArray([]),
            creditProductResult: ko.observableArray([]),
            creditValue: ko.observable(),
            bundleItem: ko.observable(),
            groupItem: ko.observable(),
            giftvoucherCanShip: ko.observable(false),
            defaults: {
                template: 'ui/catalog/product/detail-popup'
            },
            initialize: function () {
                configurable.detailPopup(this);
                this._super();
            },
            getTypeId: function () {
                return this.itemData().type_id;
            },
            incQty: function(){
                var qty = this.getQtyAddToCart();
                var qty_increment = parseFloat((this.itemData().qty_increment)?this.itemData().qty_increment:1);
                qty = qty + qty_increment;
                var maximum_qty = this.itemData().maximum_qty;
                if(maximum_qty && qty > maximum_qty){
                    if(qty_increment){
                        if(maximum_qty%qty_increment != 0){
                            maximum_qty = qty_increment * Math.ceil(maximum_qty/qty_increment);
                        }
                    }
                    qty = maximum_qty;
                    alertHelper({
                        priority: "danger",
                        title: this.__("Message"),
                        message: this.__("Maximum qty allow in cart is ")+maximum_qty
                    });
                }
                var minimum_qty = this.itemData().minimum_qty;
                if(minimum_qty && qty < minimum_qty){
                    qty = minimum_qty;
                    alertHelper({
                        priority: "danger",
                        title: this.__("Message"),
                        message: this.__("Minimum qty allow in cart is ")+minimum_qty
                    });
                }else if(!minimum_qty && qty <= 0){
                    qty = qty_increment;
                }
                this.qtyAddToCart(qty);
            },
            descQty: function(){
                var qty = this.getQtyAddToCart();
                var qty_increment = parseFloat((this.itemData().qty_increment)?this.itemData().qty_increment:1);
                qty = qty - qty_increment;
                var maximum_qty = this.itemData().maximum_qty;
                if(maximum_qty && qty > maximum_qty){
                    if(qty_increment){
                        if(maximum_qty%qty_increment != 0){
                            maximum_qty = qty_increment * Math.ceil(maximum_qty/qty_increment);
                        }
                    }
                    qty = maximum_qty;
                    alertHelper({
                        priority: "danger",
                        title: this.__("Message"),
                        message: this.__("Maximum qty allow in cart is ")+maximum_qty
                    });
                }
                var minimum_qty = this.itemData().minimum_qty;
                if(minimum_qty && qty < minimum_qty){
                    var min = Math.max(qty_increment, minimum_qty);
                    qty = min;
                    alertHelper({
                        priority: "danger",
                        title: this.__("Message"),
                        message: this.__("Minimum qty allow in cart is ")+min
                    });
                }else if(!minimum_qty && qty <= 0){
                    qty = qty_increment;
                }
                this.qtyAddToCart(qty);
            },
            getQtyAddToCart: function(){
                if (this.qtyAddToCart() <= 1 || isNaN(this.qtyAddToCart())) {
                    var qty_increment = parseFloat((this.itemData().qty_increment)?this.itemData().qty_increment:1);
                    return qty_increment;
                }
                return this.qtyAddToCart();
            },
            modifyQty: function(data,event){
                var qty = parseFloat(event.target.value);
                var qty_increment = parseFloat((this.itemData().qty_increment)?this.itemData().qty_increment:1);
                var maximum_qty = this.itemData().maximum_qty;
                if(maximum_qty && qty > maximum_qty){
                    if(qty_increment){
                        if(maximum_qty%qty_increment != 0){
                            maximum_qty = qty_increment * Math.ceil(maximum_qty/qty_increment);
                        }
                    }
                    event.target.value = maximum_qty;
                    qty = maximum_qty;
                    alertHelper({
                        priority: "danger",
                        title: this.__("Message"),
                        message: this.__("Maximum qty allow in cart is ")+maximum_qty
                    });
                }
                var minimum_qty = this.itemData().minimum_qty;
                if(minimum_qty && qty < minimum_qty){
                    var min = Math.max(qty_increment, minimum_qty);
                    event.target.value = min;
                    qty = min;
                    alertHelper({
                        priority: "danger",
                        title: this.__("Message"),
                        message: this.__("Minimum qty allow in cart is ")+min
                    });
                }else if(!minimum_qty && qty <= 0){
                    event.target.value = qty_increment;
                    qty = qty_increment;
                }
                this.qtyAddToCart(qty);
            },
            setAllData: function () {
                var self = this;
                this.qtyAddToCart(1);
                if (this.itemData().images) {
                    this.reloadJs();
                }
                /* set config */
                this.defaultPriceAmount('');
                //configurable.priceConfig($.parseJSON(self.getItemData().price_config));
                if (this.getTypeId() == 'configurable') {
                    if (window.timeout != 0) {
                        var timeout = 0;
                    } else {
                        window.timeout = 1;
                        var timeout = 1000;
                    }
                    var spConfigData = $.parseJSON(self.itemData().json_config);
                    this.defaultPriceAmount(priceHelper.convertAndFormat(spConfigData.prices.basePrice.amount));
                    setTimeout(function() {
                        configurable._resetConfigurable();
                        configurable.priceConfig($.parseJSON(self.itemData().price_config));
                        configurable.options.spConfig = spConfigData;
                        configurable.options.optionTemplate = '<%- data.label %>' +
                            '<% if (data.finalPrice.value) { %>' +
                            ' <%- data.finalPrice.formatted %>' +
                            '<% } %>';
                        configurable.createPriceBox();

                        var eventData = {configurableObject : configurable, optionsData:self.itemData()};
                        generalHelper.dispatchEvent('webpos_product_generate_configurable_options_before', eventData);

                        configurable._create();
                    }, timeout);
                } else if (this.getTypeId() == 'giftvoucher') {
                    this.giftvoucherCanShip(false);
                } else {
                    this.defaultPriceAmount(priceHelper.convertAndFormat(this.itemData().final_price));
                    //configurable.priceConfig($.parseJSON(self.itemData().price_config));
                }
            },
            prepareAddToCart: function() {
                var self = this;
                self.updatePrice();
                var ProductModel = ProductFactory.get();
                if (this.validateAddToCartForm()) {
                    var product = self.getProductData();
                    var stocks = product.stocks;
                    if (product.super_group && product.super_group.length > 0) {
                        ko.utils.arrayForEach(product.super_group, function (product) {
                            if (product.id) {
                                for(var i in stocks) {
                                    if(stocks[i].sku === product.sku) {
                                        product.stocks = [stocks[i]];
                                        break;
                                    }
                                }
                                ProductModel.data = product;
                                product.unit_price = ProductModel.getFinalPrice();
                                self.addProduct(product);
                            }
                        });
                    }else if(product.storecredit_type == 2){
                        var rate = parseFloat(product.storecredit_rate);
                        var storeCreditMin = parseFloat(product.storecredit_min);
                        var storeCreditMax = parseFloat(product.storecredit_max);
                        var currentStoreCredit = parseFloat(priceHelper.toBasePrice($('#storecredit_'+product.id).val()));

                        if((currentStoreCredit <storeCreditMin) ||(currentStoreCredit > storeCreditMax)){
                            alertHelper({
                                priority: "danger",
                                title: this.__("Error"),
                                message: this.__("Invalid credit value!")
                            });
                        }else{
                            var basePrice = priceHelper.toBasePrice(parseFloat($('#storecredit_'+product.id).val())) * rate;
                            self.basePriceAmount(basePrice);
                            self.creditValue(priceHelper.toBasePrice(parseFloat($('#storecredit_'+product.id).val())));
                            product = self.getProductData();
                            self.addProduct(product);
                            product.credit_price_amount = undefined;
                        }

                    }else if(product.storecredit_type == 3){
                        product = self.getProductData();
                        self.addProduct(product);
                        product.credit_price_amount = undefined;
                    }else {
                        self.addProduct(product);
                        product.credit_price_amount = undefined;
                    }
                    self.closeDetailPopup();
                } else {
                    if ($('.swatch-option').length > 0) {
                        alertHelper({title:'Error', content: generalHelper.__('Please choose all options')});
                    }
                }
            },
            /* Validate Add Address Form */
            validateAddToCartForm: function () {
                var form = '#product_addtocart_form';
                return $(form).validation() && $(form).validation('isValid');
            },
            getProductData: function(){
                var self =  this;
                var product = self.itemData();
                if (product.type_id == "configurable") {
                    product.super_attribute = self.configurableOptionsResult();
                    product.attributes_info = self.configurableOptionsAttributesInfoResult();
                    product.unit_price = self.basePriceAmount();
                    product.child_id = self.configurableProductIdResult();
                    product.options_label = self.configurableLabelResult();
                }
                if (product.type_id == "grouped") {
                    product.super_group = self.groupedProductResult();
                    product.unit_price = "";
                    product.options_label = "";
                }
                if (product.type_id == "customercredit") {
                    var rate = product.storecredit_rate;
                    if(typeof product.credit_price_amount !== 'undefined'){
                        product.amount = self.creditValue();
                        product.credit_price_amount = parseFloat(product.amount) * parseFloat(rate);
                    }else{
                        if(product.storecredit_type == 3){
                            var values = product.customercredit_value.split(',');
                            product.credit_price_amount = parseFloat(values[0]) * parseFloat(rate);
                            product.amount = parseFloat(values[0]);
                        }else if(product.storecredit_type == 2){
                            product.credit_price_amount = parseFloat(product.storecredit_min) * parseFloat(rate);
                            product.amount = parseFloat(product.storecredit_min);
                        }else{
                            product.credit_price_amount = parseFloat(product.customercredit_value) * parseFloat(rate);
                            product.amount = parseFloat(product.customercredit_value);
                        }
                    }
                    self.creditValue(product.amount);
                    self.basePriceAmount(product.credit_price_amount);
                    product.unit_price = self.basePriceAmount();
                    product.options_label = priceHelper.convertAndFormat(self.creditValue());
                    product.hasOption = true;
                }
                if (product.type_id == "bundle") {
                    product.bundle_option = self.bundleOptionsValueResult();
                    product.bundle_option_qty = self.bundleOptionsQtyResult();
                    product.bundle_childs_qty = self.bundleChildsQtyResult();
                    product.unit_price = self.basePriceAmount();
                    product.options_label = self.bundleOptionsLableResult();
                }
                if (self.customOptionsValueResult().length > 0) {
                    product.selected_options = self.customOptionsValueResult();
                    product.unit_price = self.basePriceAmount();
                    // product.options_label = self.customOptionsLableResult();
                    product.custom_options_label = self.customOptionsLableResult();
                }

                if (product.type_id == "giftvoucher") {
                    product.unit_price = self.basePriceAmount();
                    product.recipient_ship = self.giftvoucherCanShip();
                }
                product.qty = self.qtyAddToCart();
                return product;
            },
            addProduct: function(product){
                var self = this;
                var Product = ProductFactory.get();
                Product.setData(product);
                CartModel.addProduct(Product.getInfoBuyRequest(CartModel.customerGroup()));
                self.customOptionsValueResult([]);
                self.customOptionsLableResult([]);
                self.configurableProductIdResult('');
                self.configurableOptionsResult('');
                self.configurableLabelResult('');
                self.groupedProductResult([]);
                self.bundleOptionsValueResult([]);
                self.bundleOptionsQtyResult([]);
                self.bundleChildsQtyResult([]);
                self.bundleOptionsLableResult([]);
                self.customOptionsValueResult([]);
                self.customOptionsLableResult([]);
                $("#search-header-product").val("");
                $("#search-header-product").blur();
                $("#search-header-product").select();
            },
            closeDetailPopup: function() {
                $("#popup-product-detail").hide();
                $(".wrap-backover").hide();
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                $('#product-options-wrapper input').val('');
            },
            reloadJs: function () {
                var $j = jQuery.noConflict();
                if ($j("#product-img-slise").find('div.owl-controls').length > 0) {
                    var removeControl = $j("#product-img-slise").find('div.owl-controls');
                    removeControl[0].remove();
                }
                setTimeout(function(){
                    $j("#product-img-slise").owlCarousel({
                        items: 1,
                        itemsDesktop: [1000, 1],
                        itemsDesktopSmall: [900, 1],
                        itemsTablet: [600, 1],
                        itemsMobile: false,
                        navigation: true,
                        pagination:true,
                        navigationText: ["", ""]
                    });
                }, 50);
            },
            updatePrice: function () {
                var self = this;
                var product = self.itemData();

                if (product.type_id == "grouped" && self.groupItem()) {
                    self.groupItem().updatePrice();
                }
                if (product.type_id == "bundle" && self.bundleItem()) {
                    self.bundleItem().updatePrice();
                }
            },
            showPopup: function(){
                $("#popup-product-detail").show();
                $("#popup-product-detail").removeClass("fade");
                $(".wrap-backover").show();

                $(document).click(function(e) {
                    if( e.target.id == 'popup-product-detail') {
                        $("#popup-product-detail").hide();
                        $(".wrap-backover").hide();
                        $('.notification-bell').show();
                        $('#c-button--push-left').show();
                        $('#product-options-wrapper input').val('');
                    }
                });
                this.updatePrice();
            }
        });
    }
);