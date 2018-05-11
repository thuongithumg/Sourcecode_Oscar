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
        'jquery'
    ],
    function ($) {
        "use strict";
        return {
            prepareKey: function(key) {
                var staffId = 1;
                return 'webpos_' + staffId + '_' + key;
            },
            prepareValueForSave: function (value) {
                return value;
            },
            prepareValueForGet: function (value) {
                return value;
            },            
            save: function(key, value) {
                key = this.prepareKey(key);
                value = this.prepareValueForSave(value);
                return localStorage.setItem(key, value);
            },
            get: function(key) {
                key = this.prepareKey(key);
                var value = localStorage.getItem(key);  
                return this.prepareValueForGet(value);             
            },
        };
    }
);