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
        'uiComponent',
        'mage/translate',
        'helper/price',
        'helper/alert',
        'helper/datetime',
        'action/notification/add-notification'
        // 'model/event-manager'
    ],
    function ($, ko, Component, Translate, HelperPrice, Alert, HelperDatetime, AddNotification
              // EventManager
    ) {
        "use strict";
        return Component.extend({
            defaults: {
                template: ''
            },
            initialize: function () {
                this._super();
            },
            alert: function(priority, title, message){
                Alert({
                    priority: priority,
                    title: title,
                    message: message
                });
            },
            convertAndFormatWithoutSymbol: function(amount,from,to){
                return HelperPrice.convertAndFormatWithoutSymbol(amount,from,to);
            },
            convertAndFormatPrice: function(amount,from,to){
                return HelperPrice.convertAndFormat(amount,from,to);
            },
            convertPrice: function(amount,from,to){
                return HelperPrice.currencyConvert(amount,from,to);
            },
            formatPriceWithoutSymbol: function(value){
                return HelperPrice.formatPriceWithoutSymbol(value);
            },
            formatPrice: function(value){
                return HelperPrice.formatPrice(value);
            },
            toBasePrice: function(value){
                return HelperPrice.toBasePrice(value);
            },
            getPriceHelper: function(){
                return HelperPrice;
            },
            __: function(string){
                return Translate(string);
            },
            getDatetimeHelper: function(){
                return HelperDatetime;
            },
            addNotification: function(message, showAlert, alertPriority, alertTitle){
                AddNotification(message, showAlert, alertPriority, alertTitle);
            },
            // dispatchEvent: function(eventName, data, timeout){
            //     EventManager.dispatch(eventName, data, timeout);
            // },
            // observerEvent: function(eventName, function_callback){
            //     EventManager.observer(eventName, function_callback);
            // },
            getObject: function(objectPath){

            },
            setModel : function(modelpath){
                this.model = this.getObject(modelpath);
            }
        });
    }
);
