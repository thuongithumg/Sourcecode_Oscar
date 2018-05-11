/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function ($, ko, uiComponent) {
        "use strict";
        return uiComponent.extend({
            /* Show notification popup or not*/
            isShowNotification: ko.observable(false),
            /* Observable array notification*/
            notificationList: ko.observableArray([]),
            /* Initialize*/
            initialize: function () {
                this._super();
                var self = this;
                $("body").click(function(){
                    self.isShowNotification(false);
                });
                $(".notification-info").click(function(e){
                    e.stopPropagation();
                    self.isShowNotification(true);
                });
                self.notificationNumber = ko.pureComputed(function(){
                    var count = 0;
                    $.each(self.notificationList(), function (index, value) {
                        if (value.notRead == true) {
                            count++;
                        }
                    });
                    return count;
                });
                self.notificationReverse = ko.pureComputed(function () {
                    return self.notificationList().reverse();
                });

            },
            /* Template for koJS*/
            defaults: {
                template: 'Magestore_Webpos/notification/list'
            },
            /* Toggle notification */
            toggleInfo: function (data,event) {
                event.stopPropagation();
                if (this.isShowNotification() == false && this.notificationList().length > 0) {
                    this.isShowNotification(true);
                } else {
                    this.isShowNotification(false);
                }
            },
            /* Mark the notification read or not */
            markRead: function (data, event) {
                var self = this;
                event.stopPropagation();
                var allNotification = self.notificationList();
                $.each(allNotification, function (index, value) {
                   if (value.id == data.id) {
                       value.notRead = false;
                       allNotification[index] = value;
                   }
                });
                self.notificationList([]);
                self.notificationList(allNotification);
            },
            /* Add notification log*/
            addLog: function (label) {
                var data = {
                    id: Date.now(),
                    notRead: true,
                    label: label,
                    date: this.formatDate(new Date())
                };
                this.notificationList.push(data);
            },
            /* Clear all the notification*/
            clearLog: function () {
                this.notificationList([]);
            },
            /* Delete one notification*/
            delete: function (data) {
                this.notificationList.remove(data);
            },
            /* Format Data*/
            formatDate : function(dateTime){
                return dateTime.getFullYear() + "-" + this.twoDigits(1 + dateTime.getMonth()) + "-" + this.twoDigits(dateTime.getDate()) + " " + this.twoDigits(dateTime.getHours()) + ":" + this.twoDigits(dateTime.getMinutes()) + ":" + this.twoDigits(dateTime.getSeconds());
            },
            /* Format Two Digits*/
            twoDigits : function(n){
                return n > 9 ? "" + n: "0" + n;
            }
        });
    }
);
