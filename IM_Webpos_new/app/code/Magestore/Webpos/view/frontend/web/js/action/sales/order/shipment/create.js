/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'ko',
        'mage/translate',
        'Magestore_Webpos/js/model/sales/order/shipment',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/action/notification/add-notification'
    ],
    function($, ko, $t, shipment, eventmanager, alertHelper, notification) {
        'use strict';
        return {
            isValid: false,
            orderData: {},
            submitArray: [],
            submitData: {
                "entity":{
                    "orderId": 0,
                    "emailSent": 0,
                    "items": [],
                    "tracks": [],
                    "comments": []
                }
            },
            items: {},
            comment: {},
            track: {},

            execute: function(data, orderData, deferred, parent){
                var self = this;
                this.isValid = false;
                this.orderData = orderData;
                this.submitData = {
                    "entity":{
                        "orderId": this.orderData.entity_id,
                        "emailSent": 0,
                        "items": [],
                        "tracks": [],
                        "comments": []
                    }
                };
                $.each(data, function(index, value){
                    self.submitData = self.bindEmail(self.submitData,value);
                    self.submitData = self.bindItem(self.submitData,value);
                    self.submitData = self.bindTrack(self.submitData,value);
                    self.submitData = self.bindComment(self.submitData,value);
                });
                if(!this.isValid){
                    alertHelper({title:'Error', content: $t('Please choose an item to ship')});
                    return;
                }
                notification($t('The shipment has been created successfully.'), true, 'success', $t('Success'));
                parent.orderData(null);
                parent.display(false);
                if(this.submitData.entity.items.length>0){
                    shipment().setPostData(this.submitData).setMode('online').save();
                    this.saveOrderOffline(this.submitData);
                }                
            },

            saveOrderOffline: function(submitData){
                var self = this;
                if(submitData.entity.items.length>0){
                    $.each(self.orderData.items, function(orderItemIndex, orderItemValue){
                        $.each(submitData.entity.items, function(index, value){
                            if(value.orderItemId == orderItemValue.item_id){
                                orderItemValue.qty_shipped += value.qty;
                            }
                            if(value.orderItemId == orderItemValue.parent_item_id){
                                orderItemValue.qty_shipped += value.qty;
                            }
                        });
                    });
                }
                eventmanager.dispatch('sales_order_shipment_afterSave', {'response': this.orderData});
            },

            bindEmail: function(data, item){
                if(item.name.search('send_email')===0 && parseFloat(item.value)>0){
                    data.entity.emailSent = 1;
                }
                return data;
            },

            bindItem: function(data, item){
                if(item.name.search('items')===0 && parseFloat(item.value)>0){
                    this.isValid = true;
                    this.item = {};
                    item.name = item.name.replace("items[", "");
                    item.name = item.name.replace("]", "");
                    this.item.orderItemId = parseInt(item.name);
                    this.item.qty = parseFloat(item.value);
                    data.entity.items.push(this.item);
                }
                return data;
            },

            bindTrack: function(data, item){
                if(item.name.search('tracking')===0 && item.value!=''){
                    this.track = {};
                    this.track.trackNumber = item.value;
                    data.entity.tracks.push(this.track);
                }
                return data;
            },

            bindComment: function(data, item){
                if(item.name.search('comment_text')===0 && item.value){
                    this.comment.comment = item.value;
                    data.entity.comments.push(this.comment);
                }
                return data;
            },
        }
    }
);
