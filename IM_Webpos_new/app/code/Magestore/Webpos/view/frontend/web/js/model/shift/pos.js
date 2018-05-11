/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/helper/shift',
        'Magestore_Webpos/js/model/pos/pos'
    ],
    function (ko, $, Helper, ShiftHelper, PosModel) {
        'use strict';

        var PosManagement = {
            currentPosId: ko.observable(parseInt(window.webposConfig.posId)),
            availablePos: ko.observableArray([]),
            EVENT_INIT_DATA_AFTER: 'webpos_pos_init_data_after',
            /**
             * Initialize
             * @returns {PosManagement}
             */
            initialize: function(){
                var self = this;
                self.initObserver();
                self.resetData();
                self.initData();
                return self;
            },
            /**
             * Init Observer
             * @returns {PosManagement}
             */
            initObserver: function(){
                var self = this;

                return self;
            },
            /**
             * Get resouce online
             * @returns {*}
             */
            getOnlineResource: function(){
                return PosModel().getResourceOnline().setPush(true).setLog(false);
            },
            /**
             * Get resouce collection
             * @returns {*}
             */
            getResourceCollection: function(){
                return PosModel().getCollection();
            },
            /**
             * Reset data
             * @returns {PosManagement}
             */
            resetData: function(){
                var self = this;
                //self.currentPosId('');
                self.availablePos([]);
                return self;
            },
            /**
             * Init Data
             * @param availablePos
             * @returns {PosManagement}
             */
            initData: function(availablePos){
                var self = this;
                var availablePos = (availablePos)?availablePos: Helper.getOnlineConfig('available_pos');
                if(availablePos && availablePos.length > 0){
                    $.each(availablePos, function(index, pos){
                        self.availablePos.push(pos);
                    });
                }
                Helper.dispatchEvent(self.EVENT_INIT_DATA_AFTER, '');
                return self;
            },
            /**
             * Refresh data
             * @param staffId
             * @param deferred
             * @returns {PosManagement}
             */
            refreshData: function(staffId, deferred){
                var self = this;
                var staffId = (staffId)?staffId:Helper.getCurrentStaffId();
                if(staffId){
                    deferred = (deferred)?deferred:$.Deferred();
                    var collection = self.getResourceCollection().setMode('online');
                    collection.addFieldToFilter('staff_id', staffId, 'eq');
                    collection.addFieldToFilter('location_id', window.webposConfig.locationId, 'eq');
                    collection.load(deferred);
                    deferred.done(function (response) {
                        self.resetData();
                        if(response.items.length > 0){
                           self.initData(response.items);
                        }
                    });
                }
                return self;
            },
            /**
             * Assign staff and pos
             * @param posId
             * @param staffId
             * @param deferred
             * @returns {PosManagement}
             */
            assign: function(posId, staffId, deferred){
                var self = this;
                var staffId = (staffId)?staffId:Helper.getCurrentStaffId();
                var posId = (posId)?posId:self.currentPosId();
                if(posId && staffId){
                    deferred = (deferred)?deferred:$.Deferred();
                    var params = {
                        pos_id: posId,
                        staff_id: staffId
                    };
                    self.getOnlineResource().assign(params,deferred);
                }
                return self;
            },
            /**
             * Close session
             * @param posId
             * @param deferred
             * @returns {PosManagement}
             */
            close: function(posId, deferred){
                var self = this;
                var posId = (posId)?posId:self.currentPosId();
                if(posId){
                    deferred = (deferred)?deferred:$.Deferred();
                    var params = {
                        pos_id: posId,
                        staff_id: '',
                        location_id: window.webposConfig.locationId,
                        current_session_id: '',
                    };
                    self.getOnlineResource().assign(params,deferred);
                }
                return self;
            },
            /**
             * Check session required config
             * @returns {boolean}
             */
            isSessionRequired: function(){
                var self = this;
                var isRequired = Helper.getOnlineConfig('is_session_required');
                return (!isRequired || ((isRequired) == 1) )?true:false;
            },
            /**
             * Validate open shift
             * @returns {boolean}
             */
            validate: function(){
                var self = this;
                var shiftId = window.webposConfig.shiftId
                if(self.isSessionRequired() && !shiftId){
                    return false;
                }
                Helper.dispatchEvent('validated_session', '');
                return true;
            },
            /**
             * Get current pos name
             * @returns {string}
             */
            getCurrentPosName: function(){
                var self = this;
                var posId = self.currentPosId();
                var availablePos = self.availablePos();
                var posName = '';
                if(posId && availablePos && availablePos.length > 0){
                    $.each(availablePos, function(index, pos){
                        if(posId == pos.pos_id){
                            posName = pos.pos_name
                        }
                    });
                }
                return posName;
            },
            /**
             * Get current pos denominations
             * @returns {string}
             */
            getCurrentPosDenominations: function(){
                var self = this;
                var posId = self.currentPosId();
                var availablePos = self.availablePos();
                var denominations = [];
                if(availablePos && availablePos.length > 0){
                    $.each(availablePos, function(index, pos){
                        if(!posId || (posId == pos.pos_id)){
                            denominations = pos.denominations;
                            return false;
                        }
                    });
                }
                return denominations;
            }
        };
        return PosManagement.initialize();
    }
);
