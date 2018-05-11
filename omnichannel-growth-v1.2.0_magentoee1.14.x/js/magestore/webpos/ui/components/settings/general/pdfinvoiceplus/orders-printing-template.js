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
        'ui/components/settings/general/element/select',
        'helper/general',
        'jquery',
        'ko'
    ],
    function (Select, Helper, $, ko) {
        "use strict";

        return Select.extend({
            defaults: {
                elementName: 'os_pdf_invoice_plus.pdf_orders_printing_template',
                configPath: 'os_pdf_invoice_plus/pdf_orders_printing_template',
                optionsArray: ko.observableArray([]),
            },
            initialize: function () {
                this._super();
                var self = this;
                var templates = [{value: 0, text: Helper.__('POS Default Receipt')}];
                if(Helper.isPdfInvoicePlusEnable()){
                    var pdfTemplates = Helper.getPluginConfig('os_pdf_invoice_plus', 'template_names');
                    if(pdfTemplates){
                        $.each(pdfTemplates, function(template, name){
                            templates.push({
                                value: template, text: Helper.__(name)
                            });
                        });
                    }
                }
                self.optionsArray(templates);
            },
        });
    }
);