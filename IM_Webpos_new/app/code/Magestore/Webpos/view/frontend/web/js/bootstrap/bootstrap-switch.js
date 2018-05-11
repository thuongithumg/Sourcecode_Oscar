/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery'
], function (jQuery) {
    !function (e) {
        e.fn.extend({
            iosCheckbox: function () {
                "true" !== e(this).attr("data-ios-checkbox") && (e(this).attr("data-ios-checkbox", "true"), e(this).each(function () {
                    var c = e(this), s = jQuery("<div>", {"class": "ios-ui-select"}).append(jQuery("<div>", {"class": "inner"}));
                    c.is(":checked") && s.addClass("checked"), c.hide().after(s), s.click(function () {
                        if (!c[0].disabled) {
                            s.toggleClass("checked"), s.hasClass("checked") ? c.prop("checked", !0) : c.prop("checked", !1);
                            c.trigger('switchchange');
                            c.trigger('change');
                        }
                    })
                }))
            }
        })
    }(jQuery);
});