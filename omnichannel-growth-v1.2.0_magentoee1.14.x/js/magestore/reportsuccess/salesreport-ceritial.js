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

var SalesreportCeritial = Class.create();
SalesreportCeritial.prototype = {
    initialize: function(config) {
        this.gridJsObject = window[config.gridJsObjectName];
        this.changeWarehouseBtn = config.changeWarehouseBtn;
        this.updateReportBtn = config.updateReportBtn;
        this.urls = config.urls;
        this.urlsUpdateSalesData = config.urlsUpdateSalesData;
        this.editColumnsUrl = config.editColumnsUrl;

        //if(config.changeWarehouseBtn)
        //    Event.observe(config.changeWarehouseBtn, 'change', this.changeWarehouse.bind(this));

        if(config.updateReportBtn)
            Event.observe(config.updateReportBtn, 'click', this.changeWarehouse.bind(this));

        if(config.modifiColumn) {
            Event.observe('cancel_edit_columns', 'click', this.cancelEditColumns.bind(this));
            Event.observe('apply_edit_columns', 'click', this.applyEditColumns.bind(this));
        }
        this.modifiColumn_check = config.modifiColumn;
        // Firefox 1.0+
        this.isFirefox = typeof InstallTrigger !== 'undefined';
        this.isChrome = !!window.chrome && !!window.chrome.webstore;
        this.img = config.img;
        this.alt = config.alt;
        document.on('change', 'input[name=editcolumnsDimentions]', this.disableColumns.bind(this));
    },

    /**
     * Change reload grid after change warehouse
     * @param event
     */
    getValue: function getValue()
    {
        var result =[];
        var x= $$('#select_warehouse_option')[0];
        for (var i = 0; i < x.options.length; i++) {
            if(x.options[i].selected ==true){
                result.push(x.options[i].value);
            }
        }
        return [result];
    },
    getParamData : function (){
        var warehouseId = this.getValue();
        var start_moment = moment();
        var end_moment = moment();
        var start_date = $$("input[name=daterangepicker_start]")[0].value ? $$("input[name=daterangepicker_start]")[0].value : start_moment.format('MMMM D, YYYY');
        var end_date = $$("input[name=daterangepicker_end]")[0].value ? $$("input[name=daterangepicker_end]")[0].value : end_moment.format('MMMM D, YYYY');
        var params = {'warehouse_id': warehouseId,
            'date_from': start_date,
            'date_to': end_date
        };
        return params;
    },
    changeWarehouse: function(event){
        if( event && (!$$("input[name=daterangepicker_start]")[0].value || !$$("input[name=daterangepicker_end]")[0].value)){
            alert ('Please select another date ranges!');
            return;
        }
        var params = this.getParamData();
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

    modifiColumn : function(event){
        this.disableColumns();
        var  myDiv = $$('#report-edit-column')[0];
        if(myDiv.getAttribute('inactive')){
            this.hidepopup(myDiv);
            return;
        }
        if(myDiv.getAttribute('dofilter')){
            this.hidepopup(myDiv);
            this.reloadgrid(myDiv);
            return;
        }
        myDiv.setAttribute( "style", "display:block;" );
        var style = "display:block;  margin-left:"+screen.width/4+"px;";
        myDiv.setAttribute( "style", style );
    },
    disableColumns : function(event){
        var self = this;
        var dimenstion = $$("input[name='editcolumnsDimentions']");
        var list_disable = ['created_at','updated_at','sku','name','customer_email','customer_group_id','status','order_id','shipping_method','payment_method'];
        var list_sku = ['sku','name'];
        var list_customer = ['customer_email','customer_group_id'];
        var list_customer_group = ['customer_group_id'];
        var list_order_status = ['status'];
        var list_order_id = ['order_id'];
        var list_shipping = ['shipping_method'];
        var list_payment = ['payment_method'];

        self.disableAction(list_disable);
        var check_all = false;
        var check_sku = false;
        var check_customer = false;
        var check_customer_group = false;
        var check_order = false;
        var check_order_id = false;
        var check_shipping = false;
        var check_payment = false;

        dimenstion.each(function(tag,index){
            if(tag.checked){
                check_all = true;
            }
            if(tag.checked && (tag.value == 'sku') ){
                check_sku = true;
            }
            if(tag.checked && (tag.value == 'customer_email')){
                check_customer = true;
            }
            if(tag.checked && (tag.value == 'customer_group_id')){
                check_customer_group = true;
            }
            if(tag.checked && (tag.value =='status')){
                check_order = true;
            }
            if(tag.checked && (tag.value =='order_id')){
                check_order_id = true;
            }
            if(tag.checked && (tag.value =='shipping_method')){
                check_shipping = true;
            }
            if(tag.checked && (tag.value =='payment_method')){
                check_payment = true;
            }
        });
        if(!check_all){
            self.enableAction(list_disable);
            return;
        }
        if(check_sku){
           self.enableAction(list_sku);
        }
        if(check_customer){
            self.enableAction(list_customer);
        }
        if(check_customer_group){
            self.enableAction(list_customer_group);
        }
        if(check_order){
            self.enableAction(list_order_status);
        }
        if(check_order_id){
            self.enableAction(list_order_id);
        }
        if(check_shipping){
            self.enableAction(list_shipping);
        }
        if(check_payment){
            self.enableAction(list_payment);
        }

    },
    disableAction : function(list){
        list.each(function(tag,index){
            var input = $('column-'+tag);
            input.setAttribute( "disabled", true );
            input.removeAttribute( "checked");
            input.next('span').setAttribute('style','text-decoration: line-through');
        });
    },
    enableAction : function(list){
        list.each(function(tag,index){
            var input = $('column-'+tag);
            input.removeAttribute( "disabled");
            input.setAttribute( "checked", true );
            input.next('span').removeAttribute('style');
        });
    },
    cancelEditColumns : function(event){
        myDiv = $$('#report-edit-column')[0];
        myDiv.setAttribute("inactive", "1");
        this.modifiColumn(event);
        return;
    },
    applyEditColumns : function(event){
        myDiv = $$('#report-edit-column')[0];
        myDiv.setAttribute("dofilter", "1");
        this.modifiColumn(event);
        return;
    },
    hidepopup : function(element){
        element.setAttribute("inactive", "");
        element.hide();
    },
    reloadgrid : function(element){
        element.setAttribute("dofilter", "");
        var editcolumnsMetrics ='null:0';
        //$$("input:checked").each(function(tag,index)
        $$("input[name='editcolumns']").each(function(tag,index){
            if(!tag.checked)
                editcolumnsMetrics += ','+tag.value+':0';

            if(tag.checked)
                editcolumnsMetrics += ','+tag.value+':1';
        });

        var editcolumnsDimensions = 'null:0';
        $$("input[name='editcolumnsDimentions']").each(function(tag,index){
            if(!tag.checked)
                editcolumnsDimensions += ','+tag.value+':0';

            if(tag.checked)
                editcolumnsDimensions += ','+tag.value+':1';
        });

        new Ajax.Request( this.editColumnsUrl , {
            parameters: {
                'editcolumnsDimensions': editcolumnsDimensions,
                'editcolumnsMetrics': editcolumnsMetrics,
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

    updateSalesReportData : function (event){
            var self = this;
            new Ajax.Updater(
                {success: 'formLowestSuccess'}, this.urlsUpdateSalesData, {
                    method: 'post',
                    parameters: {
                        product : this.massCorrectProducts,
                        warehouse : this.massCorrectWarehouse,
                    },
                    asynchronous: true,
                    evalScripts: false,
                    onComplete: function (request, json) {
                    },
                    onLoading: function (request, json) {
                        Element.show('loading-mask');
                    },
                    onSuccess: function (transport) {
                        if (transport.responseText) {
                            var json = JSON.parse(transport.responseText);
                            if(json.remain_size && json.remain_size >0){
                                self.changesloadingmask( json.totalSize, json.remain_size);
                                self.continueUpdateSalesReportData(json);
                            }else{
                                self.changesloadingmask(1,0);
                                //self.gridJsObject.doFilter();
                                $('time_notification').innerHTML = json.updated_time;
                                self.changeWarehouse(null);
                            }
                        }
                    }
                }
            );
    },
    continueUpdateSalesReportData : function(json){
        var self = this;
        new Ajax.Updater(
            {success: 'formLowestSuccess'}, this.urlsUpdateSalesData, {
                method: 'post',
                parameters: {
                },
                asynchronous: true,
                evalScripts: false,
                onComplete: function (request, json) {
                },
                onLoading: function (request, json) {
                },
                onSuccess: function (transport) {
                    if (transport.responseText) {
                        var json = JSON.parse(transport.responseText);
                        if(json.remain_size && json.remain_size >0){
                            self.changesloadingmask(json.totalSize,json.remain_size);
                            self.continueUpdateSalesReportData(json);
                        }else{
                            self.changesloadingmask(1,0);
                            $('time_notification').innerHTML = json.updated_time;
                            self.changeWarehouse(null);
                        }
                    }
                }
            }
        );
    },
    changesloadingmask : function (totalSize, remainSize){
        var percent =  Math.ceil(((totalSize - remainSize)/totalSize)*100);
        Element.hide('loading-mask');
        $('loading-mask').innerHTML = '<p class="loader" id="loading_mask_loader"><img src='+this.img+' alt='+this.alt+' /><br/> Updating ... ' +percent+ '%</p>';
        Element.show('loading-mask');
    },
}