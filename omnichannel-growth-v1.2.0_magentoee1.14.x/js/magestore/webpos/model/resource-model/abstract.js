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
        'uiClass'
    ],
    function ($, Class) {
        "use strict";

        return Class.extend({
            off_id_auto:'off_id_auto_',
            prepareSaveData: function(data) {
                var self = this;
                var saveData = {};
                for(var i in data) {
                    if(typeof data[i] !== 'function') {
                        saveData[i] = this._prepareChildData(data[i]);
                    }
                    if(typeof data[i] == 'string' && data[i].indexOf(self.off_id_auto) >= 0) {
                        saveData['indexeddb_id'] = data[i];
                    }
                }
                return saveData;            
            },
            _prepareChildData: function(data) {
                if(data === null) {
                    return data;
                }
                if(typeof data === 'undefined') {
                    return data;
                }
                var saveData = data;
                if(data.constructor.toString().indexOf("Array") > -1) {
                    saveData = [];
                    for(var i in data) {
                        if(typeof data[i] !== 'function') {
                            saveData.push(this._prepareChildData(data[i]));
                        }                   
                    }                  
                } else if(data.constructor.toString().indexOf("Object") > -1) {
                    saveData = {};
                    for(var i in data) {
                        if(typeof data[i] !== 'function') {
                            saveData[i] = this._prepareChildData(data[i]);
                        }                   
                    }
                }

                return saveData;
            }
        });
    }
);