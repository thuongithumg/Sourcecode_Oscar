/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'Magestore_Webpos/js/model/catalog/product',
    'Magestore_Webpos/js/model/checkout/cart/customsale',
]);

define(
    ['require',
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/model/checkout/cart/customsale-factory',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/view/base/grid/cell-grid',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/config/local-config',
        'Magestore_Webpos/js/helper/alert',
        'mage/translate',
        'Magestore_Webpos/js/helper/full-screen-loader',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/lib/jquery.mobile-1.4.5',

    ],
    function (require, $, ko, ProductFactory, CustomsaleFactory, ViewManager, cellGrid, eventManager, localConfig, Alert, $t, loader, CartModel, generalHelper) {
        "use strict";
        return cellGrid.extend({
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
            pageSize: 10,
            curPage: 1,
            reload: false,
            displayOutStock: true,
            defaults: {
                template: 'Magestore_Webpos/catalog/product-list',
            },
            initialize: function () {
                if (!this.model) {
                    this.model = ProductFactory.get();
                }
                if (this.items().length > 0) {
                    return this;
                }
                this.loadLocalConfig();
                this._super();
                /* listen events */
                this.listenEvents();
                this.focusSearch();
                generalHelper.saveLocalConfig('firstTimeReload', true);
            },
            _prepareCollection: function () {
                var self = this;
                this.curPage = this.currentPage();


                this.filterAttribute = 'search_string';
                this.barcodeData = 'barcode_string';
                if (this.collection == null) {
                    this.collection = this.model.getCollection();
                }
                var mode = (generalHelper.isUseOnline('products')) ? 'online' : 'offline';
                if(generalHelper.getLocalConfig('firstTimeReload')) {
                    mode = 'online';
                }
                this.collection.setMode(mode);
                this.pageSize = 16;
                this.collection.setPageSize(this.pageSize);
                this.collection.setCurPage(this.curPage);
                this.collection.setOrder('name', 'ASC');

                if (this.searchKey) {
                    window.searchCat = '';
                    if (mode == 'online') {

                        /** allow search with '#' character  */
                        self.searchKey = self.searchKey.replace("#","/@$%@$%/");

                        var filterAttributeString = generalHelper.getBrowserConfig('webpos/product_search/product_attribute');
                        var filterAttributes = filterAttributeString.split(",");
                        var queryParams = [];
                        $.each(filterAttributes, function (index, value) {
                            value = (value == 'category_ids') ? 'category_id' : value;
                            var param = [value, "%" + self.searchKey + "%", 'like'];
                            queryParams.push(param);
                        });
                        this.collection.addFieldToFilter(queryParams);
                    } else {
                        this.collection.addFieldToFilter(
                            [
                                [this.barcodeData, "%," + this.searchKey + ",%", 'like'],
                                [this.filterAttribute, "%" + this.searchKey + "%", 'like']
                            ]
                        );
                    }
                    if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        $("#search-header-product")[0].select();
                    }
                }
                if (window.searchCat) {
                    if (mode == 'online') {
                        this.collection.addFieldToFilter('category_id', window.searchCat, '');
                    } else {
                        this.collection.addFieldToFilter('category_ids', "%'" + window.searchCat + "'%", 'like');
                    }
                }
            },

            resizeProductItem: function () {
                var allProductDiv = $('#block-product-list').find('.product-item');
                var maxHeight = 0;
                $.each(allProductDiv, function (index, value) {
                    if ($(value).height() >= maxHeight) {
                        maxHeight = $(value).height();
                    }
                });
                allProductDiv.height(maxHeight);
            },

            _prepareItems: function () {
                if (!ViewManager)
                    ViewManager = require('Magestore_Webpos/js/view/layout');
                var deferred = $.Deferred();
                var self = this;
                if (this.refresh) {
                    this.currentPage(1);
                    this.curPage = 1;
                }
                this.getCollection().load(deferred);
                $('#product-list-overlay').show();
                var barcodeResult = [];
                deferred.done(function (data) {
                    if (data.items.length < self.pageSize) {
                        self.showNext(false);
                    } else {
                        self.showNext(true);
                    }
                    if (data.items == []) {
                        self.setItems([]);
                        self.total(0);
                    } else {
                        var numberOutOfStock = 0;
                        for (var i in data.items) {
                            /* remove out-stock products from list items */
                            if (generalHelper.isStockOnline() && !data.items[i].is_salable) {
                                data.items[i].isShowOutStock = true;
                            }
                            if (!self.displayOutStock && (data.items[i].isShowOutStock)) {
                                data.items.splice(i, 1);
                                numberOutOfStock++;
                            }
                        }
                        data.total_count = parseFloat(data.total_count) - numberOutOfStock;

                        $('#product-list-overlay').hide();
                        if ($.trim(self.searchingKey)) {
                            if (data.total_count == 1) {
                                ko.utils.arrayForEach(data.items, function (product) {
                                    var searchingKey = self.searchingKey.toLowerCase();
                                    var barcode_string = product.barcode_string.toLowerCase();
                                    var search_string = product.search_string.toLowerCase();
                                    if (barcode_string && barcode_string.indexOf("," + searchingKey + ",") >= 0) {
                                        if (product.type_id == "configurable" && product.barcode_options && product.barcode_options.length > 0) {
                                            ko.utils.arrayForEach(product.barcode_options, function (options, index) {
                                                var barcodes = $.map(Object.keys(options), function (n, i) {
                                                    return n.toLowerCase();
                                                });
                                                if (options[searchingKey] || options[self.searchingKey] || $.inArray(searchingKey, barcodes) > -1) {
                                                    var originalBarcode = Object.keys(options)[$.inArray(searchingKey, barcodes)];
                                                    var optionData = ($.inArray(searchingKey, barcodes) > -1) ? (options[originalBarcode]) : (options[self.searchingKey]) ? options[self.searchingKey] : options[searchingKey];
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
                            if (barcodeResult.length == 1) {
                                self.setItems(barcodeResult);
                                self.total(barcodeResult.length);
                            } else {
                                self.setItems(data.items);
                                // console.log(data.total_count);
                                self.total(data.total_count);
                            }
                            if (self.total() == 1 && self.searchingKey.toLowerCase() != "") {
                                var product = self.items()[0];
                                if (product.child_id && (!product.custom_options || (product.custom_options && product.custom_options.length <= 0))) {
                                    ViewManager.getSingleton('view/catalog/product/detail-popup').addProduct(product);
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
                    generalHelper.saveLocalConfig('firstTimeReload', false);
                });
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
                this.searchKey = event.target.value;
                this.searchingKey = event.target.value;

                window.searchCat = '';
                if ($('.category-name')) {
                    $('.category-name').hide();
                }
                this.refresh = true;
                this.collection = null;
                this._prepareItems();
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
                            title: $t('Warning'),
                            message: $t('This product is currently out of stock')
                        });
                        return false;
                    }
                    $("#search-header-product").val("");
                    this.showPopupAddToCart(product);
                } else if (product.storecredit_type == 3 || product.storecredit_type == 2 || product.storecredit_type == 1) {
                    var Product = ProductFactory.get();
                    Product.setData(product);
                    this.showPopupAddToCart(product);
                } else if (product.type_id === 'giftvoucher') {
                    this.showPopupDetails(product);
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
                        var newProduct = jQuery.extend({}, product);
                        Product.setData(newProduct);
                        Product.resetTempAddData();
                        var infoBuyRequest = Product.getInfoBuyRequest(CartModel.customerGroup());
                        CartModel.addProduct(infoBuyRequest);
                        self.searchingKey = "";
                        self.searchKey = "";
                        $("#search-header-product").val("");
                    }
                }
                if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    if ($("#search-header-product") && $("#search-header-product").length > 0)
                        $("#search-header-product")[0].select();
                }

            },
            showPopupDetails: function (item, event) {
                if (!ViewManager)
                    ViewManager = require('Magestore_Webpos/js/view/layout');
                ViewManager.getSingleton('view/catalog/product/detail-popup');
                ViewManager.getSingleton('view/catalog/product/detail-popup').styleOfPopup('view_detail');
                ViewManager.getSingleton('view/catalog/product/detail-popup').itemData(item);
                ViewManager.getSingleton('view/catalog/product/detail-popup').setAllData();
                ViewManager.getSingleton('view/catalog/product/detail-popup').showPopup();
                /* only use our popup */
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },
            showPopupAddToCart: function (item, event) {
                if (!ViewManager)
                    ViewManager = require('Magestore_Webpos/js/view/layout');
                ViewManager.getSingleton('view/catalog/product/detail-popup');
                ViewManager.getSingleton('view/catalog/product/detail-popup').styleOfPopup('add_to_cart');
                ViewManager.getSingleton('view/catalog/product/detail-popup').itemData(item);
                ViewManager.getSingleton('view/catalog/product/detail-popup').setAllData();
                ViewManager.getSingleton('view/catalog/product/detail-popup').showPopup();
                /* only use our popup */
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },
            initCustomSalePopup: function () {
                var self = this;
                if ($('.pos_modal_link').length > 0) {
                    $('.pos_modal_link').click(function () {
                        if ($(this).data("target")) {
                            var target = $(this).data("target");
                            if ($(target).length > 0) {
                                $(target).removeClass("fade");
                                $(target).addClass("show");
                                $(target).addClass("fade-in");
                                $(target).show();
                                var CustomSale = CustomsaleFactory.get();
                                CustomSale.initTaxClasses();
                                $('.wrap-backover').show();
                                $('.notification-bell').hide();
                                $('#c-button--push-left').hide();
                                if ($(target + ' .pos_overlay').length > 0) {
                                    $(target + ' .pos_overlay').click(function () {
                                        $(target).addClass("fade");
                                        $(target).removeClass("show");
                                        $(target).removeClass("fade-in");
                                        $(target).hide();
                                        $('.wrap-backover').hide();
                                        $('.notification-bell').show();
                                    });
                                }
                                if ($(target + ' button').length > 0) {
                                    $(target + ' button').each(function () {
                                        if ($(this).data("dismiss") && $(this).data("dismiss") == "modal") {
                                            $(this).click(function () {
                                                $(target).addClass("fade");
                                                $(target).removeClass("show");
                                                $(target).removeClass("fade-in");
                                                $(target).hide();
                                                $('.wrap-backover').hide();
                                                $('.notification-bell').show();
                                            });
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            },

            addItems: function (items) {
                for (var i in items) {
                    items[i].columns = this.columns;
                    items[i].columns = this.columns;
                    if (typeof items[i].isShowOutStock == 'undefined') {
                        items[i].isShowOutStock = false;
                    }
                    if (!this.displayOutStock && items[i].isShowOutStock) {
                        /* do not display out-stock product */
                        return;
                    }
                }
                ko.utils.arrayPushAll(this.items, items);
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
                    self.refresh = true;
                    self.getCollection().reset();
                    self._prepareCollection();
                    self._prepareItems();
                });
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
                this.searchKey = '';
                this.searchingKey = '';
                this.refresh = true;
                this.collection = null;
                this._prepareItems();
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
                        if ($("#search-header-product") && $("#search-header-product").length > 0) {
                            // $("#search-header-product")[0].focus();
                        }
                    }
                });
            },
            getQtyInCart: function (product_id) {
                return CartModel.getQtyInCart(product_id);
            },

            nextPage: function () {
                this.canLoad(true);
                this.startLoading();
                this.curPage++;
                this.currentPage(this.currentPage() + 1);
                this.refresh = false;
                this.resetData();
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
                    this._prepareItems();
                }

            },
            startLoading: function () {
                $('#product-list-overlay').show();
                this.isLoading = true;
            },
            finishLoading: function () {
                setTimeout(function () {
                    $('#product-list-overlay').hide();
                }, 800);
                this.isLoading = false;
            },
            showPrice: function (type, data) {
                if (type === 'final_price')
                    return generalHelper.convertAndFormatPrice(data.final_price);
                if (type === 'price' && data.price !== 'undefined' && data.price !== null && data.final_price < data.price)
                    return generalHelper.convertAndFormatPrice(data.price);
                return '';
            },
            getAvailableQty: function (stocks, productData) {
                var qty = 0;
                ko.utils.arrayForEach(stocks, function (stock) {
                    qty = qty + stock.qty;
                });
                if (generalHelper.isStockOnline()) {
                    qty = (productData.qty) ? productData.qty : 0;
                }
                if (productData.type_id == 'configurable'
                    || productData.type_id == 'bundle'
                    || productData.type_id == 'grouped') {

                    return qty + ' ' + $t('child item(s)');
                } else {
                    return qty + ' ' + $t('item(s)');
                }
            },
            isShowAvailableQty: function (productData) {
                var mode = (generalHelper.isUseOnline('products')) ? 'online' : 'offline';
                if (productData.type_id == 'configurable'
                    || productData.type_id == 'bundle'
                    || productData.type_id == 'grouped') {
                    if (mode == 'online') {
                        return false;
                    }
                }
                return true;
            }

        });
    }
);