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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

var PurchaseorderSuccessJS = Class.create();
PurchaseorderSuccessJS.prototype = {
    initialize: function (config) {
        this.config = config;
        this.selectedItems = config.selectedItems ? Object.assign({}, config.selectedItems) : {};
        this.selectItemObject = $H(this.selectedItems);
        if (config.gridJsObjectName) {
            this.gridJsObject = window[config.gridJsObjectName];
            this.hiddenInputField = config.hiddenInputField;
            this.editFields = config.editFields;
            $(this.hiddenInputField).value = Object.toJSON(this.selectedItems);
        }
        if (config.parentGridJsObjectName)
            this.parentGridJsObject = window[config.parentGridJsObjectName];
        this.saveUrl = config.saveUrl;
        this.deleteUrl = config.deleteUrl;
        this.canReloadTotal = config.canReloadTotal;
        // if (config.priceListJson)
        //     this.priceListJson = config.priceListJson;
        // else if (config.priceListJson)
        //     this.priceListJson = config.priceListJson;
        this.deleteUrl = config.deleteUrl;
        if (config.tabContainer) {
            this.tabContainer = window[config.tabContainer];
            this.tabsReload = config.tabsReload;
        }
        this.messages = config.messages,
            this.tabIndex = 1000;

        if (config.gridJsObjectName) {
            this.gridJsObject.initRowCallback = this.initRow.bind(this);
            this.gridJsObject.rowClickCallback = this.rowClick.bind(this);
            this.gridJsObject.checkboxCheckCallback = this.checkCheckbox.bind(this);

            if (this.gridJsObject.rows) {
                this.gridJsObject.rows.each(function (row) {
                    this.initRow(this.gridJsObject, row);
                }.bind(this));
            }
        }

        if (config.reloadBtn) {
            this.reloadBtn = config.reloadBtn;
            if (Array.isArray(config.reloadBtn)) {
                config.reloadBtn.each(function (el) {
                    if ($(el))
                        Event.observe(el, 'click', function () {
                            this.gridJsObject.reload();
                        }.bind(this));
                }.bind(this))
            } else {
                Event.observe(config.reloadBtn, 'click', function () {
                    this.gridJsObject.reload();
                }.bind(this));
            }
        }

        if (config.saveBtn)
            Event.observe($(config.saveBtn), 'click', function (event) {
                this.save(event, null);
            }.bind(this));

        if (config.updateBtn)
            Event.observe($(config.updateBtn), 'click', function (event) {
                this.update(event);
            }.bind(this));
    },

    /**
     * Initialize grid row
     *
     * @param {Object} grid
     * @param {String} row
     */
    initRow: function (grid, row) {
        var checkbox = $(row).down('input[type=checkbox]'),
            deleteButton = $(row).down('a[class=delete_item]');
        if (checkbox) {
            this.editFields.each(function (el) {
                var element = $(row).down('input[name=' + el + ']');
                if (element) {
                    checkbox[el] = element;
                    checkbox[el + '_old'] = $(row).down('input[name=' + el + '_old]');
                    element.disabled = !checkbox.checked;
                    element.tabIndex = this.tabIndex++;
                    element['checkbox'] = checkbox;
                    Event.observe(element, 'keyup', this.textBoxChange.bind(this));
                }
            }.bind(this));
            var values = this.selectItemObject.get(checkbox.value);
            if (values) {
                this.editFields.each(function (el) {
                    checkbox[el].value = values[el]
                });
                this.gridJsObject.setCheckboxChecked(checkbox, true);
            }
            if (checkbox.checked)
                this.gridJsObject.setCheckboxChecked(checkbox, true);
        }
        if (deleteButton) {
            Event.observe(deleteButton, 'click', this.deleteItem.bind(this));
        }
    },

    /**
     * Process event change text box
     *
     * @param {String} event
     */
    textBoxChange: function (event) {
        var element = Event.element(event);
        if (element && element['checkbox'] && element['checkbox'].checked) {
            if (isNaN(element.value) || element.value < 0) {
                element.value = '';
                element.select();
            }
            var value = this.selectItemObject.get(element['checkbox'].value);
            value = value ? value : {};
            value[element.name] = element.value;
            if (element.name == 'qty_orderred' || element.name == 'bill_qty')
            {
                var checkbox = element['checkbox'];
                var productId = checkbox.value;
                var costField = checkbox['cost'];
                if(!costField)
                    costField = checkbox['unit_price'];
                var minCost = 0;
                if(window.priceListJson) {
                    for(var index in priceListJson){
                        if ((priceListJson[index].product_id == productId)
                            && parseFloat(priceListJson[index].minimal_qty) <= parseFloat(element.value)
                            && parseFloat(priceListJson[index].cost) > 0) {
                            if (minCost == 0) {
                                minCost = priceListJson[index].cost;
                            } else if (parseFloat(priceListJson[index].cost) < parseFloat(minCost)) {
                                minCost = priceListJson[index].cost;
                            }
                        }
                    }
                }
                if (parseFloat(minCost) > 0) {
                    costField.value = parseFloat(minCost);
                    value.cost = costField.value;
                }
            }
            this.selectItemObject.set(element['checkbox'].value, value);
            $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
        }
    },

    /**
     * Click on product row
     *
     * @param {Object} grid
     * @param {String} event
     */
    rowClick: function (grid, event) {
        var trElement = Event.findElement(event, 'tr'),
            isInput = Event.element(event).tagName === 'INPUT',
            checked = false,
            checkbox = null;

        if (trElement) {
            checkbox = Element.getElementsBySelector(trElement, 'input');
            if (checkbox[0]) {
                checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                this.gridJsObject.setCheckboxChecked(checkbox[0], checked);
            }
        }
    },

    /**
     * Process event check checkbox in a row of grid
     *
     * @param {Object} grid
     * @param {Object} element
     * @param {Boolean} checked
     */
    checkCheckbox: function (grid, element, checked) {
        if (checked) {
            var value = {};
            this.editFields.each(function (el) {
                if (element[el]) {
                    element[el].show();
                    var spanEl = element[el].up('div').down('span');
                    if (spanEl)
                        spanEl.hide();
                    element[el].disabled = false;
                    value[el] = element[el].value;
                }
            });
            this.selectItemObject.set(element.value, value);
        } else {
            this.editFields.each(function (el) {
                if (element[el]) {
                    var spanEl = element[el].up('div').down('span');
                    if (spanEl) {
                        element[el].hide();
                        spanEl.show();
                    }
                    element[el].disabled = true;
                }
            });
            this.selectItemObject.unset(element.value);
        }
        $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
    },

    /**
     * send ajax request for warehouse action
     *
     * @param url
     * @returns {*}
     */
    gridAction: function (url, params, gridObject, callback) {
        var filters = $$('#' + gridObject.containerId + ' .filter input', '#' + gridObject.containerId + ' .filter select');
        var elements = [];
        for (var i in filters) {
            if (filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        var url = gridObject._addVarToUrl(url, gridObject.filterVar, encode_base64(Form.serializeElements(elements)));

        gridObject.reloadParams = gridObject.reloadParams || {};
        gridObject.reloadParams.form_key = FORM_KEY;
        gridObject.reloadParams.selected_items = $(this.hiddenInputField).value;
        if (params) {
            for (var index in params) {
                gridObject.reloadParams[index] = params[index];
            }
        }

        new Ajax.Request(url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ), {
            loaderArea: gridObject.containerId,
            parameters: gridObject.reloadParams || {},
            evalScripts: true,
            onFailure: gridObject._processFailure.bind(gridObject),
            onComplete: gridObject.initGridAjax.bind(gridObject),
            onSuccess: function (transport) {
                try {
                    var responseText = transport.responseText.replace(/>\s+</g, '><');

                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.error) {
                            alert(response.message);
                        }
                        if (response.ajaxExpired && response.ajaxRedirect) {
                            setLocation(response.ajaxRedirect);
                        }
                    } else {
                        /**
                         * For IE <= 7.
                         * If there are two elements, and first has name, that equals id of second.
                         * In this case, IE will choose one that is above
                         *
                         * @see https://prototype.lighthouseapp.com/projects/8886/tickets/994-id-selector-finds-elements-by-name-attribute-in-ie7
                         */
                        var divId = $(gridObject.containerId);
                        if (divId.id == gridObject.containerId) {
                            divId.update(responseText);
                        } else {
                            $$('div[id="' + gridObject.containerId + '"]')[0].update(responseText);
                        }
                    }
                } catch (e) {
                    var divId = $(gridObject.containerId);
                    if (divId.id == gridObject.containerId) {
                        divId.update(responseText);
                    } else {
                        $$('div[id="' + gridObject.containerId + '"]')[0].update(responseText);
                    }
                }
                if (callback)
                    callback();
            }.bind(this)
        });
        return;
    },

    save: function (event, params) {
        params = !params ? {} : params;
        if (this.hiddenInputField) {
            if ($(this.hiddenInputField).value == '{}') {
                event.stopPropagation();
                return alert(this.messages['noItemSelected']);
            } else {
                params.selected_items = $(this.hiddenInputField).value;
            }
            this.selectItemObject = $H({});
            $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
        }
        new Ajax.Request(
            this.saveUrl + (this.saveUrl.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
            {
                parameters: params,
                onSuccess: function (transport) {
                    this.reloadTabs();
                    this.reloadParent();
                    this.reloadTotal(false);
                    this.displayButton(transport);
                }.bind(this)
            }
        );
    },

    update: function (event) {
        if ($(this.hiddenInputField).value == '{}') {
            return alert(this.messages['noItemSelected']);
        }
        this.gridAction(this.saveUrl, {}, this.gridJsObject, function () {
            this.reloadParent();
            this.reloadTotal(false);
        }.bind(this));
        this.selectItemObject = $H({});
        $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
    },

    /**
     * Delete a grid item
     *
     * @param event
     */
    deleteItem: function (event) {
        event.stopPropagation();
        var purchaseId = event.element().getAttribute('purchase_id'),
            productId = event.element().getAttribute('product_id');
        this.gridAction(
            this.deleteUrl,
            {purchase_id: purchaseId, product_id: productId},
            this.gridJsObject,
            function () {
                this.reloadTotal(true);
                this.selectItemObject.unset(productId);
                $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
            }.bind(this)
        );
    },

    /**
     * Reload parent
     */
    reloadParent: function () {
        if (this.parentGridJsObject)
            if (this.tabContainer) {
                setTimeout(function () {
                    this.parentGridJsObject.doFilter();
                }.bind(this), 400);
            } else {
                this.parentGridJsObject.doFilter();
            }
    },

    /**
     * Reload tabs
     */
    reloadTabs: function () {
        if (this.tabContainer) {
            this.tabsReload.each(function (tabId, index) {
                if ($(tabId)) {
                    $(tabId).addClassName('notloaded');
                    if (index === 0)
                        this.tabContainer.showTabContent($(tabId));
                }
            }.bind(this));
        }
    },

    /**
     * Reload purchase order item
     */
    reloadTotal: function (immediate) {
        if (this.canReloadTotal) {
            if (immediate)
                this.processReloadTotal();
            else if (this.tabContainer || this.parentGridJsObject)
                setTimeout(function () {
                    this.processReloadTotal();
                }.bind(this), 400);
            else
                this.processReloadTotal();
        }
    },

    processReloadTotal: function () {
        new Ajax.Request(
            window.reloadTotalUrl + (window.reloadTotalUrl.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
            {
                parameters: {},
                onSuccess: function (transport) {
                    if (transport.responseText.length > 0) {
                        $('purchase_summary_footer').innerHTML = transport.responseText;
                    }
                }.bind(this)
            }
        );
    },

    displayButton: function (transport) {
        if (transport.responseText) {
            var buttonJson = JSON.parse(transport.responseText)
            if (buttonJson.button_show) {
                buttonJson.button_show.each(function (id) {
                    $$('#'+id).each(function(button){button.show()})
                })
            }
            if (buttonJson.button_hide) {
                buttonJson.button_hide.each(function (id) {
                    $$('#'+id).each(function(button){button.hide()})
                })
            }
        }
    }
}

var ReturnSuccessJS = Class.create();
ReturnSuccessJS.prototype = {
    initialize: function (config) {
        this.config = config;
        this.selectedItems = config.selectedItems ? Object.assign({}, config.selectedItems) : {};
        this.selectItemObject = $H(this.selectedItems);
        if (config.gridJsObjectName) {
            this.gridJsObject = window[config.gridJsObjectName];
            this.hiddenInputField = config.hiddenInputField;
            this.editFields = config.editFields;
            $(this.hiddenInputField).value = Object.toJSON(this.selectedItems);
        }
        if (config.parentGridJsObjectName)
            this.parentGridJsObject = window[config.parentGridJsObjectName];
        this.saveUrl = config.saveUrl;
        this.deleteUrl = config.deleteUrl;
        this.canReloadTotal = config.canReloadTotal;
        // if (config.priceListJson)
        //     this.priceListJson = config.priceListJson;
        // else if (config.priceListJson)
        //     this.priceListJson = config.priceListJson;
        this.deleteUrl = config.deleteUrl;
        if (config.tabContainer) {
            this.tabContainer = window[config.tabContainer];
            this.tabsReload = config.tabsReload;
        }
        this.messages = config.messages,
            this.tabIndex = 1000;

        if (config.gridJsObjectName) {
            this.gridJsObject.initRowCallback = this.initRow.bind(this);
            this.gridJsObject.rowClickCallback = this.rowClick.bind(this);
            this.gridJsObject.checkboxCheckCallback = this.checkCheckbox.bind(this);

            if (this.gridJsObject.rows) {
                this.gridJsObject.rows.each(function (row) {
                    this.initRow(this.gridJsObject, row);
                }.bind(this));
            }
        }

        if (config.reloadBtn) {
            this.reloadBtn = config.reloadBtn;
            if (Array.isArray(config.reloadBtn)) {
                config.reloadBtn.each(function (el) {
                    if ($(el))
                        Event.observe(el, 'click', function () {
                            this.gridJsObject.reload();
                        }.bind(this));
                }.bind(this))
            } else {
                Event.observe(config.reloadBtn, 'click', function () {
                    this.gridJsObject.reload();
                }.bind(this));
            }
        }

        if (config.saveBtn)
            Event.observe($(config.saveBtn), 'click', function (event) {
                this.save(event, null);
            }.bind(this));

        if (config.updateBtn)
            Event.observe($(config.updateBtn), 'click', function (event) {
                this.update(event);
            }.bind(this));
    },

    /**
     * Initialize grid row
     *
     * @param {Object} grid
     * @param {String} row
     */
    initRow: function (grid, row) {
        var checkbox = $(row).down('input[type=checkbox]'),
            deleteButton = $(row).down('a[class=delete_item]');
        if (checkbox) {
            this.editFields.each(function (el) {
                var element = $(row).down('input[name=' + el + ']');
                if (element) {
                    checkbox[el] = element;
                    checkbox[el + '_old'] = $(row).down('input[name=' + el + '_old]');
                    element.disabled = !checkbox.checked;
                    element.tabIndex = this.tabIndex++;
                    element['checkbox'] = checkbox;
                    Event.observe(element, 'keyup', this.textBoxChange.bind(this));
                }
            }.bind(this));
            var values = this.selectItemObject.get(checkbox.value);
            if (values) {
                this.editFields.each(function (el) {
                    checkbox[el].value = values[el]
                });
                this.gridJsObject.setCheckboxChecked(checkbox, true);
            }
            if (checkbox.checked)
                this.gridJsObject.setCheckboxChecked(checkbox, true);
        }
        if (deleteButton) {
            Event.observe(deleteButton, 'click', this.deleteItem.bind(this));
        }
    },

    /**
     * Process event change text box
     *
     * @param {String} event
     */
    textBoxChange: function (event) {
        var element = Event.element(event);
        if (element && element['checkbox'] && element['checkbox'].checked) {
            if (isNaN(element.value) || element.value < 0) {
                element.value = '';
                element.select();
            }
            var value = this.selectItemObject.get(element['checkbox'].value);
            value = value ? value : {};
            value[element.name] = element.value;
            // if (element.name == 'qty_orderred' || element.name == 'bill_qty')
            // {
            //     var checkbox = element['checkbox'];
            //     var productId = checkbox.value;
            //     var costField = checkbox['cost'];
            //     if(!costField)
            //         costField = checkbox['unit_price'];
            //     var minCost = 0;
            //     if(window.priceListJson) {
            //         for(var index in priceListJson){
            //             if ((priceListJson[index].product_id == productId)
            //                 && parseFloat(priceListJson[index].minimal_qty) <= parseFloat(element.value)
            //                 && parseFloat(priceListJson[index].cost) > 0) {
            //                 if (minCost == 0) {
            //                     minCost = priceListJson[index].cost;
            //                 } else if (parseFloat(priceListJson[index].cost) < parseFloat(minCost)) {
            //                     minCost = priceListJson[index].cost;
            //                 }
            //             }
            //         }
            //     }
            //     if (parseFloat(minCost) > 0) {
            //         costField.value = parseFloat(minCost);
            //         value.cost = costField.value;
            //     }
            // }
            this.selectItemObject.set(element['checkbox'].value, value);
            $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
        }
    },

    /**
     * Click on product row
     *
     * @param {Object} grid
     * @param {String} event
     */
    rowClick: function (grid, event) {
        var trElement = Event.findElement(event, 'tr'),
            isInput = Event.element(event).tagName === 'INPUT',
            checked = false,
            checkbox = null;

        if (trElement) {
            checkbox = Element.getElementsBySelector(trElement, 'input');
            if (checkbox[0]) {
                checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                this.gridJsObject.setCheckboxChecked(checkbox[0], checked);
            }
        }
    },

    /**
     * Process event check checkbox in a row of grid
     *
     * @param {Object} grid
     * @param {Object} element
     * @param {Boolean} checked
     */
    checkCheckbox: function (grid, element, checked) {
        if (checked) {
            var value = {};
            this.editFields.each(function (el) {
                if (element[el]) {
                    element[el].show();
                    var spanEl = element[el].up('div').down('span');
                    if (spanEl)
                        spanEl.hide();
                    element[el].disabled = false;
                    value[el] = element[el].value;
                }
            });
            this.selectItemObject.set(element.value, value);
        } else {
            this.editFields.each(function (el) {
                if (element[el]) {
                    var spanEl = element[el].up('div').down('span');
                    if (spanEl) {
                        element[el].hide();
                        spanEl.show();
                    }
                    element[el].disabled = true;
                }
            });
            this.selectItemObject.unset(element.value);
        }
        $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
    },

    /**
     * send ajax request for warehouse action
     *
     * @param url
     * @returns {*}
     */
    gridAction: function (url, params, gridObject, callback) {
        var filters = $$('#' + gridObject.containerId + ' .filter input', '#' + gridObject.containerId + ' .filter select');
        var elements = [];
        for (var i in filters) {
            if (filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        var url = gridObject._addVarToUrl(url, gridObject.filterVar, encode_base64(Form.serializeElements(elements)));

        gridObject.reloadParams = gridObject.reloadParams || {};
        gridObject.reloadParams.form_key = FORM_KEY;
        gridObject.reloadParams.selected_items = $(this.hiddenInputField).value;
        if (params) {
            for (var index in params) {
                gridObject.reloadParams[index] = params[index];
            }
        }

        new Ajax.Request(url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ), {
            loaderArea: gridObject.containerId,
            parameters: gridObject.reloadParams || {},
            evalScripts: true,
            onFailure: gridObject._processFailure.bind(gridObject),
            onComplete: gridObject.initGridAjax.bind(gridObject),
            onSuccess: function (transport) {
                try {
                    var responseText = transport.responseText.replace(/>\s+</g, '><');

                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.error) {
                            alert(response.message);
                        }
                        if (response.ajaxExpired && response.ajaxRedirect) {
                            setLocation(response.ajaxRedirect);
                        }
                    } else {
                        /**
                         * For IE <= 7.
                         * If there are two elements, and first has name, that equals id of second.
                         * In this case, IE will choose one that is above
                         *
                         * @see https://prototype.lighthouseapp.com/projects/8886/tickets/994-id-selector-finds-elements-by-name-attribute-in-ie7
                         */
                        var divId = $(gridObject.containerId);
                        if (divId.id == gridObject.containerId) {
                            divId.update(responseText);
                        } else {
                            $$('div[id="' + gridObject.containerId + '"]')[0].update(responseText);
                        }
                    }
                } catch (e) {
                    var divId = $(gridObject.containerId);
                    if (divId.id == gridObject.containerId) {
                        divId.update(responseText);
                    } else {
                        $$('div[id="' + gridObject.containerId + '"]')[0].update(responseText);
                    }
                }
                if (callback)
                    callback();
            }.bind(this)
        });
        return;
    },

    save: function (event, params) {
        params = !params ? {} : params;
        if (this.hiddenInputField) {
            if ($(this.hiddenInputField).value == '{}') {
                event.stopPropagation();
                return alert(this.messages['noItemSelected']);
            } else {
                params.selected_items = $(this.hiddenInputField).value;
            }
            this.selectItemObject = $H({});
            $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
        }
        new Ajax.Request(
            this.saveUrl + (this.saveUrl.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
            {
                parameters: params,
                onSuccess: function (transport) {
                    this.reloadTabs();
                    this.reloadParent();
                    this.reloadTotal(false);
                    this.displayButton(transport);
                }.bind(this)
            }
        );
    },

    update: function (event) {
        if ($(this.hiddenInputField).value == '{}') {
            return alert(this.messages['noItemSelected']);
        }
        this.gridAction(this.saveUrl, {}, this.gridJsObject, function () {
            this.reloadParent();
            this.reloadTotal(false);
        }.bind(this));
        this.selectItemObject = $H({});
        $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
    },

    /**
     * Delete a grid item
     *
     * @param event
     */
    deleteItem: function (event) {
        event.stopPropagation();
        var returnId = event.element().getAttribute('return_id'),
            productId = event.element().getAttribute('product_id');
        this.gridAction(
            this.deleteUrl,
            {return_id: returnId, product_id: productId},
            this.gridJsObject,
            function () {
                this.reloadTotal(true);
                this.selectItemObject.unset(productId);
                $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
            }.bind(this)
        );
    },

    /**
     * Reload parent
     */
    reloadParent: function () {
        if (this.parentGridJsObject)
            if (this.tabContainer) {
                setTimeout(function () {
                    this.parentGridJsObject.doFilter();
                }.bind(this), 400);
            } else {
                this.parentGridJsObject.doFilter();
            }
    },

    /**
     * Reload tabs
     */
    reloadTabs: function () {
        if (this.tabContainer) {
            this.tabsReload.each(function (tabId, index) {
                if ($(tabId)) {
                    $(tabId).addClassName('notloaded');
                    if (index === 0)
                        this.tabContainer.showTabContent($(tabId));
                }
            }.bind(this));
        }
    },

    /**
     * Reload purchase order item
     */
    reloadTotal: function (immediate) {
        if (this.canReloadTotal) {
            if (immediate)
                this.processReloadTotal();
            else if (this.tabContainer || this.parentGridJsObject)
                setTimeout(function () {
                    this.processReloadTotal();
                }.bind(this), 400);
            else
                this.processReloadTotal();
        }
    },

    processReloadTotal: function () {
        new Ajax.Request(
            window.reloadTotalUrl + (window.reloadTotalUrl.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ),
            {
                parameters: {},
                onSuccess: function (transport) {
                    if (transport.responseText.length > 0) {
                        $('purchase_summary_footer').innerHTML = transport.responseText;
                    }
                }.bind(this)
            }
        );
    },

    displayButton: function (transport) {
        if (transport.responseText) {
            var buttonJson = JSON.parse(transport.responseText)
            if (buttonJson.button_show) {
                buttonJson.button_show.each(function (id) {
                    $$('#'+id).each(function(button){button.show()})
                })
            }
            if (buttonJson.button_hide) {
                buttonJson.button_hide.each(function (id) {
                    $$('#'+id).each(function(button){button.hide()})
                })
            }
        }
    }
}

Calendar.prototype.create = function (_par) {
    var parent = null;
    if (! _par) {
        // default parent is the document body, in which case we create
        // a popup calendar.
        parent = document.getElementsByTagName("body")[0];
        this.isPopup = true;
    } else {
        parent = _par;
        this.isPopup = false;
    }
    this.date = this.dateStr ? new CalendarDateObject(this.dateStr) : new CalendarDateObject();

    var table = Calendar.createElement("table");
    this.table = table;
    table.cellSpacing = 0;
    table.cellPadding = 0;
    table.calendar = this;
    Calendar.addEvent(table, "mousedown", Calendar.tableMouseDown);

    var div = Calendar.createElement("div");
    this.element = div;
    div.className = "calendar";
    if (this.isPopup) {
        div.style.position = "absolute";
        div.style.display = "none";
    }
    div.appendChild(table);

    var thead = Calendar.createElement("thead", table);
    var cell = null;
    var row = null;

    var cal = this;
    var hh = function (text, cs, navtype) {
        cell = Calendar.createElement("td", row);
        cell.colSpan = cs;
        cell.className = "button";
        if (navtype != 0 && Math.abs(navtype) <= 2)
            cell.className += " nav";
        Calendar._add_evs(cell);
        cell.calendar = cal;
        cell.navtype = navtype;
        cell.innerHTML = "<div unselectable='on'>" + text + "</div>";
        return cell;
    };

    row = Calendar.createElement("tr", thead);
    var title_length = 6;
    (this.isPopup) && --title_length;
    (this.weekNumbers) && ++title_length;

    hh("?", 1, 400).ttip = Calendar._TT["INFO"];
    this.title = hh("", title_length, 300);
    this.title.className = "title";
    if (this.isPopup) {
        this.title.ttip = Calendar._TT["DRAG_TO_MOVE"];
        this.title.style.cursor = "move";
        hh("&#x00d7;", 1, 200).ttip = Calendar._TT["CLOSE"];
    }

    row = Calendar.createElement("tr", thead);
    row.className = "headrow";

    this._nav_py = hh("&#x00ab;", 1, -2);
    this._nav_py.ttip = Calendar._TT["PREV_YEAR"];

    this._nav_pm = hh("&#x2039;", 1, -1);
    this._nav_pm.ttip = Calendar._TT["PREV_MONTH"];

    this._nav_now = hh(Calendar._TT["TODAY"], this.weekNumbers ? 4 : 3, 0);
    this._nav_now.ttip = Calendar._TT["GO_TODAY"];

    this._nav_nm = hh("&#x203a;", 1, 1);
    this._nav_nm.ttip = Calendar._TT["NEXT_MONTH"];

    this._nav_ny = hh("&#x00bb;", 1, 2);
    this._nav_ny.ttip = Calendar._TT["NEXT_YEAR"];

    // day names
    row = Calendar.createElement("tr", thead);
    row.className = "daynames";
    if (this.weekNumbers) {
        cell = Calendar.createElement("td", row);
        cell.className = "name wn";
        cell.innerHTML = Calendar._TT["WK"];
    }
    for (var i = 7; i > 0; --i) {
        cell = Calendar.createElement("td", row);
        if (!i) {
            cell.navtype = 100;
            cell.calendar = this;
            Calendar._add_evs(cell);
        }
    }
    this.firstdayname = (this.weekNumbers) ? row.firstChild.nextSibling : row.firstChild;
    this._displayWeekdays();

    var tbody = Calendar.createElement("tbody", table);
    this.tbody = tbody;

    for (i = 6; i > 0; --i) {
        row = Calendar.createElement("tr", tbody);
        if (this.weekNumbers) {
            cell = Calendar.createElement("td", row);
        }
        for (var j = 7; j > 0; --j) {
            cell = Calendar.createElement("td", row);
            cell.calendar = this;
            Calendar._add_evs(cell);
        }
    }

    if (this.showsTime) {
        row = Calendar.createElement("tr", tbody);
        row.className = "time";

        cell = Calendar.createElement("td", row);
        cell.className = "time";
        cell.colSpan = 2;
        cell.innerHTML = Calendar._TT["TIME"] || "&nbsp;";

        cell = Calendar.createElement("td", row);
        cell.className = "time";
        cell.colSpan = this.weekNumbers ? 4 : 3;

        (function(){
            function makeTimePart(className, init, range_start, range_end) {
                var part = Calendar.createElement("span", cell);
                part.className = className;
                part.innerHTML = init;
                part.calendar = cal;
                part.ttip = Calendar._TT["TIME_PART"];
                part.navtype = 50;
                part._range = [];
                if (typeof range_start != "number")
                    part._range = range_start;
                else {
                    for (var i = range_start; i <= range_end; ++i) {
                        var txt;
                        if (i < 10 && range_end >= 10) txt = '0' + i;
                        else txt = '' + i;
                        part._range[part._range.length] = txt;
                    }
                }
                Calendar._add_evs(part);
                return part;
            };
            var hrs = cal.date.getHours();
            var mins = cal.date.getMinutes();
            var t12 = !cal.time24;
            var pm = (hrs > 12);
            if (t12 && pm) hrs -= 12;
            var H = makeTimePart("hour", hrs, t12 ? 1 : 0, t12 ? 12 : 23);
            var span = Calendar.createElement("span", cell);
            span.innerHTML = ":";
            span.className = "colon";
            var M = makeTimePart("minute", mins, 0, 59);
            var AP = null;
            cell = Calendar.createElement("td", row);
            cell.className = "time";
            cell.colSpan = 2;
            if (t12)
                AP = makeTimePart("ampm", pm ? "pm" : "am", ["am", "pm"]);
            else
                cell.innerHTML = "&nbsp;";

            cal.onSetTime = function() {
                var pm, hrs = this.date.getHours(),
                    mins = this.date.getMinutes();
                if (t12) {
                    pm = (hrs >= 12);
                    if (pm) hrs -= 12;
                    if (hrs == 0) hrs = 12;
                    AP.innerHTML = pm ? "pm" : "am";
                }
                H.innerHTML = (hrs < 10) ? ("0" + hrs) : hrs;
                M.innerHTML = (mins < 10) ? ("0" + mins) : mins;
            };

            cal.onUpdateTime = function() {
                var date = this.date;
                var h = parseInt(H.innerHTML, 10);
                if (t12) {
                    if (/pm/i.test(AP.innerHTML) && h < 12)
                        h += 12;
                    else if (/am/i.test(AP.innerHTML) && h == 12)
                        h = 0;
                }
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();
                date.setHours(h);
                date.setMinutes(parseInt(M.innerHTML, 10));
                date.setFullYear(y);
                date.setMonth(m);
                date.setDate(d);
                this.dateClicked = false;
                this.callHandler();
            };
        })();
    } else {
        this.onSetTime = this.onUpdateTime = function() {};
    }

    var tfoot = Calendar.createElement("tfoot", table);

    row = Calendar.createElement("tr", tfoot);
    row.className = "footrow";

    cell = hh(Calendar._TT["SEL_DATE"], this.weekNumbers ? 8 : 7, 300);
    cell.className = "ttip";
    if (this.isPopup) {
        cell.ttip = Calendar._TT["DRAG_TO_MOVE"];
        cell.style.cursor = "move";
    }
    this.tooltips = cell;

    div = Calendar.createElement("div", this.element);
    this.monthsCombo = div;
    div.className = "combo";
    for (i = 0; i < Calendar._MN.length; ++i) {
        var mn = Calendar.createElement("div");
        mn.className = Calendar.is_ie ? "label-IEfix" : "label";
        mn.month = i;
        mn.innerHTML = Calendar._SMN[i];
        div.appendChild(mn);
    }

    div = Calendar.createElement("div", this.element);
    this.yearsCombo = div;
    div.className = "combo";
    for (i = 12; i > 0; --i) {
        var yr = Calendar.createElement("div");
        yr.className = Calendar.is_ie ? "label-IEfix" : "label";
        div.appendChild(yr);
    }

    this._init(this.firstDayOfWeek, this.date);
    var buttonId = this.params.button.id;
    var parentId;
    if(buttonId){
        switch (buttonId){
            case 'received_at_trig':
                parentId = 'received_item_modal_dialog';
                break;
            case 'returned_at_trig':
                parentId = 'returned_item_modal_dialog';
                break;
            case 'transferred_at_trig':
                parentId = 'transferred_item_modal_dialog';
                break;
            case 'billed_at_trig':
                parentId = 'create_invoice_modal_dialog';
                break;
            case 'register_payment_at_trig':
                parentId = 'register_payment_modal_dialog';
                break;
            case 'register_refund_at_trig':
                parentId = 'register_refund_modal_dialog';
                break;
            case 'supply_need_forecast_date_to_trig':
            case 'supply_need_from_date_trig':
            case 'supply_need_to_date_trig':
                parentId = 'supply_need_product_modal_dialog';
                break;
            case 'scan_barcode_received_at_trig':
                parentId = 'scan_barcode_received_item_modal_dialog';
                break;
            case 'scan_barcode_returned_at_trig':
                parentId = 'scan_barcode_returned_item_modal_dialog';
                break;
            case 'scan_barcode_transferred_at_trig':
                parentId = 'scan_barcode_transferred_item_modal_dialog';
                break;
        }
    }
    if(parentId && $(parentId)){
        parent = $(parentId);
    }
    parent.appendChild(this.element);
};

var dates = {
    convert:function(d) {
        // Converts the date in d to a date-object. The input can be:
        //   a date object: returned without modification
        //  an array      : Interpreted as [year,month,day]. NOTE: month is 0-11.
        //   a number     : Interpreted as number of milliseconds
        //                  since 1 Jan 1970 (a timestamp) 
        //   a string     : Any format supported by the javascript engine, like
        //                  "YYYY/MM/DD", "MM/DD/YYYY", "Jan 31 2009" etc.
        //  an object     : Interpreted as an object with year, month and date
        //                  attributes.  **NOTE** month is 0-11.
        return (
            d.constructor === Date ? d :
                d.constructor === Array ? new Date(d[0],d[1],d[2]) :
                    d.constructor === Number ? new Date(d) :
                        d.constructor === String ? new Date(d) :
                            typeof d === "object" ? new Date(d.year,d.month,d.date) :
                                NaN
        );
    },
    compare:function(a,b) {
        // Compare two dates (could be of any type supported by the convert
        // function above) and returns:
        //  -1 : if a < b
        //   0 : if a = b
        //   1 : if a > b
        // NaN : if a or b is an illegal date
        // NOTE: The code inside isFinite does an assignment (=).
        return (
            isFinite(a=this.convert(a).valueOf()) &&
            isFinite(b=this.convert(b).valueOf()) ?
            (a>b)-(a<b) :
                NaN
        );
    },
    inRange:function(d,start,end) {
        // Checks if date in d is between dates in start and end.
        // Returns a boolean or NaN:
        //    true  : if d is between start and end (inclusive)
        //    false : if d is before start or after end
        //    NaN   : if one or more of the dates is illegal.
        // NOTE: The code inside isFinite does an assignment (=).
        return (
            isFinite(d=this.convert(d).valueOf()) &&
            isFinite(start=this.convert(start).valueOf()) &&
            isFinite(end=this.convert(end).valueOf()) ?
            start <= d && d <= end :
                NaN
        );
    }
}