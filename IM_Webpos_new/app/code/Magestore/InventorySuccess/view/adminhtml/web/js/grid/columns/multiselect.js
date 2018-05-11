/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Ui/js/grid/columns/multiselect'
], function ($, ko, UISelect) {
    'use strict';

    return UISelect.extend({
        defaults: {
            headerTmpl: 'Magestore_InventorySuccess/grid/columns/multiselect',
        },
        toggleSelectAll: function () {
            if(this.isPageSelected(true)){
                this.deselectPage();
            }else{
                this.selectPage();
            }
            return this;
        }
    });
});
