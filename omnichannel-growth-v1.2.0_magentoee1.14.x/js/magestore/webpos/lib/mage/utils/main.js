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
define(function (require) {
    'use strict';

    var utils = {},
        _ = require('underscore');

    return _.extend(
        utils,
        require('lib/mage/utils/arrays'),
        require('lib/mage/utils/compare'),
        require('lib/mage/utils/objects'),
        require('lib/mage/utils/strings'),
        require('lib/mage/utils/template')
    );
});
