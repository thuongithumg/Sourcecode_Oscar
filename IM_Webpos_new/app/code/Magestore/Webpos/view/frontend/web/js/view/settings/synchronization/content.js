/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/lib/cookie'
    ],
    function ($, ko, Component, Cookies) {
        "use strict";

        return Component.extend({
            headerName: ko.observable('Sync Data'),
            initialize: function () {
                this._super();
            },
            changeMenu: function (self, data) {
                self.headerName(data.label);
            },
            resetLocalDatabase: function(){
                var indexedDB = this.getIndexedDB();
                if(indexedDB){
                    indexedDB.deleteDatabase('magestore_webpos');
                }
                Cookies.set('check_login', 1, { expires: parseInt(window.webposConfig.timeoutSession) });
                window.location.reload();
            },
            getIndexedDB: function() {
                if ( !indexedDB ) {
                    indexedDB = window.indexedDB || window.webkitIndexedDB || window.mozIndexedDB || window.oIndexedDB || window.msIndexedDB || ((window.indexedDB === null && window.shimIndexedDB) ? window.shimIndexedDB : undefined);

                    if ( !indexedDB ) {
                        return false;
                    }
                }
                return indexedDB;
            }
        });
    }
);