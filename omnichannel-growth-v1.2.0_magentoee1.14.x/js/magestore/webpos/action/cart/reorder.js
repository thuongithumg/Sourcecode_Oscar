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

/*global define*/
define(
    [
        'jquery',
        'ko',
        'action/checkout/select-customer-checkout',
        'action/notification/add-notification',
        'action/cart/checkout',
        'helper/price',
        'model/checkout/cart/items/item',
        'model/checkout/checkout',
        'model/checkout/cart',
        'model/catalog/product-factory',
        'model/customer/customer-factory',
        'model/resource-model/magento-rest/abstract',
        'helper/price',
        'model/checkout/cart/items/item/interface'
    ],
    function($, ko, SelectCustomer, AddNoti,  CheckoutOrder, PriceHelper, Item, CheckoutModel,
             CartModel, ProductFactory, CustomerFactory, restAbstract, priceHelper, ItemInterface) {
        'use strict';
        return function (orderData) {
            if(orderData && orderData.status == "notsync"){
                CheckoutOrder(orderData);
            }else{
                if(orderData && orderData.items_info_buy){
                    var Product = ProductFactory.get();
                    var items = orderData.items_info_buy.items;
                    if(items && items.length > 0){
                        var itemsId = [];
                        var hasCustomSale = false;
                        var numberItem = 0;
                        ko.utils.arrayForEach(items, function(item, index) {
                            if(item.id == 'custom_item'){
                                //hasCustomSale = true;
                                numberItem++;
                                var customData = item.custom_sales_info[0];
                                CartModel.addProduct(customData);
                            }else{
                                itemsId.push(PriceHelper.toNumber(item.id));
                            }
                        });
                        /*if(hasCustomSale == true){
                            var message = "Your order has Custom Sale item, you need to add it to cart manually"
                            AddNoti(message, true, 'warning', 'Message');
                        }*/
                        var deferred = $.Deferred();
                        restAbstract().setPush(true).callRestApi(
                            'webpos/product/list',
                            'post',
                            {},
                            {itemsId: itemsId},
                            deferred
                        );
                        deferred.done(function(response){
                            if(response.total_count > 0 && response.items){
                                ko.utils.arrayForEach(response.items, function(product, index) {
                                    ko.utils.arrayForEach(items, function(item, index) {
                                        if(item.id == product.entity_id){
                   							$.each(item, function(key, value){
                                                product[key] = value;
                                            });
                                            Product.setData(product);
                                            var infoBuy = Product.getInfoBuyRequest(CartModel.customerGroup());
                                            if(item.base_original_price && (item.base_original_price != item.base_unit_price
                                                /*van chet trong truong hop no la include tax va custom price that*/
                                                && !webposConfig['tax/calculation/price_includes_tax']=='1'
                                                )){
                                                infoBuy.unit_price = parseFloat(item.base_original_price);
                                                infoBuy.has_custom_price = true;
                                                infoBuy.show_original_price = true;
                                                infoBuy.custom_type = ItemInterface.CUSTOM_PRICE_CODE;
                                                infoBuy.custom_price_type = ItemInterface.FIXED_AMOUNT_CODE;
                                                infoBuy.custom_price_amount = parseFloat(item.unit_price);
                                            }else{
                                                // infoBuy.unit_price = item.base_unit_price;
                                                infoBuy.unit_price = priceHelper.toBasePrice(product.price);
                                                if (infoBuy.product_type == "customercredit")
                                                    infoBuy.options_label = priceHelper.convertAndFormat(item.amount);
                                            }
                                            CartModel.addProduct(infoBuy);
                                            Product.resetTempAddData();
                                        }
                                    });
                                });
                            }else{
                                if(orderData.increment_id && numberItem == 0){
                                    var message = "Cannot load product from order #"+orderData.increment_id+", you can try to reload the product data from synchronization page"
                                    AddNoti(message, true, 'danger', 'Error');
                                }
                            }
                        });
                        if(orderData.customer_id){
                            var customerData = {
                                id:orderData.customer_id,
                                email:orderData.customer_email,
                                firstname:orderData.customer_firstname,
                                full_name:orderData.customer_firstname +" "+ orderData.customer_lastname,
                                lastname:orderData.customer_lastname,
                                group_id:orderData.customer_group_id
                            };

                            var customerDeferred = CustomerFactory.get().load(orderData.customer_id);

                            if(orderData.customer_telephone){
                                customerData.telephone = orderData.customer_telephone;
                            }

                            customerDeferred.done(function (data) {
                                customerData.addresses = data.addresses;
                                SelectCustomer(customerData);
                                $('#shipping-checkout').val('');
                                $('#billing-checkout').val('');
                            });
                        }
                        if(orderData.billing_address){
                            delete orderData.billing_address.entity_id;
                            delete orderData.billing_address.parent_id;
                            delete orderData.billing_address.customer_address_id;
                            if(orderData.billing_address.region && !orderData.billing_address.region.region){
                                var region = {
                                    region:orderData.billing_address.region,
                                    region_id:orderData.billing_address.region_id,
                                    region_code:orderData.billing_address.region_code
                                };
                                orderData.billing_address.region = region;
                            }
                            delete orderData.billing_address.region_code;
                            CheckoutModel.saveBillingAddress(orderData.billing_address);
                        }

                        if(orderData.extension_attributes){
                            if(orderData.extension_attributes.shipping_assignments && orderData.extension_attributes.shipping_assignments.length > 0){
                                var shipping_assignments = orderData.extension_attributes.shipping_assignments[0];
                                if(shipping_assignments && shipping_assignments.shipping){
                                    var address = shipping_assignments.shipping.address;
                                    if(address){
                                        delete address.entity_id;
                                        delete address.parent_id;
                                        delete address.customer_address_id;
                                        if(address.region && !address.region.region){
                                            var region = {
                                                region:address.region,
                                                region_id:address.region_id,
                                                region_code:address.region_code
                                            };
                                            address.region = region;
                                        }
                                        delete address.region_code;
                                        CheckoutModel.saveShippingAddress(address);
                                    }
                                }
                            }
                        }
                        if($('#checkout').length > 0){
                            $('#checkout').click();
                        }
                    }
                }else{
                    if(orderData.increment_id){
                        var message = "Cannot load order #"+orderData.increment_id+", please reload the order data from synchronization page"
                        AddNoti(message, true, 'danger', 'Error');
                    }
                }
            }
        }
    }
);
