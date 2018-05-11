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
/**
 * Is being used by knockout template engine to store template to.
 */
define([
    'ko',
    'uiClass'
], function (ko, Class) {
    'use strict';

    return Class.extend({

        /**
         * Initializes templateName, _data, nodes properties.
         *
         * @param  {template} template - identifier of template
         */
        initialize: function (template) {
            this.templateName = template;
            this._data = {};
            this.nodes = ko.observable([]);
        },

        /**
         * Data setter. If only one arguments passed, returns corresponding value.
         * Else, writes into it.
         * @param  {String} key - key to write to or to read from
         * @param  {*} value
         * @return {*} - if 1 arg provided, returnes _data[key] property
         */
        data: function (key, value) {
            if (arguments.length === 1) {
                return this._data[key];
            }

            this._data[key] = value;
        }
    });
});
