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
    'require',
    'jquery',
    'ko',
    'model/catalog/product-factory',
    'ui/components/layout',
    'ui/components/base/grid/cell-grid',
    'eventManager',
    'model/config/local-config',
    'helper/alert',
    'model/appConfig',
    'model/checkout/cart',
    'helper/general',
    'model/url-builder',
    'mage/storage',
    'lib/cookie',
    'lib/jquery.mobile-1.4.5'
], function (require,
             $,
             ko,
             ProductFactory,
             viewManager,
             Component,
             eventManager,
             localConfig,
             Alert,
             AppConfig,
             CartModel,
             generalHelper,
             urlBuilder,
             storage,
             Cookies) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/catalog/product-list'
        },
        items: ko.observableArray([]),
        total: ko.observable(0),
        searchCat: '',
        breadcrumb: ko.observable(''),
        columns: ko.observableArray([]),
        isShowHeader: ko.observable(false),
        isSearchable: ko.observable(true),
        searchingKey: '',
        currentPage: ko.observable(1),
        syncPercent: ko.observable(100),
        displaySyncPercent: ko.observable(false),
        showNext: ko.observable(true),
        isShowNormalPrice: ko.observable(true),
        pageSize: 10,
        curPage: 1,
        reload: false,
        displayOutStock: true,
        initialize: function () {
            if (!this.model) {
                this.model = ProductFactory.get();
            }
            if (this.items().length > 0) {
                return this;
            }
            this.loadLocalConfig();
            this._super();
            var self = this;
            /* listen events */
            this.listenEvents();
            this.focusSearch();
        },
        _prepareCollection: function () {
            this.curPage = this.currentPage();
            var self = this;
            if (this.collection == null) {
                if (generalHelper.isOnlineCheckout()) {
                    this.collection = this.model.setMode('online').getCollection();
                    this.collection.addParamToFilter('customer_group_id', CartModel.customerGroup());
                } else {
                    this.collection = this.model.setMode('offline').getCollection();
                }
            }
            this.pageSize = 16;
            this.collection.setOrder('name', 'ASC');
            this.collection.setPageSize(this.pageSize);
            this.collection.setCurPage(this.curPage);


            if (this.searchKey) {
                if (generalHelper.isOnlineCheckout()) {
                    var filterAttributeString = window.webposConfig.search_string;
                    var filterAttributes = filterAttributeString.split(",");
                    var queryParams = [];
                    $.each(filterAttributes, function (index, value) {
                        var param = [value, "%" + self.searchKey + "%", 'like'];
                        queryParams.push(param);
                    });
                    this.collection.addFieldToFilter(queryParams);
                } else {

                    this.filterAttribute = 'search_string';
                    this.barcodeData = 'barcode_string';
                    this.collection.addFieldToFilter(
                        [
                            [this.barcodeData, "%" + this.searchKey + "%", 'like'],
                            [this.filterAttribute, "%" + this.searchKey + "%", 'like']
                        ]
                    );
                }

                if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    $("#search-header-product")[0].select();
                }
            }
            if (window.searchCat) {
                this.collection.addFieldToFilter('category_ids', "%'" + window.searchCat + "'%", 'like');
            }
        },

        setSyncPercent: function (percent) {
            var self = this;
            self.syncPercent(percent);
            if (percent < 100) {
                self.displaySyncPercent(true);
            } else {
                setTimeout(function () {
                    self.displaySyncPercent(false);
                }, 5000);
            }
        },

        filter: function (element, event) {
            if (event.target.value == '') {
                return;
            }
            var keyword = event.target.value;
            keyword = keyword.replace('#','');
            if (keyword && generalHelper.isOnlineCheckout()) {
                keyword = "fixbug*bugfix"+keyword;
            }
            this.searchKey = keyword;
            this.searchingKey = keyword;

            if ($('.category-name')) {
                $('.category-name').hide();
            }
            this.refresh = true;
            this.collection = null;
            this._prepareItems();
            if ($('#search-header-product').val() != '') {
                $('#remove-text-search-product').addClass('show');
            } else {
                $('#remove-text-search-product').removeClass('show');
            }
            //event.target.value = '';
            $(event.target).blur();
            $(event.target).select();
        },

        _prepareItems: function () {
            var deferred = $.Deferred();
            var self = this;
            if (this.refresh) {
                this.currentPage(1);
                this.curPage = 1;
            }
            this.getCollection().load(deferred);

            $('#product-list-overlay').show();
            var oldTimeStamp = Date.now();
            var barcodeResult = [];
            deferred.done(function (data) {
                var countRecord = data.items.length;
                if (countRecord < self.pageSize) {
                    self.showNext(false);
                } else {
                    self.showNext(true);
                }
                if (data.items == []) {
                    self.setItems([]);
                    self.total(0);
                } else {
                    for (var i in data.items) {
                        /* remove out-stock products from list items */
                        if (!self.displayOutStock && data.items[i].isShowOutStock) {
                            data.items.splice(i, 1);
                        }
                    }

                    /* update total count */
                    if (self.curPage == 1 && data.total_count <= 20) {
                        data.total_count = data.items.length;
                    }
                    $('#product-list-overlay').hide();
                    if ($.trim(self.searchingKey)) {

                        if (data.total_count == 1 && (!generalHelper.isOnlineCheckout())) {
                            ko.utils.arrayForEach(data.items, function (product) {
                                var searchingKey = self.searchingKey.toLowerCase();
                                var barcode_string = product.barcode_string.toLowerCase();
                                var search_string = product.search_string.toLowerCase();
                                if (barcode_string && barcode_string.indexOf("," + searchingKey + ",") >= 0) {
                                    if (product.type_id == "configurable" && product.barcode_options && product.barcode_options.length > 0) {
                                        ko.utils.arrayForEach(product.barcode_options, function (options) {
                                            if (options[searchingKey] || options[self.searchingKey]) {
                                                var optionData = (options[self.searchingKey]) ? options[self.searchingKey] : options[searchingKey];
                                                product.super_attribute = optionData.options;
                                                product.unit_price = optionData.product.price;
                                                product.child_id = optionData.product.product_id;
                                                product.options_label = optionData.label;
                                            }
                                        });
                                    }
                                    barcodeResult.push(product);
                                }
                            });
                        }

                        if (barcodeResult.length == 1 && (!generalHelper.isOnlineCheckout())) {
                            self.setItems(barcodeResult);
                            self.total(barcodeResult.length);
                        } else {
                            self.setItems(data.items);
                            self.total(data.total_count);
                        }


                        if (self.total() == 1 && self.searchingKey.toLowerCase() != "") {
                            var product = self.items()[0];
                            if (product.child_id && (!product.custom_options || (product.custom_options && product.custom_options.length <= 0))) {
                                viewManager.getSingleton('ui/components/catalog/product/detail-popup').addProduct(product);
                                self.searchingKey = "";
                                self.searchKey = "";
                            } else {
                                $("#block-product-list .product-item .product-img").click();
                            }
                        }
                    } else {
                        self.setItems(data.items);
                        self.total(data.total_count);
                    }
                }
                if (this.breadcrumb) {
                    self.breadcrumb(this.breadcrumb);
                }
            });
        },
        getAllCategories: function () {
            window.searchCat = '';
            this.searchKey = '';
            eventManager.dispatch('load_product_by_category', {"catagory": {}});
        },

        afterRender: function () {
            var self = this;
            var productListWrapper = $('#product-list-wrapper');
            productListWrapper.on('swiperight', function (event) {
                if ($('.icon-iconPOS-previous').is(':visible')) {
                    self.previousPage();
                }
            });
            productListWrapper.on('swipeleft', function (event) {
                if ($('.icon-iconPOS-next').is(':visible')) {
                    self.nextPage();
                }
            });

            /* default show category page*/
            // self.getAllCategories();
        },

        getClassSearch: function () {
            var className = 'remove-text';
            if (this.searchingKey) {
                return className + ' show';
            }
            return className;
        },
        removeSearch: function () {
            $("#search-header-product").val("");
            $("#remove-text-search-product").removeClass("show");
            this.searchKey = '';
            this.searchingKey = '';
            this.refresh = true;
            this.collection = null;
            this._prepareItems();
        },
        loadLocalConfig: function () {
            if (localConfig.get('catalog/outstock-display') === null) {
                this.displayOutStock = true;
            } else {
                this.displayOutStock = localConfig.get('catalog/outstock-display') == 1 ? true : false;
            }
        },
        listenEvents: function () {
            var self = this;
            var refreshCallback = function (event, eventData) {
                if (!!self.collection) {
                    self.refresh = true;
                    self.collection.queryParams.filterParams = [];
                    self.collection.queryParams.orderParams = [];
                    self.collection.queryParams.currentPage = '';
                    self.collection.queryParams.pageSize = '';
                    self.collection.queryParams.paramToFilter = [];
                    self.collection.queryParams.paramOrFilter = [];
                    /* default show category page*/
                    // window.searchCat = '';
                    self.searchingKey = '';
                    self.searchKey = '';
                    self._prepareItems();
                }
            };

            eventManager.observer('webpos_config_change_after', function (event, eventData) {
                var config = eventData.config;
                if (config.configPath === 'catalog/outstock-display') {
                    self.displayOutStock = config.value();
                    self.refresh = true;
                    self._prepareItems();
                }
            });

            /* after updated/ reloaded stock items */
            eventManager.observer('stock_item_finish_pull_after', function (event, eventData) {
                /* reload product list */
                refreshCallback(event, eventData);
            });

            /* after updated/ reloaded product */
            eventManager.observer('product_finish_pull_after', function (event, eventData) {
                /* reload product list */
                refreshCallback(event, eventData);
            });


            /* observer event to reload product list */
            eventManager.observer('start_new_order', function (event, eventData) {
                /* reload product list */
                refreshCallback(event, eventData);
            });

            var addCustomerGroupToFilterAndReload = function () {
                if (generalHelper.isOnlineCheckout() && CartModel.customerGroup() && !!self.collection) {
                    self.collection.addParamToFilter('customer_group_id', CartModel.customerGroup());
                    self._prepareItems();
                }
            };

            // eventManager.observer('checkout_select_customer_after', function (event, data){
            //     addCustomerGroupToFilterAndReload();
            // });

            eventManager.observer('after_edit_customer', function(){
                addCustomerGroupToFilterAndReload();
            });
        },

        getQtyInCart: function (product_id) {
            return CartModel.getQtyInCart(product_id);
        },

        afterRenderItems: function () {
            var height = 0;
            $('.wrap-list-product .product-item').each(function () {
                if ($(this).height() >= height) {
                    height = $(this).height();
                }
            });
            $('.wrap-list-product .product-item').each(function () {
                $(this).height(height);
            });
        },

        focusSearch: function () {
            var self = this;
            eventManager.observer('checkout_show_container_after', function (event, eventData) {
                if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    if ($("#search-header-product") && $("#search-header-product").length > 0)
                        $("#search-header-product")[0].focus();
                }
            });
            eventManager.observer('catalog_product_collection_load_after', function (event, eventData) {
                if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    if ($("#search-header-product") && $("#search-header-product").length > 0)
                        $("#search-header-product")[0].focus();
                }
            });
        },
        nextPage: function () {
            this.canLoad(true);
            this.startLoading();
            this.curPage++;
            this.currentPage(this.currentPage() + 1);
            this.refresh = false;
            this.resetData();
            if (generalHelper.isOnlineCheckout()) {
                this.collection.setMode('online');
            } else {
                this.collection.setMode('offline');
            }
            this._prepareItems();
        },

        previousPage: function () {
            if (this.curPage >= 1) {
                this.canLoad(true);
                this.startLoading();
                this.curPage--;
                this.currentPage(this.currentPage() - 1);
                this.refresh = false;
                this.resetData();
                if (generalHelper.isOnlineCheckout()) {
                    this.collection.setMode('online');
                } else {
                    this.collection.setMode('offline');
                }
                this._prepareItems();
            }

        },
        showCustomSale: function () {
            var customSalePopup = $('#popup-custom-sale');
            customSalePopup.addClass('fade-in');
            customSalePopup.addClass('show');
            var overlay = customSalePopup.parent().find(AppConfig.ELEMENT_SELECTOR.DYNAMIC_OVERLAY);
            if (overlay.length > 0) {
                overlay.addClass(AppConfig.CLASS.ACTIVE);
            } else {
                customSalePopup.parent().append("<div class='pos-overlay main active'></div>");
                overlay = customSalePopup.parent().find(AppConfig.ELEMENT_SELECTOR.DYNAMIC_OVERLAY_ACTIVE);
            }
            if (overlay.length > 0) {
                overlay.click(function () {
                    customSalePopup.removeClass('fade-in');
                    customSalePopup.removeClass('show');
                    overlay.removeClass(AppConfig.CLASS.ACTIVE);
                });
            }
        },

        showPrice: function (type, data) {
            if (type === 'final_price') {
                return generalHelper.convertAndFormatPrice(data.final_price);
            }

            if (type === 'price' && data.price !== 'undefined' && data.price !== null && data.final_price < data.price)
                return generalHelper.convertAndFormatPrice(data.price);
            return '';
        },

        showPopupDetails: function (item, event) {
            this.showPopupOptions(item, 'view_detail', event);
        },
        showPopupAddToCart: function (item, event) {
            this.showPopupOptions(item, 'add_to_cart', event);
        },

        showPopupOptions: function (item, style, event) {
            if (generalHelper.isOnlineCheckout() && (item.options == 1 || item.type_id == 'giftvoucher')) {
                var productModel = ProductFactory.get();

                var apiUrl = '/webpos/product/getoptions';
                var params = {};
                var payload = {};
                var deferred = $.Deferred();
                var serviceUrl = urlBuilder.createUrl(apiUrl, params);
                var sessionId = Cookies.get('WEBPOSSESSION');
                serviceUrl = serviceUrl + '?id=' + item.entity_id;
                serviceUrl += '&customer_group_id=' + CartModel.customerGroup() + "";
                serviceUrl += '&session=' + sessionId + "";

                $('#product-list-overlay-transparent').show();
                storage.get(
                    serviceUrl, JSON.stringify(payload)
                ).done(
                    function (response) {
                        deferred.resolve(response);
                    }
                ).fail(
                    function (response) {
                        deferred.reject(response);
                    }
                ).always(
                    function (response) {
                        $('#product-list-overlay-transparent').hide();
                    }
                );
                deferred.done(function (response) {
                    item.config_options = response.config_options;
                    item.json_config = response.json_config;
                    item.price_config = response.price_config;
                    item.custom_options = response.custom_options;
                    item.grouped_options = response.grouped_options;
                    item.bundle_options = response.bundle_options;
                    item.giftvoucher_options = response.giftvoucher_options;

                    var eventData = {apiData : response, optionData:item};
                    eventManager.dispatch('webpos_product_load_options_after', eventData);

                    var viewManager = require('ui/components/layout');
                    viewManager.getSingleton('ui/components/catalog/product/detail-popup').styleOfPopup(style);
                    viewManager.getSingleton('ui/components/catalog/product/detail-popup').itemData(item);
                    viewManager.getSingleton('ui/components/catalog/product/detail-popup').setAllData();
                    viewManager.getSingleton('ui/components/catalog/product/detail-popup').showPopup();
                    /* only use our popup */
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();

                });
            } else {
                var viewManager = require('ui/components/layout');
                viewManager.getSingleton('ui/components/catalog/product/detail-popup').styleOfPopup(style);
                viewManager.getSingleton('ui/components/catalog/product/detail-popup').itemData(item);
                viewManager.getSingleton('ui/components/catalog/product/detail-popup').setAllData();
                viewManager.getSingleton('ui/components/catalog/product/detail-popup').showPopup();
                /* only use our popup */
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            }

        },
        prepareAddToCart: function (product, event) {
            var self = this;
            if (product.options == 1) {
                var productModel = ProductFactory.get();
                productModel.setData(product);
                var isOutStock = !productModel.isSalable();
                if (isOutStock) {
                    Alert({
                        priority: 'warning',
                        title: self.__('Warning'),
                        message: self.__('This product is currently out of stock')
                    });
                    return false;
                }
                $("#search-header-product").val("");
                this.showPopupAddToCart(product);
            } else if (product.type_id == 'giftvoucher') {
                var Product = ProductFactory.get();
                Product.setData(product);
                this.showPopupAddToCart(product);
            }
            else if (product.storecredit_type == 3 || product.storecredit_type == 2 || product.storecredit_type == 1) {
                var Product = ProductFactory.get();
                Product.setData(product);
                this.showPopupAddToCart(product);
            } else {
                if (product.super_group && product.super_group.length > 0) {
                    ko.utils.arrayForEach(product.super_group, function (product) {
                        if (product.id) {
                            productModel.setData(product);
                            product.unit_price = productModel.getFinalPrice();
                            self.prepareAddToCart(product);
                        }
                    });
                } else {
                    var Product = ProductFactory.get();
                    Product.setData(product);
                    Product.resetTempAddData();
                    var infoBuyRequest = Product.getInfoBuyRequest(CartModel.customerGroup());
                    CartModel.addProduct(infoBuyRequest);
                    self.searchingKey = "";
                    self.searchKey = "";
                    $("#search-header-product").val("");
                    $("#search-header-product").blur();
                    $("#search-header-product").select();
                }
            }
            if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                if ($("#search-header-product") && $("#search-header-product").length > 0)
                    $("#search-header-product")[0].select();
            }

        },

        getAvailableQty: function (productData) {
            var qty = 0;
            qty = qty + parseFloat(productData.available_qty);

            if (productData.type_id == 'configurable'
                || productData.type_id == 'bundle'
                || productData.type_id == 'grouped') {

                return 'N/A';
            } else {
                return qty + ' ' + generalHelper.__('item(s)');
            }
        },

        isShowAvailableQty: function (productData) {
            if (generalHelper.isOnlineCheckout()) {
                return true;
            } else {
                return false;
            }

            // if(productData.type_id == 'configurable'
            //     || productData.type_id == 'bundle'
            //     || productData.type_id == 'grouped') {
            //
            //     return false;
            // }
            // return true;
        }
    });
});