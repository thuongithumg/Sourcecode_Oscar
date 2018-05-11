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
        'posComponent'
    ],
    function ($, ko, Component) {
        "use strict";
        return Component.extend({
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
                template: 'ui/notification/list'
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
