/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiElement'
    ],
    function ($, Element) {
        "use strict";

        return Element.extend({
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