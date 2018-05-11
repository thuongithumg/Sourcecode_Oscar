/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
    
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/view/base/form/abstract'
    ],
    function ($, ko, OrderFactory, formAbstract) {
        "use strict";

        return formAbstract.extend({
            fieldsets: ko.observableArray([]),
            dataModel: ko.observable(''),

            initialize: function () {
                this._super();
            },
                
            _prepareForm: function(){
                var self = this;
                var deferred = $.Deferred();
                OrderFactory.get().load(1, deferred);
                deferred.done(function(response){
                    self.setFormData(response)
                    var fieldset = self.createFieldset('test_fieldset_id', {legend: 'DEMO LEGEND'});
                    fieldset.addField(
                        'test_field_name',
                        'text',
                        {
                            class: 'test_class_field',
                            name: 'base_currency_code',
                            label: 'Test Text Field 1',
                            title: 'Test Title 1',
                            isShowLabel: false
                        }
                    );
                    fieldset.addField(
                        'test_field_name_2',
                        'text',
                        {
                            class: 'test_class_field_2',
                            name: 'test_name_2',
                            label: 'Test Text Field 2',
                            title: 'Test Title 2',
                            value: 'Test Value 2'
                        }
                    );
                    self.addFieldset(fieldset);

                    var fieldset = self.createFieldset('test_fieldset_id_2', {legend: 'DEMO LEGEND 2'});
                    fieldset.addField(
                        'test_field_name_3',
                        'select',
                        {
                            class: 'test_class_field_3',
                            name: '',
                            label: 'Test Select Field 3',
                            title: 'Test Title 3',
                            options: {
                                value1: 'Test value 1',
                                value2: 'Test value 2',
                            },
                            value: 'value1'
                        }
                    );
                    fieldset.addField(
                        'test_field_name_4',
                        'select',
                        {
                            class: 'test_class_field_4',
                            name: 'test_name_4',
                            label: 'Test Label 4',
                            title: 'Test Title 4',
                            options: {
                                value1: 'Test value 3',
                                value2: 'Test value 4',
                            },
                            value: 'value2'
                        }
                    );
                    self.addFieldset(fieldset);

                    var fieldset = self.createFieldset('test_fieldset_id_2', {legend: 'DEMO LEGEND 3', columns: 2});
                    fieldset.addField(
                        'test_field_name_5',
                        'radio',
                        {
                            class: 'test_class_field_5',
                            name: 'test_name_5',
                            label: 'Test Radio Field 1',
                            title: 'Test Title 1',
                            options: {
                                value1: 'Test value 1',
                                value2: 'Test value 2',
                            },
                            value: 'value1'
                        }
                    );
                    fieldset.addField(
                        'test_field_name_6',
                        'radio',
                        {
                            class: 'test_class_field_6',
                            name: 'test_name_6',
                            label: 'Test Radio 2',
                            title: 'Test Title 2 ',
                            options: {
                                value1: 'Test value 3',
                                value2: 'Test value 4',
                            },
                            value: 'value2'
                        }
                    );
                    fieldset.addField(
                        'test_field_name_10',
                        'radio',
                        {
                            class: 'test_class_field_10',
                            name: 'test_name_6',
                            label: 'Test Radio 3',
                            title: 'Test Title 3',
                            options: {
                                value1: 'Test value 3',
                                value2: 'Test value 4',
                            },
                            value: 'value2'
                        }
                    );
                    self.addFieldset(fieldset);

                    var fieldset = self.createFieldset('test_fieldset_id_2', {legend: 'DEMO LEGEND 4', columns: 3});
                    fieldset.addField(
                        'test_field_name_7',
                        'checkbox',
                        {
                            class: 'test_class_field_7',
                            name: 'test_name_7',
                            label: 'Test Radio Field 7',
                            title: 'Test Title 7',
                            options: {
                                value1: 'Test value 1',
                                value2: 'Test value 2',
                            },
                            value: ['value1']
                        }
                    );
                    fieldset.addField(
                        'test_field_name_8',
                        'checkbox',
                        {
                            class: 'test_class_field_8',
                            name: 'test_name_8',
                            label: 'Test Radio 8',
                            title: 'Test Title 8',
                            options: {
                                value1: 'Test value 3',
                                value2: 'Test value 4',
                            },
                            value: ['value1', 'value2']
                        }
                    );
                    fieldset.addField(
                        'test_field_name_9',
                        'checkbox',
                        {
                            class: 'test_class_field_9',
                            name: 'test_name_9',
                            label: 'Test Radio 9',
                            title: 'Test Title 9',
                            options: {
                                value1: 'Test value 5',
                                value2: 'Test value 6',
                            },
                            value: ['value1', 'value2']
                        }
                    );
                    self.addFieldset(fieldset);
                });
            },
        });
    }
);
