/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'uiComponent',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/model/event-manager'
    ],
    function ($, ko, ViewManager, Component, staffHelper, Event) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/menu/item'
            },
            initialize: function () {
                this._super();
                if (!this.data) {
                    this.data = {};
                }
                if (!this.data.container) {
                    this.data.container = this.data.id + '_container';
                }

                if (this.data.id == 'inventory' && !staffHelper.isHavePermission('Magestore_Webpos::manage_inventory')) {
                    this.data.is_display = 0;
                } else {
                    this.data.is_display = 1;
                }
                if (this.data.id == 'orders_history' && !staffHelper.canShowOrderMenu()) {
                    this.data.is_display = 0;
                } else {
                    this.data.is_display = 1;
                }
            },
            initData: function (object) {
                object.container = (object.container) ? object.container : object.id + '_container';
                this.data = object;
            },
            itemClick: function (item) {
                var self = this;
                var activeElement = '';
                $.each($('.pos_container.active'),function(){
                    activeElement = $(this).attr('id');
                });
                if (item.data.id + '_container' !== activeElement) {
                    var container = ViewManager.getSingleton('view/container');
                    container.init(item.data.container);
                    container.toggleArea();
                    Event.dispatch('show_container_after', item.data.id);
                    Event.dispatch(item.data.id + '_show_container_after', item.data.id);
                }

            },
            afterRender: function(element, item){
                Event.dispatch(item.data.id + '_menu_item_render_after', item.data.id);
            }
        });
    }
);
