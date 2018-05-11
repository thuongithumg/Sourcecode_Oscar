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
        'eventManager',
        'model/appConfig'
    ],
    function ($, ko, Event, AppConfig) {
        "use strict";

        var DataManager = {
            /**
             * Data JSON
             */
            _data: ko.observable({}),
            /**
             * Available keys
             */
            _availableKeys: ko.observableArray([]),
            /**
             * Initialize
             * @returns {DataManager}
             */
            initialize: function(){
                var self = this;
                return self;
            },
            /**
             *
             * @param key
             * @returns {*}
             */
            getData: function(key){
                var self = this;
                var data = self._data();
                return (key)?data[key]:data;
            },
            /**
             * Set data
             * @param key
             * @param value
             */
            setData: function(key, value){
                var self = this;
                var data = self._data();
                if(typeof key == 'object'){
                    data = key;
                }else{
                    data[key] = value;
                }
                self._data(data);
                self._initAvailableDataKeys();
                Event.dispatch(AppConfig.EVENT.DATA_MANAGER_SET_DATA_AFTER, self.getData());
            },
            /**
             * Get all available data key
             * @returns {Array}
             */
            _initAvailableDataKeys: function(){
                var self = this;
                var data = self.getData();
                var keys = [];
                $.each(data, function(index){
                    keys.push(index);
                });
                self._availableKeys(keys);
            },
            /**
             * Get all available data key
             * @returns {Array}
             */
            getAvailableDataKeys: function(){
                var self = this;
                return self._availableKeys();
            }
        };
        return DataManager.initialize();
    }
);