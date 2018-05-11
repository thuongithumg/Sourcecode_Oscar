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
        'posComponent',
        'eventManager',
        'model/resource-model/magento-rest/catalog/category',
        'helper/general'
    ],
    function ($, ko, Component, eventManager, categoryRest, generalHelper) {
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
                template: 'ui/catalog/category/breadcrumbs',
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
                var breadcrums = [];
                if (generalHelper.isOnlineCheckout()) {
                    path = allPaths.join('_');
                    var breadcrumbUrl = 'webpos/catalog/breadcrumbs?path=' + path;
                    //var collectionTest

                    categoryRest().callRestApi(
                        breadcrumbUrl,
                        'get',
                        {},
                        {},
                        deferred
                    );
                } else {
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
                }

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