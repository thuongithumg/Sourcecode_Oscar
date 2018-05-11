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
define([
    'ko',
    'jquery',
    'posComponent',
    'model/container'
], function (ko, $, Component, Container) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/container/default',
            container_id:""
        },
        initialize: function () {
            this._super();
            var self = this;
            this.containerId = ko.pureComputed(function(){
                return self.getContainerId();
            });
        },
        /**
         * Get container id
         * @returns {string}
         */
        getContainerId: function(){
            var self = this;
            var id = self.container_id;
            return Container.getContainerId(id);
        },
        /**
         * Show pos menu
         */
        showMenu: function(){
            Container.showMenu();
        }
    });
});