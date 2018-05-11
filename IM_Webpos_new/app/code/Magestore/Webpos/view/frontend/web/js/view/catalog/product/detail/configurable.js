/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/catalog/product/detail-popup'
    ],
    function ($,ko, detailPopup, configurable) {
        "use strict";
        ko.bindingHandlers.getConfigData = {
            update: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
                // This will be called once when the binding is first applied to an element,
                // and again whenever any observables/computeds that are accessed change
                // Update the DOM element based on the supplied values here.
                //detailPopup().setAllData();
            }
        }
        return detailPopup.extend({
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/configurable'
            },
            initialize: function () {
                this._super();
            }
        });
    }
);