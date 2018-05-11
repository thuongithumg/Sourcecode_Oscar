/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Magestore_Webpos/js/accounting.min'
], function ($, priceUtils, accounting) {
    'use strict';

    return {
        formatPrice: formatPrice,
        formatPriceWithoutSymbol: formatPriceWithoutSymbol,
        currencyConvert:currencyConvert,
        convertAndFormat:convertAndFormat,
        convertAndFormatWithoutSymbol:convertAndFormatWithoutSymbol,
        toNumber: toNumber,
        toPositiveNumber: toPositiveNumber,
        toBasePrice: toBasePrice,
        correctPrice: correctPrice,
        roundPrice: roundPrice,
        comparePrice: comparePrice,
    };

    function formatPrice(amount) {
        amount = parseFloat(amount);
        amount = toNumber(amount);
        var correctedAmount = amount;
        var priceFormat = window.webposConfig.priceFormat;
        return priceUtils.formatPrice(correctedAmount, priceFormat);
    }

    function formatPriceWithoutSymbol(amount) {
        amount = parseFloat(amount);
        amount = toNumber(amount);
        var correctedAmount = amount;
        var priceFormat = window.webposConfig.priceFormat;
        var newPriceFormat = $.extend(true, {}, priceFormat);
        newPriceFormat.pattern = '%s';
        return priceUtils.formatPrice(correctedAmount, newPriceFormat);
    }

    function currencyConvert(amount,from,to) {
        if(!from){
            from = window.webposConfig.baseCurrencyCode;
        }
        if(!to){
            to = window.webposConfig.currentCurrencyCode;
        }
        var fromRate = 0;
        var toRate = 0;
        var currencies = window.webposConfig.currencies;
        if(currencies && currencies[from] && currencies[from]['currency_rate']) {
            fromRate = currencies[from]['currency_rate'];
        }
        if(currencies && currencies[to] && currencies[to]['currency_rate']) {
            toRate = currencies[to]['currency_rate'];
        }
        if(fromRate && toRate) {
            amount = parseFloat(amount) * parseFloat(toRate) / parseFloat(fromRate);
        }
        return amount;
    }
    function convertAndFormat(amount,from,to) {
        amount = currencyConvert(amount,from,to);
        return formatPrice(amount);
    }

    function convertAndFormatWithoutSymbol(amount,from,to) {
        amount = currencyConvert(amount,from,to);
        return formatPriceWithoutSymbol(amount);
    }

    function toNumber(string) {
        var priceFormat = window.webposConfig.priceFormat;
        var decimalSymbolNumber = priceFormat.decimalSymbol;
        var groupSymbolNumber = priceFormat.groupSymbol;
        var result = accounting.unformat(string, decimalSymbolNumber);
        //result = accounting.formatNumber(result, 4, decimalSymbolNumber);
        return result;
    }

    function toPositiveNumber(amount) {
        if(!amount){
            return 0;
        }
        amount = parseFloat(amount);
        amount = toNumber(amount);
        amount = Math.abs(amount);
        return amount;
    }
    
    function toBasePrice(amount){
        var from = window.webposConfig.currentCurrencyCode;
        var to = window.webposConfig.baseCurrencyCode;
        return currencyConvert(amount,from,to);
    }
    
    function roundPrice(amount){
        amount = toNumber(amount);
        var priceFormat = window.webposConfig.priceFormat;
        // var correctedAmount = amount.toFixed(priceFormat.precision);
        var correctedAmount = Math.floor(amount * Math.pow(10,priceFormat.precision))/Math.pow(10,priceFormat.precision);
        return parseFloat(correctedAmount);
    }

    function correctPrice(amount){
        amount = toNumber(amount);
        var priceFormat = window.webposConfig.priceFormat;
        var correctedAmount = amount.toFixed(priceFormat.precision);
        return parseFloat(correctedAmount);
    }
    
    function comparePrice(amountA, amountB) {
        if(Math.round(amountA * 100) > Math.round(amountB * 100)) {
            return 1;
        } else if(Math.round(amountA * 100) < Math.round(amountB * 100)) {
            return -1;
        }
        return 0;
    }
});