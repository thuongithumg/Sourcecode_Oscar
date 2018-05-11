/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($,ko, Component, staffHelper, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/menu/group'
            },
            initialize: function () {
                this._super();
            },
            hasChilds: function(){
                if (this.id == 'inventory' && !staffHelper.isHavePermission('Magestore_Webpos::manage_inventory')) {
                    return false;
                }
                if (this.id == 'shift' && (Helper.getOnlineConfig('is_session_required') == 0)) {
                    return false;
                }
                return (this.elems().length > 0)?true:false;
            },
            initData: function (object) {
                if(object.id){
                    this.id = object.id;
                }
                if(object.title){
                    this.title = object.title;
                }
                if(object.sortOrder){
                    this.sortOrder = object.sortOrder;
                }
            },


        });
    }
);