/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/base/abstract',
        'Magestore_Webpos/js/view/base/form/element/fieldset'
    ],
    function ($, ko, viewAbstract, fieldset) {
        "use strict";
        return viewAbstract.extend({
            fieldsets: ko.observableArray([]),
            fieldsetView: fieldset,
            dataModel: ko.observable(''),

            defaults: {
                template: 'Magestore_Webpos/base/form/abstract',
            },

            isShowHeader: true,

            initialize: function () {
                this._render();
                this._super();
            },

            _render: function() {
                //this.setFormData();
                this._prepareForm();
            },

            createFieldset: function(id ,data){
                var fieldset = this.fieldsetView().create(id, data, this.dataModel());
                return fieldset;
            },

            addFieldset: function(element){
                var htmlRender = element.render();
                this.fieldsets.push(htmlRender);
            },

            setFormData: function(data){
                this.dataModel(data);
            },

            _prepareForm: function(){
                
            }
        });
    }
);
