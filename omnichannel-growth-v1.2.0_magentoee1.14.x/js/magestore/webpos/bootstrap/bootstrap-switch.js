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