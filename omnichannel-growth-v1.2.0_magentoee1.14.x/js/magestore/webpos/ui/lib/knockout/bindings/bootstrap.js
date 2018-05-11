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

    var renderer = require('ui/lib/knockout/template/renderer');

    return {
        scope:          require('ui/lib/knockout/bindings/scope'),
        mageInit:       require('ui/lib/knockout/bindings/mage-init'),
        aferRender:     require('ui/lib/knockout/bindings/after-render'),
        autoselect:     require('ui/lib/knockout/bindings/autoselect'),
        i18n:     require('ui/lib/knockout/bindings/i18n')
    };
});
