/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'mage/translate',
        'Magestore_Webpos/js/lib/flipclock'
    ],
    function ($, $t) {
        'use strict';

        var containerId = '.loading-mask';

        return {
            clock: '',
            intervalCountDown: '',

            /**
             * Start full page loader action
             */
            startLoader: function (countdown) {

                this.showPopUp();
                var countdownNumberEl = document.getElementById('countdown-number');


                countdownNumberEl.textContent = countdown;

                this.intervalCountDown = setInterval(function() {
                    countdown = --countdown <= 0 ? 0 : countdown;

                    countdownNumberEl.textContent = countdown;
                }, 1000);
            },

            /**
             * Stop full page loader action
             */
            stopLoader: function () {
                if (this.intervalCountDown !== '') {
                    clearInterval(this.intervalCountDown);
                }
                this.hidePopUp();
            },

            showPopUp: function () {
                var timeoutTerminal = $('#form-timeout-terminal');
                timeoutTerminal.removeClass('fade');
                timeoutTerminal.addClass('fade-in');
                timeoutTerminal.addClass('show');
                $(".wrap-backover").show();
            },

            hidePopUp: function () {
                var timeoutTerminal = $('#form-timeout-terminal');
                timeoutTerminal.removeClass('fade-in');
                timeoutTerminal.removeClass('show');
                timeoutTerminal.addClass('fade');
                $(".wrap-backover").hide();
            }

        };
    }
);
