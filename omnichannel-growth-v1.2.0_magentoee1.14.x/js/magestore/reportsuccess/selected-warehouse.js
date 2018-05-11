/**
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
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */


var SelectedWarehouse = Class.create();
SelectedWarehouse.prototype = {
    initialize: function(config) {
        this.gridJsObject = window[config.gridJsObjectName];
        this.changeWarehouseBtn = config.changeWarehouseBtn;
        this.updateReportBtn = config.updateReportBtn;
        this.urls = config.urls;
        this.editColumnsUrl = config.editColumnsUrl;
        if(config.changeWarehouseBtn)
            Event.observe(config.changeWarehouseBtn, 'change', this.changeWarehouse.bind(this));

        if(config.updateReportBtn)
            Event.observe(config.updateReportBtn, 'click', this.selectmultilWarehouse.bind(this));

        if(config.modifiColumn) {
            Event.observe('cancel_edit_columns', 'click', this.cancelEditColumns.bind(this));
            Event.observe('apply_edit_columns', 'click', this.applyEditColumns.bind(this));
        }
        this.modifiColumn_check = config.modifiColumn;
        // Firefox 1.0+
        this.isFirefox = typeof InstallTrigger !== 'undefined';
        this.isChrome = !!window.chrome && !!window.chrome.webstore;
    },

    /**
     * Change reload grid after change warehouse
     * @param event
     */
    changeWarehouse: function(event){
        var warehouseId = event.target.value;
        var params = {'warehouse_id': warehouseId};
        if(($('date_from'))){
            var params = {'warehouse_id': warehouseId,
                          'select_date': $('date_from').value,
            };
        }
        this.warehouseAction(params, this.gridJsObject);
    },
    warehouseAction: function(params, gridObject) {
        if(this.modifiColumn_check) {
            /* fix reload grid twice */
            //this.reloadgrid($$('#report-edit-column')[0]);
        }
        var filters = $$('#'+gridObject.containerId+' .filter input', '#'+gridObject.containerId+' .filter select');
        var elements = [];
        for(var i in filters){
            if(filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        gridObject.reloadParams = gridObject.reloadParams || {};
        gridObject.reloadParams.form_key = FORM_KEY;
        if (params) {
            for(var index in params) {
                gridObject.reloadParams[index] = params[index];
            }
        }

        var url = this.gridJsObject.url;
        var url = gridObject._addVarToUrl(url, gridObject.filterVar, encode_base64(Form.serializeElements(elements)));
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
                        if(this.urls){
                            new Ajax.Updater(
                                {success: 'formLowestSuccess'}, this.urls, {
                                    method: 'post',
                                    asynchronous: false,
                                    evalScripts: false,
                                    onComplete: function (request, json) {
                                    },
                                    onLoading: function (request, json) {
                                    },
                                    onSuccess: function (transport) {
                                        var data = JSON.parse(transport.responseText);
                                        $$('#totals_report')[0].innerHTML = data;
                                    }
                                }
                            );
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

    selectmultilWarehouse: function(event){
        if(($('date_from'))){
            var warehouseId = $('select_warehouse_option').value;
            var params = {'warehouse_id': warehouseId,
                'select_date': $('date_from').value,
            };
        }else{
            var params = {'type': $$('#select_type_option')[0].value,
                'warehouse_id' :  [this.getValue()] };
        }
        this.updateReport(params, this.gridJsObject);
    },
    updateReport : function(params,gridObject ){
        gridObject.reloadParams = gridObject.reloadParams || {};
        gridObject.reloadParams.form_key = FORM_KEY;
        if (params) {
            for(var index in params) {
                gridObject.reloadParams[index] = params[index];
            }
        }
        this.gridJsObject.doFilter();
    },

    getValue: function getValue()
    {
        var result =[];
        var x= $$('#select_warehouse_option_multil')[0];
        for (var i = 0; i < x.options.length; i++) {
            if(x.options[i].selected ==true){
                result.push(x.options[i].value);
            }
        }
        //var myJsonString = JSON.stringify(result);
        return result;
    },
    modifiColumn : function(event){
        var  myDiv = $$('#report-edit-column')[0];
        if(myDiv.getAttribute('inactive')){
            this.hidepopup(myDiv);
            return;
        }
        if(myDiv.getAttribute('dofilter')){
            this.reloadgrid(myDiv);
            return;
        }
        myDiv.setAttribute( "style", "display:block;" );
        //if(!this.isFirefox){
        //    $$('#'+event.id)[0].insert(myDiv);
        //}else{
        //    //var myDiv = new Element('div');
        //    //myDiv.setAttribute( "class", "blueClass" );
        //    var style = "display:block;  margin-left:"+screen.width/1.6+"px;";
        //    myDiv.setAttribute( "style", style );
        //    //myDiv.update('Helloasd');
        //}
        var style = "display:block;  margin-left:"+screen.width/1.6+"px;";
        myDiv.setAttribute( "style", style );
    },
    cancelEditColumns : function(event){
        myDiv = $$('#report-edit-column')[0];
        myDiv.setAttribute("inactive", "1");
        //if(this.isFirefox){
            this.modifiColumn(event);
        //}
        return;
    },
    applyEditColumns : function(event){
        myDiv = $$('#report-edit-column')[0];
        myDiv.setAttribute("dofilter", "1");
        //if(this.isFirefox){
            this.modifiColumn(event);
        //}
        return;
    },
    hidepopup : function(element){
        element.setAttribute("inactive", "");
        element.hide();
    },
    reloadgrid : function(element){
        element.setAttribute("dofilter", "");
        $$('#update_report')[0].insert(element);
        element.hide();
        var data ='null:0';
        //$$("input:checked").each(function(tag,index)
        $$("input[name='editcolumns']").each(function(tag,index){
            if(!tag.checked)
                data += ','+tag.value+':0';

            if(tag.checked)
                data += ','+tag.value+':1';
        });

        new Ajax.Request( this.editColumnsUrl , {
            parameters: {
                'columns': data,
                'grid' : this.gridJsObject.containerId+'JsObject'
            },
            evalScripts: true,
            onSuccess: function(transport) {
                try {
                    this.gridJsObject.doFilter();
                } catch (e) {
                }
                if (callback)
                    callback();
            }.bind(this)
        });
    },
}