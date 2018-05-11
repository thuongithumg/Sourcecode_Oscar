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
        'uiElement',
        'eventManager'
    ],
    function ($, Class, eventmanager) {
        "use strict";

        return Class.extend({
            /* Set Mode For Collection*/
            mode: 'offline',
            model: {},
            /* Query Params*/
            queryParams: {
                filterParams: [],
                orderParams: [],
                pageSize: '',
                currentPage: '',
                paramToFilter: [],
                paramOrFilter: []
            },
            /* Get Current Mode*/
            getMode: function () {
                return this.mode;
            },
            /* Set Mode For Collection*/
            setMode: function (mode) {
                this.mode = mode;
                return this;
            },
            /**
             * Set model to colelction
             */
            setModel: function (model) {
                this.model = model;
                return this;
            },
            /**
             * Get model from Collection
             */
            getModel: function () {
                return this.model;
            },
            /* Get Data*/
            getData: function () {
                return this.data;
            },
            /* Set data For Collection*/
            setData: function (data) {
                this.data = data;
                return this;
            },
            /* Set Resource Offline Or Online*/
            setResource: function (onlineResource, offlineResource) {
                this.onlineResource = onlineResource;
                this.offlineResource = offlineResource;
                return this;
            },
            /* Get Current Resource*/
            getResource: function () {
                if (this.getMode() == 'offline') {
                    return this.offlineResource;
                } else {
                    return this.onlineResource;
                }
            },
            /* Get Resource Online*/
            getResourceOnline: function () {
                return this.onlineResource;
            },
            /* Get Resource Offline*/
            getResourceOffline: function () {
                return this.offlineResource;
            },
            /* Set Order*/
            setOrder: function (field, direction) {
                var orderCondition = {
                    "field": field,
                    "direction": direction
                };
                this.queryParams.orderParams.push(orderCondition);
                return this;
            },
            /* Collection Filter*/
            addFieldToFilter: function (field, value, condition) {
                var self = this;
                var filterCondition = {
                    "field": field,
                    "value": value,
                    "condition": condition
                };
                if ($.isArray(field)) {
                    var orFilter = [];
                    $.each(field, function (index, value) {
                        filterCondition = {
                            "field": value[0],
                            "value": value[1],
                            "condition": value[2]
                        };
                        orFilter.push(filterCondition);
                    });
                    self.queryParams.paramOrFilter.push(orFilter);
                } else {
                    this.queryParams.filterParams.push(filterCondition);
                }

                return this;
            },
            /* Filter by param*/
            addParamToFilter: function (field, value) {
                var paramCondition = {
                    "field": field,
                    "value": value
                };
                this.queryParams.paramToFilter.push(paramCondition);
                return this;
            },
            _loadBefore: function() {
                var eventData = {'collection': this};
                // eventmanager.dispatch('collection_load_before', eventData);
                if(this.getModel().event_prefix){
                    eventmanager.dispatch(this.getModel().event_prefix + '_collection_load_before', eventData);
                }
            },
            /* Load Collection*/
            load: function (deferred) {
                if(!deferred)
                    deferred = $.Deferred();
                if (!this.getResource())
                    return;
                var self = this;
                self._loadBefore();
                var cdeferred = this.getResource().queryCollectionData(this);
                cdeferred.done(function (data){
                    self.setData(data);
                    /* after load collection */
                    self._loadAfter(data);
                    if(self.deferred) {
                        self.deferred.done(function(eventdata){
                            deferred.resolve(data);
                            self.reset();
                        });
                    } else {
                        deferred.resolve(data);
                        self.reset();
                    }
                }).fail(function (data){
                     deferred.reject(data);
                     self.reset();
                }).always(function(response){
                    self.reset();
                });
                //self.reset();
                return deferred;
            },
            _loadAfter: function(response) {
                var eventData = {'collection': this, 'response': response};
                // eventmanager.dispatch('collection_load_after', eventData);
                if(this.getModel().event_prefix){
                    eventmanager.dispatch(this.getModel().event_prefix + '_collection_load_after', eventData);
                }
            },
            /* Set Page Size*/
            setPageSize: function (pageSize) {
                this.queryParams.pageSize = pageSize;
                return this;
            },
            /* Set Current Page*/
            setCurPage: function (curPage) {
                this.queryParams.currentPage = curPage;
                return this;
            },
            /* Reset Collection*/
            reset: function () {
                var self = this;
                //self.cleanData();
                //self.destroy();
                this.queryParams.filterParams = [];
                this.queryParams.orderParams = [];
                this.queryParams.currentPage = '';
                this.queryParams.pageSize = '';
                this.queryParams.paramToFilter = [];
                this.queryParams.paramOrFilter = [];
                return this;
            }
        });
    }
);