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
        'model/abstract',
        'model/resource-model/magento-rest/staff/staff'
    ],
    function ($,modelAbstract, restResource) {
        "use strict";
        return modelAbstract.extend({
            mode: "online",
            initialize: function () {
                this._super();
                this.setResource(restResource(), {});
                this.setResourceCollection({});
            },
            changePassWord: function(postData, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                this.getResource().changePassWord(postData,deferred);
                return deferred;
            }
        });
    }
);