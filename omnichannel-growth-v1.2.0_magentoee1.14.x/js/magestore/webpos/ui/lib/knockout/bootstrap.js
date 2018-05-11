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
/** Loads all available knockout bindings, sets custom template engine, initializes knockout on page */

define([
    'ko',
    'ui/lib/knockout/template/engine',
    // 'knockout-es5',
    'ui/lib/knockout/bindings/bootstrap',
    'domReady'
], function (ko, templateEngine) {
    'use strict';

    ko.uid = 0;
    ko.setTemplateEngine(templateEngine);
    ko.applyBindings();
});
