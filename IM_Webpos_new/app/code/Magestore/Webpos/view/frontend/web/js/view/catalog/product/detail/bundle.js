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
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/bundle'
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
                if(!bundleOptions){
                   bundleOptions = this.itemData().bundle_options;
                }
                var price = this.itemData().final_price;
                var bundleOptionsValueResult = [];
                var bundleOptionsQtyResult = [];
                var bundleChildsQtyResult = [];
                var bundleOptionsLableResult = [];
                $.each(bundleOptions, function (index, value) {
                    var itemsData = [];
                    itemsData = this.convertToArray(value.items);

                    /* type of item is radio */
                    if (value.type == 'radio') {
                        var i = 0;
                        var selectionArr = [];
                        $.each(itemsData, function (indexItem, valueItem) {
                            if (($("#bundle-option-" + valueItem.option_id + "-" + valueItem.selection_id).length > 0)
                                && $("#bundle-option-" + valueItem.option_id + "-" + valueItem.selection_id)[0].checked) {
                                var qty = 1;
                                if ($("#bundle-option-" + valueItem.option_id + "-qty-input").length > 0) {
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
                                    if ($("#bundle-option-" + valueItem.option_id + "-qty-input").length > 0) {
                                        qty = $("#bundle-option-" + valueItem.option_id + "-qty-input")[0].value;
                                    }
                                    selectionArr[i] = valueItem.selection_id;
                                    i++;
                                    //bundleOptionsQtyResult[valueItem.selection_id] = qty;
                                    //bundleOptionsLableResult[valueItem.selection_id] = parseInt(qty) + ' x ' + valueItem.name;
                                    bundleOptionsQtyResult.push({id: valueItem.selection_id, value: qty});
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
                }.bind(this));
                this.basePriceAmount(price);
                this.defaultPriceAmount(priceHelper.convertAndFormat(price));
                this.bundleOptionsValueResult(bundleOptionsValueResult);
                this.bundleOptionsQtyResult(bundleOptionsQtyResult);
                this.bundleChildsQtyResult(bundleChildsQtyResult);
                this.bundleOptionsLableResult(bundleOptionsLableResult);
            }
        });
    }
);