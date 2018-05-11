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
            'model/abstract',
            'view/base/list/abstract'
            // 'model/event-manager'
        ],
        function ($, ko, model, listAbstract
            // ,eventManager
        ) {
            "use strict";

            return listAbstract.extend({
                reload: true,
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
                        //this._prepareCollection();
                    }
                    this._super();
                    
                    var self = this;
                    if(self.reload && self.collection.getModel().sync_id) {
                        var eventName = self.collection.getModel().sync_id + '_pull_after';
                        // eventManager.observer(eventName, function () {
                        //     self.refresh = true;
                        //     self.isOnline = false;
                        //     self.collection.setMode('offline');
                        //     self._prepareItems();
                        // });

                        var eventsearchName = self.collection.getModel().sync_id + '_search_after';
                        // eventManager.observer(eventsearchName, function (event,data) {
                        //     self.searchKey = String(data);
                        //     self.refresh = true;
                        //     self.isOnline = false;
                        //     self.collection.setMode('offline');
                        //     self._prepareItems();
                        // });
                    }                    
                },
                _prepareCollection: function () {
                    /* implement in child class */
                },
                _prepareItems: function () {
                    var self = this;
                    if (this.refresh) {
                        this.curPage = 1;
                    }
                    
                    var deferred = self.getCollection().load();
                    self.startLoading();

                    deferred.done(function (data) {
                        self.finishLoading();
                        self.setItems(data.items);
                    });
                    
                    deferred.fail(function (error) {

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