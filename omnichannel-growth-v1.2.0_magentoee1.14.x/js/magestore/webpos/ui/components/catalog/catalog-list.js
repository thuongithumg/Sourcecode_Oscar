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
define([
    'ko',
    'jquery',
    'ui/components/base/grid/cell-grid',
    'model/appConfig',
    'helper/general',
    'model/catalog/category',
    'eventManager',
    'lib/owl.carousel.min'
], function (ko, $, Component, AppConfig, generalHelper, categoryModel, eventManager) {
    'use strict';
    ko.bindingHandlers.sliderCategories = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            // This will be called when the binding is first applied to an element
            // Set up any initial state, event handlers, etc. here
            $('.catalog-header').click(function () {
                if ($('#all-categories').hasClass('in')) {
                    $('#checkout_container .main-content').css('height', 'auto');
                    $('#checkout_container .main-content .wrap-list-product').css('height', 'calc(100vh - 135px)');
                }
                else {
                    var height_sum = $('#checkout_container .o-header-nav').height() + 200;
                    $('#checkout_container .main-content').css('height', 'auto');
                    $('.ms-webpos .main-content .wrap-list-product').css('height', 'calc(100vh - ' + height_sum + 'px)');
                }
            });
            $('#all-categories').click(function () {
                var height_nav = $('#checkout_container .o-header-nav').height();
                var height_cat = $('#checkout_container #all-categories').height();
                var height_sum = height_nav + height_cat - 4;
                if ($('#all-categories').hasClass('no-cat')) {
                    height_sum = height_sum + 24;
                    $('#checkout_container .main-content').css('height', 'auto');
                    $('.ms-webpos .main-content .wrap-list-product').css('height', 'calc(100vh - 178px)');
                }
                else {
                    height_sum = height_sum + 120;
                    $('#checkout_container .main-content').css('height', 'auto');
                    $('.ms-webpos .main-content .wrap-list-product').css('height', 'calc(100vh - ' + height_sum + 'px)');
                }
            });

            $('.category-name .root-cat').click(function () {
                var height_nav = $('#checkout_container .o-header-nav').height();
                var height_cat = $('#checkout_container #all-categories').height();
                var height_sum = height_nav + height_cat;
                height_sum = height_sum + 24;
                $('#checkout_container .main-content').css('height', 'auto');
                $('.ms-webpos .main-content .wrap-list-product').css('height', 'calc(100vh - 178px)');
            });
        },
        update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            // This will be called once when the binding is first applied to an element,
            // and again whenever any observables/computeds that are accessed change
            // Update the DOM element based on the supplied values here.

            //viewModel.loadingCat(1);
            ko.unwrap(valueAccessor());
            var html = '';
            ko.utils.arrayForEach(valueAccessor().call(), function (item, key) {
                html = html + '<div class="item" id="' + key + '">'
                    + '<div class="category-item-view-product img-cat">'
                    + '<a href="#">'
                    + '<img src="' + item.image + '" alt="' + item.name + '"/>'
                    + '</a>'
                    + '</div>';
                if (item.children.length) {
                    html = html + '<div class="category-item-view-children collapsed"><h4 class="cat-name">' + item.name + '</h4>'
                        + '<span class="icon-iconPOS-dropdown"></span></div>';
                }else {
                    html = html + '<h4 class="cat-name none-child">' + item.name + '</h4>';
                }
                html = html + '</div>';
            });
            element.innerHTML = html;
            if (element.innerHTML) {
                $("#list-cat-header").owlCarousel({
                    items: 7,
                    itemsDesktop: [1000, 7],
                    itemsDesktopSmall: [900, 7],
                    itemsTablet: [600, 5],
                    itemsMobile: false,
                    navigation: true,
                    navigationText: ["", ""]
                });
            }
            var value = valueAccessor();
            $('.category-item-view-product').each(function (index) {
                $(this).on("click", function () {
                    viewModel.clickCatViewProduct(value()[$(this)[0].parentNode.id]);
                });
            });
            $('.category-item-view-children').each(function (index) {
                $(this).on("click", function () {
                    viewModel.clickCatViewChildren(value()[$(this)[0].parentNode.id]);
                });
            });
            //viewModel.loadingCat(0);
        }
    };
    return Component.extend({
        defaults: {
            template: 'ui/catalog/catalog-list'
        },
        model: categoryModel(),
        productList: null,
        items: ko.observableArray([]),
        className: ko.observable(''),
        loadingCat: ko.observable(''),
        parentId: '',
        columns: ko.observableArray([]),
        isShowHeader: false,
        isSearchable: true,
        pageSize: 10,
        curPage: 1,
        initialize: function () {
            this._super();
            var self = this;
        },

        _prepareCollection: function () {
            this.filterAttribute = 'sku';
            if(this.collection == null) {
                if (generalHelper.isOnlineCheckout()) {
                    this.collection = this.model.setMode('online').getCollection();
                } else {
                    this.collection = this.model.setMode('offline').getCollection();
                }
            }
            this.collection.setOrder('position', 'ASC');
            this.pageSize = 100;
            this.collection.setPageSize(this.pageSize);
            this.collection.setCurPage(this.curPage);
            if (!this.parentId) {
                this.collection.addFieldToFilter('level', '2', 'eq');  //TODO
            } else {
                this.collection.addFieldToFilter('parent_id', this.parentId, 'eq');
            }
        },

        _prepareItems: function () {
            var deferred = $.Deferred();
            var self = this;
            this.getCollection().load(deferred);
            this.startLoading();
            deferred.done(function (data) {
                self.finishLoading();
                self.items(data.items);
                if (data.total_count == 0) {
                    //self.className(' no-cat');
                    if ($('#all-categories') && $('#all-categories').length > 0) {
                        $('#all-categories').addClass('no-cat');
                        $('#all-categories').removeClass('in');
                    }
                    if ($('.catalog-header') && $('.catalog-header').length > 0) {
                        $('.catalog-header').addClass('no-cat');
                    }
                } else {
                    //self.className('');
                    if ($('#all-categories') && $('#all-categories').length > 0) {
                        $('#all-categories').removeClass('no-cat');
                    }
                    if ($('.catalog-header') && $('.catalog-header').length > 0) {
                        $('.catalog-header').removeClass('no-cat');
                    }
                }
            });
        },

        clickCat: function (data) {
            var viewManager = require('ui/components/layout');
            var self = this;
            self.refresh = true;
            self.parentId = parseInt(data.id);
            self.collection = null;
            self._prepareItems();
            if (data.path) {
                viewManager.getSingleton('ui/components/catalog/category/breadcrumbs').getBreadCrumb(data.path);
            } else {
                if ($('.category-name')) {
                    $('.category-name').hide();
                }
            }
            /* reload product - start */
            if (!self.productList) {
                self.productList = viewManager.getSingleton('ui/components/catalog/product-list');
            }

            self.productList.searchCat = parseInt(data.id);
            window.searchCat = parseInt(data.id);
            self.productList.refresh = true;
            self.productList.collection = null;
            self.productList._prepareItems();
            $("#search-header-product").val("");
            /* reload product - end*/
        },

        clickCatViewChildren: function (data) {
            var self = this;
            self.refresh = true;
            self.parentId = parseInt(data.id);
            self.collection = null;
            self._prepareItems();
        },
        clickCatViewProduct: function (data) {
            var viewManager = require('ui/components/layout');

            var self = this;
            if (data.path) {
                viewManager.getSingleton('ui/components/catalog/category/breadcrumbs').getBreadCrumb(data.path);
            } else {
                if ($('.category-name')) {
                    $('.category-name').hide();
                }
            }
            /* reload product - start */
            if (!self.productList) {
                self.productList = viewManager.getSingleton('ui/components/catalog/product-list');
            }
            self.productList.searchCat = parseInt(data.id);
            if (typeof data.id != 'undefined') {
                window.searchCat = data.id;
            } else {
                window.searchCat = '';
            }

            self.productList.currentPage(1);
            self.productList.refresh = true;
            self.productList.collection = null;
            self.productList._prepareItems();
            $("#search-header-product").val("");
            /* reload product - end*/
        }
    });
});