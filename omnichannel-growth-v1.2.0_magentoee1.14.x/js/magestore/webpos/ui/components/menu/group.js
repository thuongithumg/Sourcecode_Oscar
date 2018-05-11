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
        'posComponent',
        'helper/general'
    ],
    function ($, ko, Component, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/menu/group'
            },
            initialize: function () {
                this._super();
            },
            hasChilds: function(){
                var count_item = 0;
                $.each(this.elems(), function( index, value ) {
                    if(value.data.is_display == 1){
                        count_item++;
                    }
                });
                if(count_item == 0){
                    return false;
                }
                if (this.id === 'session' && (Helper.getOnlineConfig('is_session_required') === '0')) {
                    return false;
                }
                return (this.elems().length > 0)?true:false;
            },
            initData: function (object) {
                if(object.id){
                    this.id = object.id;
                }
                if(object.title){
                    this.title = object.title;
                }
                if(object.sortOrder){
                    this.sortOrder = object.sortOrder;
                }
            }
        });
    }
);