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

var WarehouseSelectedProduct = Class.create();
WarehouseSelectedProduct.prototype = {
    initialize: function(config) {
        this.selectedItems = {};
        this.selectItemObject = $H(this.selectedItems);
        this.gridJsObject = window[config.gridJsObjectName];
        this.gridJsParentObject = window[config.gridJsParentObjectName];
        this.gridJsStockInformationObject = window[config.gridJsStockInformationObjectName];
        this.gridJsStockMovementObject = window[config.gridJsStockMovementObjectName];
        this.hiddenInputField = config.hiddenInputField;
        this.editFields = config.editFields;
        this.changeWarehouseBtn = config.changeWarehouseBtn;
        this.saveUrl = config.saveUrl;
        this.deleteUrl = config.deleteUrl;
        this.stockInformationUrl = config.stockInformationUrl;
        this.stockMovementUrl = config.stockMovementUrl;
        this.tabIndex = 1000;
        this.messages = config.messages;
        $(this.hiddenInputField).value = Object.toJSON(this.selectedItems);

        this.gridJsObject.initRowCallback = this.initRow.bind(this);
        this.gridJsObject.rowClickCallback = this.rowClick.bind(this);
        this.gridJsObject.checkboxCheckCallback = this.checkCheckbox.bind(this);

        if (this.gridJsObject.rows) {
            this.gridJsObject.rows.each(function (row) {
                this.initRow(this.gridJsObject, row);
            }.bind(this));
        }

        if(config.changeWarehouseBtn)
            Event.observe(config.changeWarehouseBtn, 'change', this.changeWarehouse.bind(this));
        
        if(config.reloadBtn)
            Event.observe(config.reloadBtn, 'click', function(){
                this.gridJsObject.reload();
            }.bind(this));

        if(config.saveBtn)
            Event.observe($(config.saveBtn), 'click', function(event){
                this.updateStock(event);
                event.preventDefault();
            }.bind(this));

        if(config.deleteBtn)
            Event.observe($(config.deleteBtn), 'click', function(event){
                if(confirm(config.messages['confirmMessage']))
                    this.updateStock(event);
            }.bind(this));
    },

    /**
     * Initialize grid row
     *
     * @param {Object} grid
     * @param {String} row
     */
     initRow: function(grid, row){
        var checkbox = $(row).down('input[type=checkbox]'),
            viewStockInformationButton = $(row).down('a[class=view_stock_information]'),
            viewStockMovementButton = $(row).down('a[class=view_stock_movement]'),
            deleteButton = $(row).down('a[class=delete_item]');
        if(checkbox){
            this.editFields.each(function(el){
                var element = $(row).down('input[name='+el+']');
                if(element){
                    checkbox[el] = element;
                    checkbox[el+'_old'] = $(row).down('input[name='+el+'_old]');
                    element.disabled = !checkbox.checked;
                    element.tabIndex = this.tabIndex++;
                    element['checkbox'] = checkbox;
                    Event.observe(element, 'keyup', this.textBoxChange.bind(this));
                }
                var element = $(row).down('select[name='+el+']');
                if(element){
                    checkbox[el] = element;
                    element.disabled = !checkbox.checked;
                    element.tabIndex = this.tabIndex++;
                    element['checkbox'] = checkbox;
                    Event.observe(element, 'change', this.textBoxChange.bind(this));
                }
            }.bind(this));
            var values = this.selectItemObject.get(checkbox.value);
            if(values){
                this.editFields.each(function(el) {
                    checkbox[el].value = values[el]
                });
                this.gridJsObject.setCheckboxChecked(checkbox, true);
            };
            if(checkbox.checked)
                this.gridJsObject.setCheckboxChecked(checkbox, true);
        }
        if(viewStockInformationButton)
            Event.observe(viewStockInformationButton, 'click', function(event){
                this.showPopup(event, 'stock_information_grid_name', this.stockInformationUrl, this.gridJsStockInformationObject);
            }.bind(this));
        if(viewStockMovementButton)
            Event.observe(viewStockMovementButton, 'click', function(event){
                this.showPopup(event, 'stock_movement_grid_name', this.stockMovementUrl, this.gridJsStockMovementObject);
            }.bind(this));
        if(deleteButton)
            Event.observe(deleteButton, 'click', this.deleteItem.bind(this));
    },

    /**
     * Process event change text box
     *
     * @param {String} event
     */
    textBoxChange: function(event) {
        var element = Event.element(event);
        if (element && element['checkbox'] && element['checkbox'].checked) {
            if(element.name == "sum_total_qty" && (element.value == '' || isNaN(element.value) || element.value < 0)){
                element.value = 0;
                element.select();
            }
            var value = this.selectItemObject.get(element['checkbox'].value);
            value = value ? value : {};
            value[element.name] = element.value;
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
    rowClick: function(grid, event){
        if(this.editFields.indexOf(event.target.name)!=-1 || event.target.tagName == 'A')
            return false;
        var trElement = Event.findElement(event, 'tr'),
            isInput = Event.element(event).tagName === 'INPUT',
            checked = false,
            checkbox = null;

        if (trElement) {
            checkbox = trElement.down('input[type=checkbox]');

            if (checkbox) {
                checked = isInput ? checkbox.checked : !checkbox.checked;
                this.gridJsObject.setCheckboxChecked(checkbox, checked);
            }
        }
    },

    /**
     * Process event check checkbox in a row of grid
     *
     * @param {Object} grid
     * @param {Object} checkbox
     * @param {Boolean} checked
     */
    checkCheckbox: function(grid, checkbox, checked) {
        if (checked) {
            var value = {};
            this.editFields.each(function(el){
                if (checkbox[el]){
                    checkbox[el].disabled = false;
                    value[el] = checkbox[el].value;
                    if(checkbox[el].tagName == "INPUT") {
                        checkbox[el].show();
                        checkbox[el].up('div').down('span').hide();
                        value[el + '_old'] = checkbox[el + '_old'].value;
                    }
                }
            }.bind(this));
            this.selectItemObject.set(checkbox.value, value);
        } else {
            this.editFields.each(function(el){
                if (checkbox[el]){
                    if(checkbox[el].tagName == "INPUT"){
                        checkbox[el].hide();
                        checkbox[el].up('div').down('span').show();
                    }
                    checkbox[el].disabled = true;
                }
            }.bind(this));
            this.selectItemObject.unset(checkbox.value);
        }
        $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
        if(this.gridJsObject.massaction){
            if(checked) {
                this.gridJsObject.massaction.checkedString = 
                    varienStringArray.add(checkbox.value, this.gridJsObject.massaction.checkedString);
            } else {
                this.gridJsObject.massaction.checkedString =
                    varienStringArray.remove(checkbox.value, this.gridJsObject.massaction.checkedString);
            }
            this.gridJsObject.massaction.updateCount();
        }
    },

    /**
     * Change reload grid after change warehouse
     * @param event
     */
    changeWarehouse: function(event){
        this.selectItemObject = $H({});
        $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
        var warehouseId = event.target.value;
        var params = {'warehouse_id': warehouseId};
        this.warehouseAction(this.gridJsObject.url, params, this.gridJsObject);
    },

    /**
     * Update Stock of warehouse
     * 
     * @param event
     * @returns {*}
     */
    updateStock: function(event){
        if(this.changeWarehouseBtn){
            if($(this.changeWarehouseBtn).value=='0'){
                return alert(this.messages['noWarehouseSelected']);
            }
        }
        if($(this.hiddenInputField).value=='{}'){
            return alert(this.messages['noItemSelected']);
        }
        return this.warehouseAction(this.saveUrl, null, this.gridJsObject, function(){
            if(this.gridJsParentObject)
                this.gridJsParentObject.reload();
            this.selectItemObject = $H({});
            $(this.hiddenInputField).value = Object.toJSON(this.selectItemObject);
        }.bind(this));
    },

    /**
     * Delete grid item
     * 
     * @param event
     */
    deleteItem: function(event){
        event.preventDefault();
        if(confirm(this.messages['deleteConfirm']))
            this.warehouseAction(this.deleteUrl, {'item_id': event.target.getAttribute('value')}, this.gridJsObject);
    },

    /**
     * send ajax request for warehouse action
     *
     * @param url
     * @returns {*}
     */
    warehouseAction: function(url, params, gridObject, callback) {
        var filters = $$('#'+gridObject.containerId+' .filter input', '#'+gridObject.containerId+' .filter select');
        var elements = [];
        for(var i in filters){
            if(filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        var url = gridObject._addVarToUrl(url, gridObject.filterVar, encode_base64(Form.serializeElements(elements)));
        
        gridObject.reloadParams = gridObject.reloadParams || {};
        gridObject.reloadParams.form_key = FORM_KEY;
        gridObject.reloadParams.selected_items = $(this.hiddenInputField).value;
        if (params) {
            for(var index in params) {
                gridObject.reloadParams[index] = params[index];
            }
        }
        
        new Ajax.Request(url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ), {
            loaderArea: gridObject.containerId,
            parameters: gridObject.reloadParams || {},
            evalScripts: true,
            onFailure: gridObject._processFailure.bind(gridObject),
            onComplete: gridObject.initGridAjax.bind(gridObject),
            onSuccess: function(transport) {
                try {
                    var responseText = transport.responseText.replace(/>\s+</g, '><');

                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.error) {
                            alert(response.message);
                        }
                        if(response.ajaxExpired && response.ajaxRedirect) {
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
                            $$('div[id="'+gridObject.containerId+'"]')[0].update(responseText);
                        }
                    }
                } catch (e) {
                    var divId = $(gridObject.containerId);
                    if (divId.id == gridObject.containerId) {
                        divId.update(responseText);
                    } else {
                        $$('div[id="'+gridObject.containerId+'"]')[0].update(responseText);
                    }
                }
                if (callback)
                    callback();
            }.bind(this)
        });
        return;
    },

    /**
     * show product popup
     *
     * @param event
     */
    showPopup: function(event, labelName, url, gridObject){
        var productId = event.element().getAttribute('value');
        var productName = event.element().getAttribute('product-name');
        $(labelName).innerHTML = productName;
        return this.warehouseAction(url, {id: productId}, gridObject);
    },
}