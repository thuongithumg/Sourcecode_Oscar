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
        'model/resource-model/magento-rest/abstract',
    ],
    function ($, onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            createApiUrl: '/webpos/invoices/create',
            interfaceName: 'sales_order_invoice',
            initialize: function () {
                this._super();
            },
            
            save: function(model, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                this.setPush(true);
                this.callRestApi(this.createApiUrl, 'post', {}, model.getPostData(), deferred, this.interfaceName + '_afterSave');
                return deferred;
            }
        });
    }
);