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
                template: 'ui/catalog/product/detail/customoption'
            },
            initialize: function () {
                this._super();
            },
            getFormatPrice: function (price, price_type) {
                var self = this;
                if (price_type == 'fixed') {
                    return priceHelper.convertAndFormat(price);
                }
                if (price_type == 'percent') {
                    return priceHelper.convertAndFormat(parseFloat(price) * parseFloat(price) / 100);
                }
            },
            updatePrice: function (customOptions) {
                var self = this;
                var price = self.itemData().final_price;
                var customOptionsValueResult = [];
                var customOptionsLableResult = [];
                $.each(customOptions, function (index, value) {

                    // item is field or textarea
                    if (value.type == 'field' || value.type == 'area') {
                        var i = 0;
                        var selectionArr = [];
                        var lableString = '';
                        if ($("#options_" + value.option_id + "_text").length > 0) {
                            if ($("#options_" + value.option_id + "_text")[0].value) {
                                if (value.price_type == 'fixed') {
                                    price = parseFloat(price) + parseFloat(value.price);
                                }
                                if (value.price_type == 'percent') {
                                    price = parseFloat(price) + parseFloat(price) * parseFloat(value.price) / 100;
                                }
                                selectionArr[i] = $("#options_" + value.option_id + "_text")[0].value;
                                if (i == 0) {
                                    lableString = $("#options_" + value.option_id + "_text")[0].value;
                                    //customOptionsLableResult[value.option_id] = $("#options_" + value.option_id + "_text")[0].value;
                                }
                                if (i != 0) {
                                    lableString += ', '+ $("#options_" + value.option_id + "_text")[0].value;
                                    //customOptionsLableResult[value.option_id] += ', '+ $("#options_" + value.option_id + "_text")[0].value;
                                }
                                i++;
                            }
                        }
                        if (selectionArr.length > 0)
                            customOptionsValueResult.push({id: value.option_id, value: selectionArr});
                            //customOptionsValueResult[value.option_id] = selectionArr;
                        if (lableString.length >0 )
                            customOptionsLableResult.push({id: value.option_id, value: lableString});
                    }

                    // item is selection
                    if (value.type == 'drop_down') {
                        if (($("#select_" + value.option_id).length > 0)
                                && $("#select_" + value.option_id)[0].value) {
                            var selectId = $("#select_" + value.option_id)[0].value;
                            var i = 0;
                            var selectionArr = [];
                            var lableString = '';
                            $.each(value.values, function (index, valueData) {
                                if (valueData.option_type_id == selectId) {
                                    if (valueData.price_type == 'fixed') {
                                        price = parseFloat(price) + parseFloat(valueData.price);
                                    }
                                    if (valueData.price_type == 'percent') {
                                        price = parseFloat(price) + parseFloat(price) * parseFloat(valueData.price) / 100;
                                    }
                                    selectionArr[i] = valueData.option_type_id;
                                    if (i == 0) {
                                        lableString = valueData.title;
                                        //customOptionsLableResult[value.option_id] = valueData.title;
                                    }
                                    if (i != 0) {
                                        lableString += ', '+ valueData.title;
                                        //customOptionsLableResult[value.option_id] += ', '+ valueData.title;
                                    }
                                    i++;
                                }
                            });
                            if (selectionArr.length > 0)
                                customOptionsValueResult.push({id: value.option_id, value: selectionArr});
                                //customOptionsValueResult[value.option_id] = selectionArr;
                            if (lableString.length >0 )
                                customOptionsLableResult.push({id: value.option_id, value: lableString});
                        }
                    }

                    // item is radio
                    if (value.type == 'radio') {
                        var i = 0;
                        var selectionArr = [];
                        var lableString = '';
                        $.each(value.values, function (index, valueData) {
                            if ($("#options_" + value.option_id + "_" + valueData.sort_order).length > 0
                                && $("#options_" + value.option_id + "_" + valueData.sort_order)[0].checked) {
                                if (valueData.option_type_id == $("#options_" + value.option_id + "_" + valueData.sort_order)[0].value) {
                                    if (valueData.price_type == 'fixed') {
                                        price = parseFloat(price) + parseFloat(valueData.price);
                                    }
                                    if (valueData.price_type == 'percent') {
                                        price = parseFloat(price) + parseFloat(price) * parseFloat(valueData.price) / 100;
                                    }
                                    selectionArr[i] = valueData.option_type_id;
                                    if (i == 0) {
                                        lableString = valueData.title;
                                        //customOptionsLableResult[value.option_id] = valueData.title;
                                    }
                                    if (i != 0) {
                                        lableString += ', '+ valueData.title;
                                        //customOptionsLableResult[value.option_id] += ', '+ valueData.title;
                                    }
                                    i++;
                                }
                            }
                        });
                        if (selectionArr.length > 0)
                            customOptionsValueResult.push({id: value.option_id, value: selectionArr});
                            //customOptionsValueResult[value.option_id] = selectionArr;
                        if (lableString.length >0 )
                            customOptionsLableResult.push({id: value.option_id, value: lableString});
                    }

                    // item is checkbox
                    if (value.type == 'checkbox') {
                        var i = 0;
                        var selectionArr = [];
                        var lableString = '';
                        $.each(value.values, function (index, valueData) {
                            if ($("#options_" + value.option_id + "_" + valueData.sort_order).length > 0
                                && $("#options_" + value.option_id + "_" + valueData.sort_order)[0].checked) {
                                if (valueData.option_type_id == $("#options_" + value.option_id + "_" + valueData.sort_order)[0].value) {
                                    if (valueData.price_type == 'fixed') {
                                        price = parseFloat(price) + parseFloat(valueData.price);
                                    }
                                    if (valueData.price_type == 'percent') {
                                        price = parseFloat(price) + parseFloat(price) * parseFloat(valueData.price) / 100;
                                    }
                                    selectionArr[i] = valueData.option_type_id;
                                    if (i == 0) {
                                        lableString = valueData.title;
                                        //customOptionsLableResult[value.option_id] = valueData.title;
                                    }
                                    if (i != 0) {
                                        lableString += ', '+ valueData.title;
                                        //customOptionsLableResult[value.option_id] += ', '+ valueData.title;
                                    }
                                    i++;
                                }
                            }
                        });
                        if (selectionArr.length > 0)
                            customOptionsValueResult.push({id: value.option_id, value: selectionArr});
                            //customOptionsValueResult[value.option_id] = selectionArr;
                        if (lableString.length >0 )
                            customOptionsLableResult.push({id: value.option_id, value: lableString});
                    }

                    // item is checkbox
                    if (value.type == 'multiple') {
                        var i = 0;
                        var selectionArr = [];
                        var lableString = '';
                        if ($("#select_" + value.option_id).length > 0) {
                            var optionsSelect = $("#select_" + value.option_id).val();
                            if (optionsSelect) {
                                $.each(value.values, function (index, valueData) {
                                    if (optionsSelect.indexOf(String(valueData.option_type_id)) > -1) {
                                        if (valueData.price_type == 'fixed') {
                                            price = parseFloat(price) + parseFloat(valueData.price);
                                        }
                                        if (valueData.price_type == 'percent') {
                                            price = parseFloat(price) + parseFloat(price) * parseFloat(valueData.price) / 100;
                                        }
                                        selectionArr[i] = valueData.option_type_id;
                                        if (i == 0) {
                                            lableString = valueData.title;
                                            //customOptionsLableResult[value.option_id] = valueData.title;
                                        }
                                        if (i != 0) {
                                            lableString += ', '+ valueData.title;
                                            //customOptionsLableResult[value.option_id] += ', '+ valueData.title;
                                        }
                                        i++;
                                    }
                                });
                            }
                        }
                        if (selectionArr.length > 0)
                            customOptionsValueResult.push({id: value.option_id, value: selectionArr});
                            //customOptionsValueResult[value.option_id] = selectionArr;
                        if (lableString.length >0 )
                            customOptionsLableResult.push({id: value.option_id, value: lableString});
                    }
                });
                self.customOptionsValueResult(customOptionsValueResult);
                self.customOptionsLableResult(customOptionsLableResult);
                self.basePriceAmount(price);
                self.defaultPriceAmount(priceHelper.convertAndFormat(price));
            }
        });
    }
);