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
        'uiElement',
        'model/collection/abstract',
        'eventManager'
    ],
    function ($, ko, Element, resourceCollection, eventmanager) {
        "use strict";

        return Element.extend({
            /* Declare mode*/
            mode: "offline",
            log: true,
            push: false,
            event_prefix: "",
            sync_id:'',
            removeFuncBeforeMassUpdate: false,
            /* Declare data*/
            data: '',
            /* Get current mode*/
            getMode: function() {
                return this.mode;
            },
            /* Set mode for model*/
            setMode: function (mode) {
                this.mode = mode;
                return this;
            },
            setPush: function (mode) {
                this.push = mode;
                return this;
            },
            setLog: function (mode) {
                this.log = mode;
                return this;
            },
            /* Set resource*/
            setResource : function(onlineResource, offlineResource){
                this.onlineResource = onlineResource;
                this.offlineResource = offlineResource;
                return this;
            },
            /* Get current resource*/
            getResource : function () {
                if (this.getMode() == 'offline') {
                    return this.offlineResource;
                } else {
                    try {
                        this.onlineResource.setPush(this.push).setLog(this.log);
                    }catch(err) {
                    }
                    return this.onlineResource;
                }
            },
            /* Get resource online*/
            getResourceOnline : function(){
                try {
                    this.onlineResource.setPush(this.push).setLog(this.log);
                }catch(err) {
                }
                return this.onlineResource;
            },
            /* Get resource offline*/
            getResourceOffline : function(){
                return this.offlineResource;
            },
            /* Set resource collection*/
            setResourceCollection : function(resourceCollection){
                this.resourceCollection = resourceCollection;
                return this;
            },
            /* Get resource collection*/
            getResourceCollection : function(){
                return this.resourceCollection;
            },
            /* Get collection*/
            getCollection : function () {
                //đừng có bắn log nữa, chỉ bắn ra khi test thôi, khi push lên dev thì xoá log đi
                return this.resourceCollection.setMode(this.getMode()).setModel(this);
            },
            /* Get event prefix */
            getEventPrefix: function() {
                return this.event_prefix;
            },
            /* Load model by id*/
            load : function(id, deferred){
                if(!deferred)
                    deferred = $.Deferred();

                this.getResource().load(id, deferred);
                return deferred;
            },
            /* Save model by id*/
            save : function (deferred) {
                var self = this;
                if(!deferred)
                    deferred = $.Deferred();
                self._saveBefore();
                self.getResource().save(self, deferred);
                /* after saved */
                deferred.always(function (response) {
                    self._saveAfter(response);
                });
                return deferred;
            },
            update : function (deferred) {
                var self = this;
                if(!deferred)
                    deferred = $.Deferred();
                self._saveBefore();
                self.getResource().update(self, deferred);
                /* after saved */
                deferred.always(function (response) {
                    self._saveAfter(response);
                });
                return deferred;
            },
            _saveBefore: function(){
                var eventData = {'model' : this};
                eventmanager.dispatch('model_save_before', eventData);
                if(this.event_prefix){
                    eventmanager.dispatch(this.event_prefix + '_save_before', eventData);
                }
            },
            _saveAfter: function(response){
                var eventData = {'model' : this, 'response': response};
                eventmanager.dispatch('model_save_after', eventData);
                if(this.event_prefix){
                    eventmanager.dispatch(this.event_prefix + '_save_after', eventData);
                }
            },
            /*
             * Mass update stock items
             */
            massUpdate: function(items, deferred) {
                if(!deferred)
                    deferred = $.Deferred();
                var self = this;
                self._massUpdateBefore(items);
                this.getResource().massUpdate(items, deferred, this.removeFuncBeforeMassUpdate);
                /* after updated */
                deferred.always(function (response) {
                    self._massUpdateAfter(items, response);
                });
                return deferred;
            },
            _massUpdateBefore: function(items) {
                var eventData = {'model' : this, 'items': items};
                eventmanager.dispatch('model_massupdate_before', eventData);
                if(this.event_prefix){
                    eventmanager.dispatch(this.event_prefix + '_massupdate_before', eventData);
                }
            },
            _massUpdateAfter: function(items, response) {
                var eventData = {'model' : this, 'items': items, 'response': response};
                eventmanager.dispatch('model_massupdate_after', eventData);
                if(this.event_prefix){
                    eventmanager.dispatch(this.event_prefix + '_massupdate_after', eventData);
                }
            },
            /* Get data*/
            getData: function () {
                return this.data;
            },
            /* Set data*/
            setData: function (data) {
                this.data = data;
                return this;
            },
            /* Delete by id*/
            delete: function (id, deferred) {
                if(!deferred)
                    deferred = $.Deferred();
                var self = this;
                self._deleteBefore(id);
                this.getResource().delete(id, deferred);
                /* after deleted */
                deferred.always(function (response) {
                    self._deleteAfter(id, response);
                    self.cleanData();
                    self.destroy();
                });
                return deferred;
            },
            _deleteBefore: function(id) {
                var eventData = {'model' : this, 'id': id};
                eventmanager.dispatch('model_delete_before', eventData);
                if(this.event_prefix){
                    eventmanager.dispatch(this.event_prefix + '_delete_before', eventData);
                }
            },
            _deleteAfter: function(id, response) {
                var eventData = {'model' : this, 'id': id, 'response': response};
                eventmanager.dispatch('model_delete_after', eventData);
                if(this.event_prefix){
                    eventmanager.dispatch(this.event_prefix + '_delete_after', eventData);
                }
            },
            /* Clear*/
            clear: function () {
                var self = this;
                var deferred = self.getResourceOffline().clear(deferred);
                return deferred;
            },
            /* Update data*/
            pullData: function (deferredParent,pageSize,curPage,Filter) {
                var deferred = $.Deferred();
                var self = this;
                var dataCollection = this.resourceCollection.reset().setMode('online').setPageSize(pageSize).setCurPage(curPage);
                if(typeof Filter != 'undefined' && Filter.field && Filter.value && Filter.condition ){
                    dataCollection.addFieldToFilter(Filter.field,Filter.value,Filter.condition);
                }
                dataCollection.load(deferred);
                deferred.fail(function (error) {
                    deferredParent.reject(error);
                });
                deferred.done(function(data) {
                    if(data.items && data.items.length) {
                        self.getResourceOffline().massUpdate(data, deferredParent);
                        deferredParent.done(function(){
                            if(self.sync_id) {
                                eventmanager.dispatch(self.sync_id + '_pull_after', {'items': data.items});
                            }
                        });
                    }else {
                        deferredParent.resolve({
                            updated:0,
                            total:0
                        });
                    }
                    self.resourceCollection.setMode('offline');
                });
            },
            /* Push data*/
            pushData: function (deferredParent) {
                var deferred = $.Deferred();
                var self = this;
                var dataCollection = this.resourceCollection.setMode('offline').addFieldToFilter('need_push','1','eq');
                dataCollection.load(deferred);
                deferred.done(function(data) {
                    if(data.total_count) {
                        self.getResourceOnline().pushData(data, deferredParent);
                    }else {
                        deferredParent.resolve(true);
                    }
                });
            },

            prepareBeforeSave: function () {
                return this;
            }
        });
    }
);