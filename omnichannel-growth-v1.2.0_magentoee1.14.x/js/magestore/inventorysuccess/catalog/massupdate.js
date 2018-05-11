

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

var InventorysuccessProductMassupdate = Class.create();
InventorysuccessProductMassupdate.prototype = {
    initialize: function (config) {
        this.config = config;
        this.checkQtyField();
    },
    
    checkQtyField: function() {
        $("inventory_qty").disable();
        $("inventory_qty_checkbox").hide();
        $("inventory_qty_checkbox").next('label').hide();
        $("inventory_qty").up('td').insert('<ul class="messages"><li class="notice-msg"><ul><li><span>'+this.config.notice_edit_qty+'</span></li></ul></li></ul>');
    } 
}