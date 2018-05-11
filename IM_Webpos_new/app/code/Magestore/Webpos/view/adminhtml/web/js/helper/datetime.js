/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
], function ($, priceUtils, accounting) {
    'use strict';


    return {
        getMonthShortText: getMonthShortText,
        getWeekDay: getWeekDay,
        getTimeOfDay: getTimeOfDay,
        getFullDate: getFullDate,
        getFullDatetime: getFullDatetime,
        getSqlDatetime: getSqlDatetime,
        getTime: getTime,
        getDate: getFullDate,
        getBaseCurrentTime: getBaseCurrentTime,
        getBaseSqlDatetime: getBaseSqlDatetime,
        formatDate: formatDate,
        twoDigits: twoDigits,
        getBaseTime: getBaseTime,
        toCurrentTime: toCurrentTime,
        stringToCurrentTime:stringToCurrentTime
    };

    /**
     * return short form of month: Jan
     * @param dateString
     * @returns {*}
     */
    function getMonthShortText(dateString) {
        var monthText = [];
        monthText[1] = "Jan";
        monthText[2] = "Feb";
        monthText[3] = "Mar";
        monthText[4] = "Apr";
        monthText[5] = "May";
        monthText[6] = "Jun";
        monthText[7] = "Jul";
        monthText[8] = "Aug";
        monthText[9] = "Sept";
        monthText[10] = "Oct";
        monthText[11] = "Nov";
        monthText[12] = "Dec";

        var date ="";
        if(!dateString){
            date = new Date();
        }
        else{
            if (typeof dateString === 'string') {
                date = new Date(dateString.replace(/-/g, "/"));
            } else {
                date = reFormatDateString(dateString);
            }

        }
        
        return monthText[date.getMonth()+1];
    }

    /**
     * return day in month: 23
     * @param dateString
     * @returns {number}
     */
    function getDay(dateString) {
        var date ="";
        if(!dateString){
            date = new Date();
        }
        else{
            date = reFormatDateString(dateString);
        }
        return date.getDate();
    }

    function reFormatDateString(dateString) {
        var date = '';
        if (typeof dateString === 'string') {
            date = new Date(dateString.split(' ').join('T'))
        } else {
            date = new Date(dateString);
        }
        return date;
    }

    /**
     * return text day in week: Monday
     * @param dateString
     * @returns {*}
     */
    function getWeekDay(dateString) {


        var date ="";
        if(!dateString){
            date = new Date();
        }
        else{
            date = reFormatDateString(dateString);
        }

        var weekDay = [];
        weekDay[1] = "Monday";
        weekDay[2] = "Tuesday";
        weekDay[3] = "Wednesday";
        weekDay[4] = "Thursday";
        weekDay[5] = "Friday";
        weekDay[6] = "Saturday";
        weekDay[0] = "Sunday";

        return weekDay[date.getDay()];
    }

    /**
     * return a string with format: 15:30PM
     * @param dateString
     * @returns {*}
     */
    function getTimeOfDay(dateString) {

        var date ="";
        if(!dateString){
            date = new Date();
        }
        else{
            date = reFormatDateString(dateString);
        }
        if (date.toDateString() == 'Invalid Date')
        {
            return false;
        }
        var hour = date.getHours();
        var minute = date.getMinutes();
        var beforeMidday = " AM";


        if (hour >12){
            beforeMidday = " PM";
        }


        if (minute < 10) {
            minute = "0" + minute;
        }

        if (hour < 10) {
            hour = "0" + hour;
        }

        var result = hour + ":" + minute + beforeMidday;


        return result;
    }

    /**
     * return a date with format: Thursday 4 May, 2016
     *
     * @param dateString
     * @returns {string}
     */
    function getFullDate(dateString) {
        var date ="";
        if(!dateString){
            date = new Date();
        }
        else{
            date = reFormatDateString(dateString);
        }

        var result = getWeekDay(dateString) + " " + getDay(dateString) + " " + getMonthShortText(dateString) + ", " + date.getFullYear();
       
        return result;
    }

    /**
     * return a date time with format: Thursday 4 May, 2016 15:26PM
     * @param dateString
     * @returns {string}
     */
    function getFullDatetime(dateString) {
        return getFullDate(dateString) + " " + getTimeOfDay(dateString);
    }
    
    /**
     * return a date time with format: 2016 15:26PM
     * @param dateString
     * @returns {string}
     */
    function getTime(dateString) {
        return getTimeOfDay(dateString);
    }

    /**
     * return a string of datetime with sql format:
     * 2016-06-22 23:30:52
     * @param dateString
     * @returns {string}
     */
    function getSqlDatetime(dateString) {

        var date ="";
        if(!dateString){
            date = new Date();
        }
        else{
            date = reFormatDateString(dateString);
        }

        var month = date.getMonth() + 1;
        if (month < 10) {
            month = "0" + month;
        }

        var day = date.getDate();
        if (day < 10) {
            day = "0" + day;
        }

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }
        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        return date.getFullYear() + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
    }
    
    function getBaseCurrentTime(){
        var currentTime = $.now();
        var date = new Date(currentTime);
        var diff = date.getTimezoneOffset();
        currentTime = currentTime + diff*60000;
        return currentTime;
    }
    
    function getBaseTime(time){
        var date = new Date(time);
        var diff = date.getTimezoneOffset();
        time = date.getTime() + diff*60000;
        return time;
    }
    
    function toCurrentTime(time){
        var date = new Date(time);
        var diff = date.getTimezoneOffset();
        time = time - diff*60000;
        return time;
    }
    
    function getBaseSqlDatetime(currentTime){
        if(!currentTime){
            currentTime = getBaseCurrentTime();
        }else{
            currentTime = getBaseTime(currentTime);
        }
        return getSqlDatetime(currentTime);
    }

    /* Format Data*/
    function formatDate(dateTime) {
        return dateTime.getFullYear() + "-" + twoDigits(1 + dateTime.getMonth()) + "-" +
            twoDigits(dateTime.getDate()) + " " + twoDigits(dateTime.getHours()) + ":" +
            twoDigits(dateTime.getMinutes()) + ":" + twoDigits(dateTime.getSeconds());
    }
    /* Format Two Digits*/
    function twoDigits(n) {
        return n > 9 ? "" + n: "0" + n;
    }

    function stringToCurrentTime(dateString){
        var time = new Date(dateString);
        var currentTime = toCurrentTime(time.getTime());
        return currentTime;
    }
});
