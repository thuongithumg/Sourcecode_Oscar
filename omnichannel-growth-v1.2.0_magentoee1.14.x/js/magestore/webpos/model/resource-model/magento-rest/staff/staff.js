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
        'model/resource-model/magento-rest/abstract'
    ],
    function ($,onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            initialize: function () {
                this._super();;
                this.setChangePassWordApiUrlApiUrl('/webpos/staff/changepassword');
            },
            /* Set changePassWord Api Url*/
            setChangePassWordApiUrlApiUrl: function (changePassWord) {
                this.changePassWordApiUrl = changePassWord;
            },
            changePassWord: function(postData, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                if(this.changePassWordApiUrl) {
                    this.callRestApi(
                        this.changePassWordApiUrl,
                        'post',
                        {},
                        postData,
                        deferred
                    );
                }
                return deferred;
            },
        });
    }
);