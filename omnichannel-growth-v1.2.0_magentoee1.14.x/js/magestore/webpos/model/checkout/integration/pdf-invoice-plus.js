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
        'helper/general'
    ],
    function ($, ko, Helper) {
        "use strict";
        var PdfInvoicePlusModel = {
            MODULE_CODE:'os_pdf_invoice_plus',
            initialize: function () {
                return this;
            },
            getPrintUrlTemplate: function(orderId){
                var url = '';
                var templateCode = Helper.getPdfInvoiceTemplate();
                var urls = Helper.getPluginConfig('os_pdf_invoice_plus', 'print_urls');
                if(urls && (typeof urls[templateCode] != 'undefined') && orderId){
                    url = urls[templateCode];
                    url = url.replace('entity_id_here', orderId);
                }
                return url;
            },
            startPrint: function(orderId) {
                var self = this;
                var url = self.getPrintUrlTemplate(orderId);
                if(url){
                    var tempForm = document.createElement('form');
                    tempForm.style.display = 'none';
                    tempForm.enctype = 'application/x-www-form-urlencoded';
                    tempForm.method = 'POST';
                    document.body.appendChild(tempForm);
                    tempForm.action = url;
                    tempForm.target = 'webpos-external-iframe';
                    tempForm.setAttribute('target', 'webpos-external-iframe');
                    tempForm.submit();
                }
            }
        };
        return PdfInvoicePlusModel.initialize();
    }
);