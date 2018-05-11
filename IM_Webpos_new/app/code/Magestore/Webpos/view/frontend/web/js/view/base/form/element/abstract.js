/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/base/abstract',
    ],
    function ($, ko, viewAbstract) {
        "use strict";
        var webPosFormFieldData = {
            type: '',
            label: '',
            title: '',
            class: '',
            style: '',
            disabled:'',
            readonly:'',
            placeholder:'',
            checked:'',
            legend:'',
            options:'',
            value: '',
            isShowLabel: '',
        };
        
        return viewAbstract.extend({
            id: ko.observable(''),
            data: ko.observableArray([]),

            defaults: {
                template: 'Magestore_Webpos/base/form/element/abstract',
            },

            initialize: function () {
                this._super();
            },

            resetData: function() {
                this.id('');
                this.data([]);
                return this;
            },
            
            getId: function(){
                return this.id;
            }
        });
    }
);
