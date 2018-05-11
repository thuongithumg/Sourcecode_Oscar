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
            'ui/components/base/grid/collection-grid',
        ],
        function ($, ko, colGrid) {
            "use strict";

            return colGrid.extend({
                defaults: {
                    template: 'ui/base/grid/cell-grid',
                },                
                initialize: function () {
                    this._super();
                },
            });
        }
);