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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'jquery',
        'ko',
        'posComponent',
        'helper/staff',
        'eventManager',
        'model/container',
        'model/appConfig'
    ],
    function ($, ko, Component, staffHelper, Event, Container, AppConfig) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/menu/item'
            },
            /**
             * Constructor
             */
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
                    this.data.hide_order = 1;
                } else {
                    this.data.is_display = 1;
                    this.data.hide_order = 0;
                }

                if (this.data.id == 'checkout' && !staffHelper.isHavePermission('Magestore_Webpos::create_orders')) {
                    this.data.is_display = 0;
                } else {
                    this.data.is_display = 1;
                }

                if (this.data.id == 'cash_drawer' && window.webposConfig['webpos/general/enable_tills'] == 0) {
                    this.data.is_display = 0;
                } else {
                    this.data.is_display = 1;
                }
            },
            /**
             * Init menu item data
             * @param object
             */
            initData: function (object) {
                object.container = (object.container) ? object.container : object.id + '_container';
                this.data = object;
            },
            /**
             * Action when click menu item
             * @param item
             */
            itemClick: function (item) {
                var containerId = item.data.container;
                var isShowing = Container.toggleArea(containerId);
                if(isShowing){
                    Event.dispatch(AppConfig.EVENT.SHOW_CONTAINER_AFTER, item.data.id);
                    Event.dispatch(item.data.id + '_show_container_after', item.data.id);
                }
            },

            afterRender: function(element, item){
                // console.log(item.data.id);
                Event.dispatch(item.data.id + '_menu_item_render_after', item.data.id);
            }
        });
    }
);
