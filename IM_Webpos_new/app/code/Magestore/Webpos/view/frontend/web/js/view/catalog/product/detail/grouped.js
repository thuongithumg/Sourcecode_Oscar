/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/catalog/product/detail-popup',
        'Magestore_Webpos/js/helper/price'
    ],
    function ($,ko, detailPopup, priceHelper) {
        "use strict";
        return detailPopup.extend({
            defaultValue: ko.observable(0),
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/grouped'
            },
            initialize: function () {
                this.defaultValue(0);
                this._super();
                detailPopup().groupItem(this);
            },
            getFormatPrice: function (price) {
                return priceHelper.convertAndFormat(price);
            },
            descQty: function (id, groupedOptions) {
                if ($('#super_group_' + id).length > 0) {
                    var qty = parseInt($('#super_group_' + id)[0].value) - 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + id)[0].value = parseInt(qty);
                    this.updatePrice(groupedOptions);
                }
            },
            incQty: function (id, groupedOptions) {
                if ($('#super_group_' + id).length > 0) {
                    var qty = parseInt($('#super_group_' + id)[0].value) + 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + id)[0].value = parseInt(qty);
                    this.updatePrice(groupedOptions);
                }
            },
            increaseQty: function ($obj) {
                if ($('#super_group_' + $obj.id).length > 0) {
                    var qty = parseInt($('#super_group_' + $obj.id)[0].value) + 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + $obj.id)[0].value = parseInt(qty);
                    this.updatePrice(this.groupedOptions);
                }
            },
            decreaseQty: function ($obj) {
                if ($('#super_group_' + $obj.id).length > 0) {
                    var qty = parseInt($('#super_group_' + $obj.id)[0].value) - 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + $obj.id)[0].value = parseInt(qty);
                    this.updatePrice(this.groupedOptions);
                }
            },
            getDefaultQty: function($parent, $obj){
                $parent.updatePrice($parent.groupedOptions);
                return parseInt($obj.default_qty);
            },
            prepareAddToCart: function(){
                //self.updatePrice(self.groupedOptions);
                this.prepareAddToCart();
            },
            updatePrice: function (groupedOptions) {
                if(!groupedOptions){
                    groupedOptions = this.itemData().grouped_options;
                 }
                 if(typeof groupedOptions !== 'undefined'){
                     var groupData = [];
                     this.groupedProductResult([]);

                     $.each(groupedOptions, function (index, value) {
                         if ($("[name='super_group[" + value.id + "]']").length > 0) {
                             if ($("[name='super_group[" + value.id + "]']")[0].value
                                 && $("[name='super_group[" + value.id + "]']")[0].value > 0) {
                                 value.qty = $("[name='super_group[" + value.id + "]']")[0].value;
                                 groupData.push(value);
                             } else {
                                 $("[name='super_group[" + value.id + "]']")[0].value = 0;
                             }
                         }
                     });
                     this.groupedProductResult(groupData);
                 }
            }
        });
    }
);