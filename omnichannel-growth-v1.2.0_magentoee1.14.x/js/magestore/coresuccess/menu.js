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
 * @package     Magestore_Inventorybarcode
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

var ERPmenuController = new Class.create();

ERPmenuController.prototype = {
    initialize: function (hideMessage, showMessage) {
        this.hideMessage = hideMessage;
        this.showMessage = showMessage;
    },
    showmainmenu: function () {
        $$('div.header')[0].style.display = 'block';
        $$('div.nav-bar')[0].style.display = 'block';
        if($$('div.notification-global').length > 0){
            for(var i=0;i<$$('div.notification-global').length;i++){
                $$('div.notification-global')[i].style.display = 'block';
            }
        }
        $('show_main_menu').innerHTML = ' <a href="javascript:void(0);" class="over" onclick="erpMenu.hidemainmenu()"><i class="fa fa-angle-double-up"></i></a>';
    },
    hidemainmenu: function () {
        $$('div.header')[0].style.display = 'none';
        $$('div.nav-bar')[0].style.display = 'none';
        if($$('div.notification-global').length > 0){
            for(var i=0;i<$$('div.notification-global').length;i++){
                //$$('div.notification-global')[i].style.display = 'none';
            }
        }
        $('show_main_menu').innerHTML = ' <a href="javascript:void(0);" onclick="erpMenu.showmainmenu()"><i class="fa fa-angle-double-down"></i></a>';
    },
    
    showAppBoard: function() {
        $('erp_available_app_dashboard').show();
        $('app_board_control').innerHTML = '<a onclick="erpMenu.hideAppBoard();" href="javascript:void(0);"><i class="fa fa-angle-double-up"></i></a>';
    },
    
    hideAppBoard: function() {
        $('erp_available_app_dashboard').hide();
        $('app_board_control').innerHTML = '<a onclick="erpMenu.showAppBoard();" href="javascript:void(0);"><i class="fa fa-angle-double-down"></i></a>';
    },
    
    showDashboardMenu: function() {
        if($('erp_menu_dashboard').visible()) {
            $('erp_menu_dashboard').hide();
        } else {
            $('erp_menu_dashboard').show();
        }
    }
}