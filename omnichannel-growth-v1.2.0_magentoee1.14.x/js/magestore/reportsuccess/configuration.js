/**
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
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
window.onload = function () {
    setCustomObserver();
};

function setCustomObserver() {
    /* inventoryplus_barcode_use_multiple_barcode */
    if($('reportsuccess_general_default_viewreport_apply_time')) {
        var old_value = $('reportsuccess_general_default_viewreport_apply_time').value;
        $('reportsuccess_general_default_viewreport_apply_time').setAttribute('old_value', old_value);
        $('reportsuccess_general_default_viewreport_apply_time').observe('change', reportsuccess_general_default_viewreport_apply_time);
    }
}

function reportsuccess_general_default_viewreport_apply_time(event) {
    var element = null;
    try {
        element = event.element();
    }catch(e) {

    }
    if(!element) {
        element = event;
    }
    var old_value = element.getAttribute('old_value');
    var r = confirm("This action will reset the history data. Do you really want to change this option?");
    if (r == true) {
    } else {
        element.value = old_value;
        return false;
    }
}
