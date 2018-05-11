/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, ko, Component, eventManager) {
        "use strict";
        ko.bindingHandlers.afterRenderGrid = {
            update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                //alert($('.grid-row').length);
            }
        };
        return Component.extend({
            breadcrumbs: ko.observableArray([]),
            numberBreadcrumbs: ko.observable(0),
            defaults: {
                template: 'Magestore_Webpos/catalog/category/breadcrumbs',
            },
            initialize: function () {
                this._super();
            },
            getBreadCrumb: function (path) {
                var self = this;
                self.breadcrumbs([]);
                self.numberBreadcrumbs(0);
                if (path) {
                    this.setBreadCrumb(path).done(function (data) {
                        self.breadcrumbs(data);
                        self.numberBreadcrumbs(data.length);
                    });
                }
            },
            setBreadCrumb: function (path, deferred) {
                var allPaths = path.split('/');
                if (!deferred) {
                    deferred = $.Deferred();
                }
                //var collectionTest
                var breadcrums = [];
                server.category.query()
                    .all()
                    .execute()
                    .then(function (results) {
                        var check = 0;
                        for (var i = 0; i <= allPaths.length - 1; i++) {
                            $.each(results, function (index, value) {
                                if (value.id == allPaths[i]) {
                                    if (check != 0 || value.first_category == 1) {
                                        breadcrums.push(value);
                                        check++;
                                    }
                                }
                                if (check) {
                                    return;
                                }
                            });
                        }
                        deferred.resolve(breadcrums);
                    });
                return deferred;
            },
            getAllCategory: function () {
                eventManager.dispatch('load_product_by_category', {"catagory":{},'open_category':true});
            },
            clickCat: function (data) {
                eventManager.dispatch('load_product_by_category', {"catagory": data,'open_category':true});
            }
        });
    }
);