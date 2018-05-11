/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract'
    ],
    function ($,ko, modelAbstract) {
        "use strict";
        return modelAbstract.extend({
            itemId: ko.observable(),
            type: ko.observable(),
            initialize: function () {
                this._super();
            },
            setItem: function (item) {
                this.itemId(item.id);
            },
            getItemId: function () {
                return this.itemId();
            },
            setType: function (type) {
                this.type(type);
            },
            getType: function () {
                return this.type();
            },
            showPopup: function(event){
                $("#popup-product-detail").show();
                $("#popup-product-detail").removeClass("fade");
                $(".wrap-backover").show();

                $(document).click(function(e) {
                    if( e.target.id == 'popup-product-detail') {
                        $("#popup-product-detail").hide();
                        $(".wrap-backover").hide();
                        $('.notification-bell').show();
                        $('#c-button--push-left').show();
                    }
                });
            }
        });
    }
);