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
        'ui/components/catalog/product/detail-popup',
        'helper/price'
    ],
    function ($,ko, detailPopup, priceHelper) {
        "use strict";
        return detailPopup.extend({
            defaultValue: ko.observable(0),
            defaults: {
                template: 'ui/catalog/product/detail/grouped'
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
                var self = this;
                if ($('#super_group_' + id).length > 0) {
                    var qty = parseInt($('#super_group_' + id)[0].value) - 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + id)[0].value = parseInt(qty);
                    self.updatePrice(groupedOptions);
                }
            },
            incQty: function (id, groupedOptions) {
                var self = this;
                if ($('#super_group_' + id).length > 0) {
                    var qty = parseInt($('#super_group_' + id)[0].value) + 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + id)[0].value = parseInt(qty);
                    self.updatePrice(groupedOptions);
                }
            },
            increaseQty: function ($obj) {
                var self = this;
                if ($('#super_group_' + $obj.id).length > 0) {
                    var qty = parseInt($('#super_group_' + $obj.id)[0].value) + 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + $obj.id)[0].value = parseInt(qty);
                    self.updatePrice(self.groupedOptions);
                }
            },
            decreaseQty: function ($obj) {
                var self = this;
                if ($('#super_group_' + $obj.id).length > 0) {
                    var qty = parseInt($('#super_group_' + $obj.id)[0].value) - 1;
                    if (qty < 0)
                        qty = 0;
                    $('#super_group_' + $obj.id)[0].value = parseInt(qty);
                    self.updatePrice(self.groupedOptions);
                }
            },
            getDefaultQty: function($parent, $obj){
                var self = $parent;
                self.updatePrice(self.groupedOptions);
                return parseInt($obj.default_qty);
            },
            prepareAddToCart: function(){
                var self = this;
                //self.updatePrice(self.groupedOptions);
                self.prepareAddToCart();
            },
            updatePrice: function (groupedOptions) {
                var self = this;
                if(!groupedOptions){
                    groupedOptions = self.itemData().grouped_options;
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
                     self.groupedProductResult(groupData);
                 }
            }
        });
    }
);