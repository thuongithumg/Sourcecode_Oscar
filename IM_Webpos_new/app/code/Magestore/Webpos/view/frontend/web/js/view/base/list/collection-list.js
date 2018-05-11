/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
        [
            'jquery',
            'ko',
            'Magestore_Webpos/js/model/abstract',
            'Magestore_Webpos/js/view/base/list/abstract',
            'Magestore_Webpos/js/model/event-manager',
            'Magestore_Webpos/js/lib/cookie'
        ],
        function ($, ko, model, listAbstract,eventManager, Cookies) {
            "use strict";

            return listAbstract.extend({
                reload: true,
                loading: ko.observable(false),
                initialize: function () {
                    this.pageSize = 20;
                    this.collection = null;
                    this._super();
                },
                getCollection: function () {
                    this._prepareCollection();
                    return this.collection;
                },
                _render: function () {
                    
                    if(!this.collection) {
                        this._prepareCollection();
                    }
                    this._super();
                    
                    var self = this;
                    if(self.reload && self.collection.getModel().sync_id) {
                        var eventName = self.collection.getModel().sync_id + '_pull_after';
                        eventManager.observer(eventName, function () {
                            if(Cookies.get('check_login')) { // don't run when webpos run synchronization first time
                                self.refresh = true;
                                self.isOnline = false;
                                self.collection.setMode('offline');
                                self._prepareItems();
                            }
                        });

                        var eventsearchName = self.collection.getModel().sync_id + '_search_after';
                        eventManager.observer(eventsearchName, function (event,data) {
                            if(Cookies.get('check_login')) { // don't run when webpos run synchronization first time
                                self.searchKey = String(data);
                                self.refresh = true;
                                self.isOnline = false;
                                self.collection.setMode('offline');
                                self._prepareItems();
                            }
                        });
                    }                    
                },
                _prepareCollection: function () {
                    /* implement in child class */
                },
                _prepareItems: function () {
                    var self = this;
                    if(self.loading() == true){
                        return false;
                    }
                    self.loading(true);
                    if (this.refresh) {
                        this.curPage = 1;
                    }
                    
                    var deferred = self.getCollection().load();
                    self.startLoading();

                    deferred.done(function (data) {
                        self.loading(false);
                        self.finishLoading();
                        self.setItems(data.items);
                    });
                    
                    deferred.fail(function (error) {
                        self.loading(false);
                    });
                },
                filter: function (element, event) {
                    this.searchKey = event.target.value;
                    this.refresh = true;
                    this._prepareItems();
                },
                lazyload: function (element, event) {
                    var scrollHeight = event.target.scrollHeight;
                    var clientHeight = event.target.clientHeight;
                    var scrollTop = event.target.scrollTop;
                    if (scrollTop > (scrollHeight - clientHeight) * 0.6 && this.canLoad() === true) {
                        this.startLoading();
                        this.curPage++;
                        this.refresh = false;
                        this._prepareItems();
                    }
                }
            });
        }
);