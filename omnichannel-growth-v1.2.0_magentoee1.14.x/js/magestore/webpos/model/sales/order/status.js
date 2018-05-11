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
        'ko',
        'helper/general'
    ], function (ko, Helper) {
    'use strict';

    return {
        getStatusObject: function(){
            var status = [
                {statusClass: 'pending', statusTitle: 'Pending', statusLabel: 'Pending'},
                {statusClass: 'pending_payment', statusTitle: 'Pending Payment', statusLabel: 'Pending Payment'},
                {statusClass: 'processing', statusTitle: 'Processing', statusLabel: 'Processing'},
                {statusClass: 'complete', statusTitle: 'Complete', statusLabel: 'Complete'},
                {statusClass: 'canceled', statusTitle: 'Canceled', statusLabel: 'Cancelled'},
                {statusClass: 'closed', statusTitle: 'Closed', statusLabel: 'Closed'},
                {statusClass: 'notsync', statusTitle: 'Not Sync', statusLabel: 'Not Sync'}
            ];
            if(Helper.isStorePickupEnable()){
                status.push({statusClass: 'store_pickup', statusTitle: 'Pickup At Store', statusLabel: 'Pickup At Store'});
            }
            return status;
        },

        getStatusObjectView: function(){
            var status = [
                {statusClass: 'pending', statusTitle: 'Pending', statusLabel: 'Pending'},
                {statusClass: 'pending_payment', statusTitle: 'Pending Payment', statusLabel: 'Pending Payment'},
                {statusClass: 'processing', statusTitle: 'Processing', statusLabel: 'Processing'},
                {statusClass: 'complete', statusTitle: 'Complete', statusLabel: 'Complete'},
                {statusClass: 'canceled', statusTitle: 'Canceled', statusLabel: 'Cancelled'},
                {statusClass: 'closed', statusTitle: 'Closed', statusLabel: 'Closed'},
                {statusClass: 'notsync', statusTitle: 'Not Sync', statusLabel: 'Not Sync'},
                {statusClass: 'holded', statusTitle: 'On Hold', statusLabel: 'On Hold'},
                {statusClass: 'onhold', statusTitle: 'On Hold', statusLabel: 'On Hold'}
            ];
            if(Helper.isStorePickupEnable()){
                status.push({statusClass: 'store_pickup', statusTitle: 'Pickup At Store', statusLabel: 'Pickup At Store'});
            }
            return status;
        },

        getStatusArray: function(){
            var status = ['pending','processing','complete','canceled','closed','notsync', 'holded'];
            if(Helper.isStorePickupEnable()){
                status.push('store_pickup');
            }
            return status;
        }
    }
});
