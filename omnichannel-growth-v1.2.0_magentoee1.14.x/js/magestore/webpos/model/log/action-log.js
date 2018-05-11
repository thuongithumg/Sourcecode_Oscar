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
        'model/abstract',
        'model/resource-model/indexed-db/log/action-log',
        'model/collection/log/action-log'
    ],
    function ($, ko, modelAbstract, logIndexedDb, logCollection) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.setResource('', logIndexedDb());
                this.setResourceCollection(logCollection());
            }
        });
    }
);