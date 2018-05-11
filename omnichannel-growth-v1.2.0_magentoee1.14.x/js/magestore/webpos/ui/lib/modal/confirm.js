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
    'jquery',
    'underscore',
    'jquery/ui',
    'ui/lib/modal/modal',
    'mage/translate'
], function ($, _) {
    'use strict';

    $.widget('mage.confirm', $.mage.modal, {
        options: {
            modalClass: 'confirm',
            title: '',
            focus: '.action-accept',
            actions: {

                /**
                 * Callback always - called on all actions.
                 */
                always: function () {},

                /**
                 * Callback confirm.
                 */
                confirm: function () {},

                /**
                 * Callback cancel.
                 */
                cancel: function () {}
            },
            buttons: [{
                text: $.mage.__('Cancel'),
                class: 'action-secondary action-dismiss',

                /**
                 * Click handler.
                 */
                click: function (event) {
                    this.closeModal(event);
                }
            }, {
                text: $.mage.__('OK'),
                class: 'action-primary action-accept',

                /**
                 * Click handler.
                 */
                click: function (event) {
                    this.closeModal(event, true);
                }
            }]
        },

        /**
         * Create widget.
         */
        _create: function () {
            this._super();
            this.modal.find(this.options.modalCloseBtn).off().on('click', _.bind(this.closeModal, this));
            this.openModal();
        },

        /**
         * Remove modal window.
         */
        _remove: function () {
            this.modal.remove();
        },

        /**
         * Open modal window.
         */
        openModal: function () {
            return this._super();
        },

        /**
         * Close modal window.
         */
        closeModal: function (event, result) {
            result = result || false;

            if (result) {
                this.options.actions.confirm(event);
            } else {
                this.options.actions.cancel(event);
            }
            this.options.actions.always(event);
            this.element.bind('confirmclosed', _.bind(this._remove, this));

            return this._super();
        }
    });

    return function (config) {
        return $('<div></div>').html(config.content).confirm(config);
    };
});
