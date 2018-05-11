/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'Magestore_Webpos/js/model/request',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/helper/price',
        'mage/translate',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function (ko, $, Request, HelperDatetime, PriceHelper, __, Event) {
        'use strict';

        var PosManagement = {
            sessionId: ko.observable(),
            verified: ko.observable(false),
            cashCounted: ko.observable(false),
            closingBalance: ko.observable(0),
            data: ko.observable({}),
            sessions: ko.observableArray([]),
            denominations: ko.observableArray([]),
            getSessionsUrl: ko.observable(''),
            saveTransactionUrl: ko.observable(''),
            closeSessionUrl: ko.observable(''),
            printUrl: ko.observable(''),
            currentPosId: ko.observable(''),
            CLOSED_STATUS:1,
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
                self.isClosed = ko.pureComputed(function(){
                    var data = self.data();
                    return (data && (data.status != self.CLOSED_STATUS))?false:true;
                });
                self.realClosingBalance = ko.pureComputed(function(){
                    var data = self.data();
                    if(data && self.isClosed()){
                        return parseFloat(data.closed_amount);
                    }
                    return (self.cashCounted())?self.closingBalance():0;
                });
                self.differenceAmount = ko.pureComputed(function(){
                    var actualBalance = self.realClosingBalance();
                    var balance = self.theoretialClosingBalance();
                    actualBalance = (actualBalance)?actualBalance:0;
                    balance = (balance)?balance:0;
                    return (actualBalance - balance);
                });
                self.theoretialClosingBalance = ko.pureComputed(function(){
                    var data = self.data();
                    if(self.isClosed()){
                        var addedAmount = parseFloat(data.cash_added);
                        var removedAmount = parseFloat(data.cash_removed);
                        var closedAmount = parseFloat(data.closed_amount);
                        return (addedAmount - removedAmount + closedAmount);
                    }else{
                        return (data)?parseFloat(data.balance):0;
                    }
                });
                self.profitLossReason = ko.pureComputed(function () {
                    var data = self.data();
                    return data.profit_loss_reason;
                });
                self.sessions.subscribe(function(sessions){
                    ko.utils.arrayForEach(sessions, function(session) {
                        var from = HelperDatetime.toCurrentTime(session.opened_at);
                        var to = HelperDatetime.toCurrentTime(session.closed_at);
                        if(session.status == self.CLOSED_STATUS){
                            session.session_label =  __('Staff')+ ': ' + session.staff_name;
                            session.from_label =  __('From')+ ': ' + HelperDatetime.getFullDatetime(from);
                            session.to_label =  __('To')+ ': ' + HelperDatetime.getFullDatetime(to);
                        }else{
                            session.session_label = session.staff_name + ' - ' + __('From') + ': ' + HelperDatetime.getFullDatetime(from);
                        }
                    });
                    var selectedSessionId = false;
                    var data = self.data();
                    if(data && data.entity_id){
                        selectedSessionId = data.entity_id;
                    }
                    if(sessions && (sessions.length > 0)){
                        var session = ko.utils.arrayFirst(sessions, function(session) {
                            return (selectedSessionId)?(session.entity_id == selectedSessionId):true;
                        });
                        if(session){
                            selectedSessionId = session.entity_id;
                        }
                    }
                    self.sessionId(selectedSessionId);
                    self.refreshSelectedSessionData();
                });
                self.sessionId.subscribe(function(sessionId){
                    self.refreshSelectedSessionData();
                });
                self.data.subscribe(function(session){
                    self.printUrl('');
                    if(session && session.print_url){
                        self.printUrl(session.print_url);
                    }
                });
                return self;
            },
            /**
             * Reset data
             * @returns {PosManagement}
             */
            resetData: function(){
                var self = this;
                self.data({});
                self.sessionId('');
                self.verified(false);
                self.cashCounted(false);
                self.closingBalance(0);
                return self;
            },
            /**
             * Init Data
             * @param data
             * @param isJson
             */
            initData: function(data, isJson){
                var self = this;
                if(data){
                    data = (isJson == true)?data:JSON.parse(data);
                    if(data.sessions){
                        self.sessions(data.sessions);
                    }
                    if(data.denominations){
                        self.denominations(data.denominations);
                    }
                    if(data.get_sessions_url){
                        self.getSessionsUrl(data.get_sessions_url);
                    }
                    if(data.save_transaction_url){
                        self.saveTransactionUrl(data.save_transaction_url);
                    }
                    if(data.close_session_url){
                        self.closeSessionUrl(data.close_session_url);
                    }
                    if(data.current_pos_id){
                        self.currentPosId(data.current_pos_id);
                    }
                    Event.dispatch(self.EVENT_INIT_DATA_AFTER, '');
                }
            },
            /**
             * Refresh data
             */
            refreshData: function () {
                var self = this;
                var params = {
                    'pos_id':self.currentPosId()
                };
                Request.send(self.getSessionsUrl(), 'post', params).done(function(response){
                    if (response) {
                        self.initData(response, true);
                        self.verified(false);
                        self.cashCounted(false);
                        self.closingBalance(0);
                    }
                });
            },
            /**
             * Get url to print z-report
             * @returns {*}
             */
            getPrintUrl: function(){
                var self = this;
                var url = self.printUrl();
                var params = {
                    difference: self.differenceAmount(),
                    theoretical_closing_balance: self.theoretialClosingBalance(),
                    profitLossReason: self.profitLossReason(),
                    real_closing_balance:self.realClosingBalance(),
                    closed_at:HelperDatetime.getBaseSqlDatetime()
                };
                url = Request.addParamsToUrl(url, params);
                return url;
            },
            /**
             * Refresh selected data
             */
            refreshSelectedSessionData: function(){
                var self = this;
                var sessionId = self.sessionId();
                var sessions = self.sessions()
                self.data({});
                if(sessions && (sessions.length > 0)){
                    var session = ko.utils.arrayFirst(sessions, function(session) {
                        return (sessionId)?(session.entity_id == sessionId):true;
                    });
                    if(session){
                        self.data(session);
                    }
                }
            },
            /**
             * Close session
             * @param deferred
             */
            closeCurrentSession: function(deferred){
                var self = this;
                var params = {
                    'pos_id': self.currentPosId(),
                    'session_id': self.sessionId(),
                    'real_closing_balance': self.realClosingBalance(),
                    'profit_loss_reason': self.profitLossReason(),
                    'base_real_closing_balance': PriceHelper.toBasePrice(self.realClosingBalance()),
                    'closed_at': HelperDatetime.getBaseSqlDatetime()
                };
                Request.send(self.closeSessionUrl(), 'post', params, deferred).done(function(response){
                    if (response) {
                        self.resetData();
                        self.initData(response, true);
                    }
                });
            }
        };
        return PosManagement.initialize();
    }
);
