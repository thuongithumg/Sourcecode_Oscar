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
            defaults: {
                template: 'ui/catalog/product/detail/bundle'
            },
            initialize: function () {
                this._super();
                detailPopup().bundleItem(this);
            },
            convertToArray: function (items) {
                var bundleItems = [];
                for (var i in items) {
                    bundleItems.push(items[i]);
                }
                return bundleItems;
            },
            getClassOption: function (data) {
                var className = 'field option';
                if (data.required) {
                    return className + ' required';
                }
                return className;
            },
            getSelectionTitlePrice: function (price) {
                return priceHelper.convertAndFormat(price);
            },
            updatePrice: function (bundleOptions) {
                var self = this;
                if(!bundleOptions){
                   bundleOptions = self.itemData().bundle_options;
                }

                var price = priceHelper.toNumber(self.itemData().final_price);
                var bundleOptionsValueResult = [];
                var bundleOptionsQtyResult = [];
                var bundleChildsQtyResult = [];
                var bundleOptionsLableResult = [];
                $.each(bundleOptions, function (index, value) {
                    var itemsData = [];
                    itemsData = self.convertToArray(value.items);
					$.each(itemsData, function (indexItem, valueItem) {
                        if ($("#bundle-option-" + valueItem.option_id + "-qty-input").length > 0) {
                        	$("#bundle-option-" + valueItem.option_id + "-qty-input").closest(".qty-holder").hide();
                        }
                    });
                    /* type of item is radio */
                    if (value.type == 'radio') {
                        var i = 0;
                        var selectionArr = [];
                        $.each(itemsData, function (indexItem, valueItem) {
                            if (($("#bundle-option-" + valueItem.option_id + "-" + valueItem.selection_id).length > 0)
                                && $("#bundle-option-" + valueItem.option_id + "-" + valueItem.selection_id)[0].checked) {
                                var qty = 1;
                                var selection_can_change_qty = valueItem.selection_can_change_qty;
                                if ($("#bundle-option-" + valueItem.option_id + "-qty-input").length > 0) {
                                    if(selection_can_change_qty == 0){
                                        $("#bundle-option-" + valueItem.option_id + "-qty-input").closest(".qty-holder").hide();
                                    }else{
                                        $("#bundle-option-" + valueItem.option_id + "-qty-input").closest(".qty-holder").show();
                                    }
                                    qty = $("#bundle-option-" + valueItem.option_id + "-qty-input")[0].value;
                                }
                                selectionArr[i] = valueItem.selection_id;
                                i++;
                                //bundleOptionsQtyResult[valueItem.selection_id] = qty;
                                bundleOptionsQtyResult.push({id: valueItem.option_id, value: qty});
                                bundleChildsQtyResult.push({id: valueItem.entity_id, value: qty});
                                //bundleOptionsLableResult[valueItem.selection_id] = parseInt(qty) + ' x ' + valueItem.name;
                                bundleOptionsLableResult.push({id: valueItem.selection_id, value: parseInt(qty) + ' x ' + valueItem.name});
                                price = parseFloat(price) + parseFloat(qty) * parseFloat(valueItem.price);
                            }
                        });
                        if (selectionArr.length > 0)
                            //bundleOptionsValueResult[value.id] = selectionArr;
                            bundleOptionsValueResult.push({id: value.id, value: selectionArr});
                    }

                    /* type of item is select */
                    if (value.type == 'select') {
                        var i = 0;
                        var selectionArr = [];
                        $.each(itemsData, function (indexItem, valueItem) {
                            if (($("#bundle-option-" + valueItem.option_id).length > 0)
                                && $("#bundle-option-" + valueItem.option_id)[0].value) {
                                var selectId = $("#bundle-option-" + valueItem.option_id)[0].value;
                                if (valueItem.selection_id == selectId) {
                                    var qty = 1;
                                    var selection_can_change_qty = valueItem.selection_can_change_qty;
                                    if ($("#bundle-option-" + valueItem.option_id + "-qty-input").length > 0) {
                                        if(selection_can_change_qty == 0){
                                            $("#bundle-option-" + valueItem.option_id + "-qty-input").closest(".qty-holder").hide();
                                        }else{
                                            $("#bundle-option-" + valueItem.option_id + "-qty-input").closest(".qty-holder").show();
                                        }
                                        qty = $("#bundle-option-" + valueItem.option_id + "-qty-input")[0].value;
                                    }
                                    selectionArr[i] = valueItem.selection_id;
                                    i++;
                                    //bundleOptionsQtyResult[valueItem.selection_id] = qty;
                                    //bundleOptionsLableResult[valueItem.selection_id] = parseInt(qty) + ' x ' + valueItem.name;
                                    bundleOptionsQtyResult.push({id: valueItem.option_id, value: qty});
                                    bundleChildsQtyResult.push({id: valueItem.entity_id, value: qty});
                                    bundleOptionsLableResult.push({id: valueItem.selection_id, value: parseInt(qty) + ' x ' + valueItem.name});
                                    price = parseFloat(price) + parseFloat(qty) * parseFloat(valueItem.price);
                                }
                            }
                        });
                        if (selectionArr.length > 0)
                            //bundleOptionsValueResult[value.id] = selectionArr;
                            bundleOptionsValueResult.push({id: value.id, value: selectionArr});
                    }

                    /* type of item is checkbox */
                    if (value.type == 'checkbox') {
                        var i = 0;
                        var selectionArr = [];
                        $.each(itemsData, function (indexItem, valueItem) {
                            if (($("#bundle-option-" + valueItem.option_id + "-" + valueItem.selection_id).length > 0)
                                && $("#bundle-option-" + valueItem.option_id + "-" + valueItem.selection_id)[0].checked) {
                                var qty = valueItem.selection_qty;
                                var selection_can_change_qty = valueItem.selection_can_change_qty;
                                if ($("#bundle-option-" + valueItem.option_id + "-qty-input").length > 0) {
                                    if(selection_can_change_qty == 0){
                                        $("#bundle-option-" + valueItem.option_id + "-qty-input").closest(".qty-holder").hide();
                                    }else{
                                        $("#bundle-option-" + valueItem.option_id + "-qty-input").closest(".qty-holder").show();
                                    }
                                    qty = $("#bundle-option-" + valueItem.option_id + "-qty-input")[0].value;
                                }
                                selectionArr[i] = valueItem.selection_id;
                                i++;
                                //bundleOptionsQtyResult[valueItem.selection_id] = qty;
                                //bundleOptionsLableResult[valueItem.selection_id] = parseInt(qty) + ' x ' + valueItem.name;
                                //bundleOptionsQtyResult.push({id: valueItem.selection_id, value: qty});
                                bundleOptionsLableResult.push({id: valueItem.selection_id, value: parseInt(qty) + ' x ' + valueItem.name});
                                price = parseFloat(price) + parseFloat(qty) * parseFloat(valueItem.price);
                            }
                        });
                        if (selectionArr.length > 0)
                            //bundleOptionsValueResult[value.id] = selectionArr;
                            bundleOptionsValueResult.push({id: value.id, value: selectionArr});
                    }

                    /* type of item is multi */
                    if (value.type == 'multi') {
                        var i = 0;
                        var selectionArr = [];
                        if ($("#bundle-option-" + value.id).length > 0) {
                            var optionsSelect = $("#bundle-option-" + value.id).val();
                            if (optionsSelect) {
                                $.each(itemsData, function (indexItem, valueItem) {
                                    if (optionsSelect.indexOf(valueItem.selection_id) > -1) {
                                        var qty = valueItem.selection_qty;
                                        selectionArr[i] = valueItem.selection_id;
                                        i++;
                                        //bundleOptionsQtyResult[valueItem.selection_id] = qty;
                                        //bundleOptionsLableResult[valueItem.selection_id] = parseInt(qty) + ' x ' + valueItem.name;
                                        //bundleOptionsQtyResult.push({id: valueItem.selection_id, value: qty});
                                        bundleOptionsLableResult.push({id: valueItem.selection_id, value: parseInt(qty) + ' x ' + valueItem.name});
                                        price = parseFloat(price) + parseFloat(qty) * parseFloat(valueItem.price);
                                    }
                                });
                            }
                        }
                        if (selectionArr.length > 0)
                            //bundleOptionsValueResult[value.id] = selectionArr;
                            bundleOptionsValueResult.push({id: value.id, value: selectionArr});
                    }
                });
                self.basePriceAmount(price);
                self.defaultPriceAmount(priceHelper.convertAndFormat(price));
                self.bundleOptionsValueResult(bundleOptionsValueResult);
                self.bundleOptionsQtyResult(bundleOptionsQtyResult);
                self.bundleChildsQtyResult(bundleChildsQtyResult);
                self.bundleOptionsLableResult(bundleOptionsLableResult);
            }
        });
    }
);