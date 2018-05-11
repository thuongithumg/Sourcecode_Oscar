/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/giftvoucher/giftvoucher'
    ],
    function ($,ko, Component, giftvoucher) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/giftvoucher/template'
            },
            initialize: function () {
                this._super();
            },
            selectedImage: ko.pureComputed(function () {
                var selectedImage = giftvoucher.selectedImage();
                var domain = window.webposConfig.imageBaseUrl + '/';
                return selectedImage.replace(domain, '');
            }),
            isSelectCustomImage: ko.observable(false),
            selectedTemplate: giftvoucher.selectedTemplate,
            templates: giftvoucher.templates,
            selectedTemplateImage: giftvoucher.selectedTemplateImage,
            images: giftvoucher.images,
            selectImage: function (id, data) {
                giftvoucher.selectImage(id, data);
            },
            selectTemplate: function (template) {
                var templateId = template.giftcard_template_id;
                giftvoucher.selectedTemplateImage(templateId);
                giftvoucher.selectTemplate(template);
                giftvoucher.chooseImage(templateId);

            }
        });
    }
);