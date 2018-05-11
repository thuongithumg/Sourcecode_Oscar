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
        'eventManager',
        'ui/components/layout'
    ],
    function ($, eventManager, ViewManager) {
        "use strict";

        return {
            /*
             * Update stock data to product list view after mass updated stock-item
             *
             */
            execute: function () {
                eventManager.observer('load_product_by_category', function (event, eventData) {
                    var catagory = ViewManager.getSingleton('ui/components/catalog/catalog-list');
                    var data = eventData.catagory;
                    catagory.clickCat(data);
                    if (eventData.open_category) {
                        $('#all-categories').addClass('in');
                    }
                });
            }
        }
    }
);