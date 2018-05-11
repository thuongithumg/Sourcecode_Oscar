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
    'ui/lib/knockout/template/renderer'
], function (ko, $, renderer) {
    'use strict';

    /**
     * 'Focus' event handler.
     *
     * @param {EventObject} e
     */
    function onFocus(e) {
        e.target.select();
    }

    ko.bindingHandlers.autoselect = {

        /**
         * Adds event handler which automatically
         * selects inputs' element text when field gets focused.
         */
        init: function (element, valueAccessor) {
            var enabled = ko.unwrap(valueAccessor());

            if (enabled !== false) {
                $(element).on('focus', onFocus);
            }
        }
    };

    renderer.addAttribute('autoselect');
});
