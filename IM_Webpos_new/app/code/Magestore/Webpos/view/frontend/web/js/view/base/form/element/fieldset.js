/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'underscore',
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/base/abstract',
        'Magestore_Webpos/js/view/base/form/element/text',
        'Magestore_Webpos/js/view/base/form/element/select',
        'Magestore_Webpos/js/view/base/form/element/radio',
        'Magestore_Webpos/js/view/base/form/element/checkbox',
    ],
    function (_, $, ko, viewAbstract, textElement, selectElement, radioElement, checkboxElement) {
        "use strict";

        var webPosFormFieldsetData = {
            title: '',
            class: '',
            style: '',
            legend:'',
            columns: 1,
        };

        return viewAbstract.extend({
            id: ko.observable(''),
            data: ko.observableArray([]),
            fields: ko.observableArray([]),
            rows: ko.observableArray([]),
            dataModel: {},

            mappingField: {
                'text': textElement(),
                'select': selectElement(),
                'radio': radioElement(),
                'checkbox': checkboxElement(),
            },

            defaults: {
                template: 'Magestore_Webpos/base/form/element/fieldset',
            },

            initialize: function () {
                this._super();
            },

            create: function(id, data, dataModel){
                this.dataModel = dataModel;
                if(!data.columns)
                    data.columns = 1;
                this.id(id);
                this.fields([]);
                this.rows([]);
                data = this.renderClassFromColumns(data);
                this.data(data);
                return this;
            },

            addField: function(id, type, data){
                data = this._prepareFieldData(id, type, data);
                this.fields.push(data);
                if(this.fields.call().length==this.data.call().columns) {
                    this.rows.push({fields: this.fields(), data: this.data()});
                    this.fields([]);
                }
            },

            _prepareFieldData: function(id, type, data){
                if(this.dataModel[data.name] && !data.value)
                    data.value = this.dataModel[data.name];
                var ele = this.getMappingField(type, data);
                ele.id(id);
                ele.data(data);
                data.htmlRender = ele.render();
                return data;
            },

            getMappingField: function(type, data){
                if(data['renderer']){
                    if(typeof data['renderer'] == 'object')
                        return data['renderer'];
                    if(typeof data['renderer'] == 'function')
                        return data['renderer']();
                }
                return this.mappingField[type];
            },

            renderClassFromColumns: function(data){
                if(!data.class)
                    data.class = '';
                data.class += ' row';
                var colClass = 12/data.columns;
                data.class += ' col-xs-'+colClass;
                return data;
            },

            render: function(){
                if(this.fields.call().length>0)
                    this.rows.push({fields: this.fields(), data: this.data()});
                return {id: this.id.call(), data: this.data(), rows: this.rows()};
            },
        });
    }
);
