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
    'underscore',
    'mageUtils'
], function (_, utils) {
    'use strict';

    var store = {};

    function flatten(data) {
        var extender = data.extends || [],
            result = {};

        extender = utils.stringToArray(extender);

        extender.push(data);

        extender.forEach(function (item) {
            if (_.isString(item)) {
                item = store[item] || {};
            }

            utils.extend(result, item);
        });

        delete result.extends;

        return result;
    }

    return {
        set: function (types) {
            types = types || {};

            utils.extend(store, types);

            _.each(types, function (data, type) {
                store[type] = flatten(data);
            });
        },

        get: function (type) {
            return store[type] || {};
        }
    };
});
