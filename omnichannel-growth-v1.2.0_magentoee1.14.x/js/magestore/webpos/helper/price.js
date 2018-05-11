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

define([
    'jquery',
    'accounting'
], function ($, accounting) {
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
        comparePrice: comparePrice,
        currentCurrencyCode: window.webposConfig.currentCurrencyCode,
        baseCurrencyCode: window.webposConfig.baseCurrencyCode
    };



    function formatPrice(amount) {
        amount = parseFloat(amount);
        amount = toNumber(amount);
        var correctedAmount = amount;
        var priceFormat = window.webposConfig.priceFormat;
        return formatCurrency(correctedAmount, priceFormat);
    }

    function formatPriceWithoutSymbol(amount) {
        amount = parseFloat(amount);
        amount = toNumber(amount);
        var correctedAmount = amount;
        var priceFormat = window.webposConfig.priceFormat;
        var newPriceFormat = $.extend(true, {}, priceFormat);
        newPriceFormat.pattern = '%s';
        return formatCurrency(correctedAmount, newPriceFormat);
    }

    function currencyConvert(amount,from,to) {
        var currencyRates = window.webposConfig.currencyRates;
        var fromRate = 1;
        var toRate = 1;
        if(typeof from == 'undefined' || !from){
            from = window.webposConfig.baseCurrencyCode;
        }
        if(typeof currencyRates[from] != 'undefined') {
            fromRate = currencyRates[from];
        }
        if(typeof to == 'undefined' || !to){
            to = window.webposConfig.currentCurrencyCode;
        }
        if(typeof currencyRates[to] != 'undefined') {
            toRate = currencyRates[to];
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
        var result;
        result = accounting.unformat(string, decimalSymbolNumber);
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
    
    function correctPrice(amount){
        amount = parseFloat(amount);
        var priceFormat = window.webposConfig.priceFormat;
        var correctedAmount = amount.toFixed(priceFormat.precision);
        correctedAmount = parseFloat(correctedAmount);
        var currencyRates = window.webposConfig.currencyRates;
        var baseCurrencyCode = window.webposConfig.baseCurrencyCode;
        var currentCurrencyCode = window.webposConfig.currentCurrencyCode;
        var toRate = 1;
        var fromRate = 1;
        if(typeof currencyRates[baseCurrencyCode] != 'undefined') {
            fromRate = currencyRates[baseCurrencyCode];
        }
        if(typeof currencyRates[currentCurrencyCode] != 'undefined') {
            toRate = currencyRates[currentCurrencyCode];
        }
        return ((fromRate/toRate) < 1)?amount:correctedAmount;
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