

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

var InventorysuccessProduct = Class.create();
InventorysuccessProduct.prototype = {
    initialize: function (config) {
        this.config = config;
        this.moveWarehouseStockForm('product_info_tabs_inventorysuccess_inventory_content');
        this.checkManageStock();
        this.disableQtyField();
        this.observerManageStockField();
        this.observerUseConfigManageStock();
        this.checkConfigurableProduct();
    },
    
    moveWarehouseStockForm: function (warehouseStockTabId) {
        var inventoryForm = $('product_info_tabs_inventory_content');
        var warehouseStockTab = $(warehouseStockTabId);
        inventoryForm.insert(warehouseStockTab.down('.entry-edit'));
    },
    
    checkManageStock: function()
    {
        if(!this.config.manage_stock) {
             this.hideWarehouseStockForm();
        } else {
             this.showWarehouseStockForm();
        }
    },
    
    disableQtyField: function(){
        if($("inventory_qty")) {
            $("inventory_qty").disable();
            if(!this.config.force_edit_permission){
                $("inventory_qty").up().insert('<br/><label for="inventory_qty" class="normal">'+ this.config.notice_edit_qty +'</label>');
            } else {
                var checkbox = new Element('input');
                checkbox.setAttribute( "type", "checkbox" );
                checkbox.setAttribute( "id", "inventory_use_force_edit" );
                checkbox.setAttribute( "name", "product[stock_data][inventory_use_force_edit]" );
                checkbox.setAttribute( "value", "0" );
                checkbox.setAttribute( "class", "checkbox");

                var label = new Element('label');
                label.setAttribute( "for", "inventory_use_force_edit");
                label.setAttribute( "class", "normal");
                label.update(this.config.force_edit_label);                
                $("inventory_qty").up().insert(checkbox);
                $("inventory_qty").up().insert(label);
            }
            Event.observe("inventory_use_force_edit", "click", function(event){
                if(event.target.checked){
                    $("inventory_qty").enable();
                    event.target.setAttribute("value",1);
                }else{
                    $("inventory_qty").disable();
                    event.target.setAttribute("value",0);
                }
            });
        }
    },
    
    observerManageStockField: function() {
        var self = this;
        Event.observe("inventory_manage_stock", "change", function(event){
            var manageStock = $(event.target.id).value;
            if(manageStock == '0') {
                self.hideWarehouseStockForm();
            } else {
                self.showWarehouseStockForm();
            } 
        });
    },
    
    observerUseConfigManageStock: function() {
        var self = this;
        Event.observe("inventory_use_config_manage_stock", "click", function(event){
            var useConfigmanageStock = $(event.target.id).checked;
            var manageStock = $("inventory_manage_stock").value;
            if(useConfigmanageStock && self.config.manage_stock_default) {
                self.showWarehouseStockForm();
            } 
            if(useConfigmanageStock && !self.config.manage_stock_default) {
                self.hideWarehouseStockForm();
            }             
            if(!useConfigmanageStock && manageStock == '1'){
                self.showWarehouseStockForm();
            }
            if(!useConfigmanageStock && manageStock == '0'){
                self.hideWarehouseStockForm();
            }                
            
        });        
    },    
    
    checkConfigurableProduct: function(){
        if($("simple_product_inventory_qty")) {
            var configurableInventoryForm = $("product_info_tabs_inventorysuccess_configurable_inventory_content");
            if(configurableInventoryForm) {
                var simpleQtyInput = $("simple_product_inventory_qty");
                simpleQtyInput.up().insert(configurableInventoryForm.down('#row_simple_product_warehouse_stock').down('td.value'));           
                simpleQtyInput.hide();
            }
        }
    },
    
    showWarehouseStockForm: function() {
        if($("row_warehouse_stock")) {
            $("row_warehouse_stock").up('.entry-edit').show();
        }
    },
    
    hideWarehouseStockForm: function() {
        if($("row_warehouse_stock")) {
            $("row_warehouse_stock").up('.entry-edit').hide();
        }
    }
}