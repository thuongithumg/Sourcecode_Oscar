/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko'
    ],
    function (ko) {
        "use strict";
        return {
            DATA: {
                CC_OWNER: 'cc_owner',
                CC_TYPE: 'cc_type',
                CC_NUMBER: 'cc_number',
                CC_EXP_MONTH: 'cc_exp_month',
                CC_EXP_YEAR: 'cc_exp_year',
                CC_CID: 'cc_cid'
            },
            info: {
                cc_owner: ko.observable(''),
                cc_type: ko.observable(''),
                cc_number: ko.observable(''),
                cc_exp_month: ko.observable(0),
                cc_exp_year: ko.observable(0),
                cc_cid: ko.observable('')
            },
            getData: function(){
                var self = this;
                return {
                    cc_owner: self.info.cc_owner(),
                    cc_type: self.info.cc_type(),
                    cc_number: self.info.cc_number(),
                    cc_exp_month: self.info.cc_exp_month(),
                    cc_exp_year: self.info.cc_exp_year(),
                    cc_cid: self.info.cc_cid()
                }
            },
            setData: function(key, value){
                var self = this;
                if(self.info[key]){
                    self.info[key](value);
                }
            },
            resetData: function(){
                this.info.cc_owner('');
                this.info.cc_type('');
                this.info.cc_number('');
                this.info.cc_exp_month(0);
                this.info.cc_exp_year(0);
                this.info.cc_cid('');
            }
        };
    }
);