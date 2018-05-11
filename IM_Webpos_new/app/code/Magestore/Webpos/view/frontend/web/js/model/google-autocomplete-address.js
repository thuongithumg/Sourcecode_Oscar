/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'ko'
    ],
    function ($, ko) {
        'use strict';

        if(window.webposConfig['webpos/general/suggest_address'] && window.webposConfig['webpos/general/google_api_key']) {
            var google_maps_loaded_def = null;
            if (!google_maps_loaded_def) {
                google_maps_loaded_def = $.Deferred();
                window.onestep_google_maps_loaded = function () {
                    google_maps_loaded_def.resolve(google.maps);
                }
                require(['https://maps.googleapis.com/maps/api/js?key='+window.webposConfig['webpos/general/google_api_key']+'&v=3.exp&libraries=places&language=en'], function () {
                }, function (err) {
                    google_maps_loaded_def.reject();
                });
            }
            google_maps_loaded_def.promise();
        }

        var initAutocomplete = function(formId, type){
            if (window.webposConfig['webpos/general/suggest_address'] && window.webposConfig['webpos/general/google_api_key']) {
                var placeSearch, autocomplete, searchElement;
                var componentForm = {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'short_name',
                    country: 'short_name',
                    postal_code: 'short_name',
                    sublocality_level_1: 'long_name'
                };
                if(type && type == 'billing'){
                    searchElement = document.querySelectorAll("#"+formId+" input[name='street1']")[0];
                }else{
                    searchElement = document.querySelectorAll("div[name='shippingAddress.street.0'] input[name='street1']")[0];
                }
                if(!searchElement){
                    return false;
                }

                autocomplete = new google.maps.places.Autocomplete(
                    (searchElement),
                    {types: ['geocode']}
                );
                var geolocate = function() {

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var geolocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                            var circle = new google.maps.Circle({
                                center: geolocation,
                                radius: position.coords.accuracy
                            });
                            autocomplete.setBounds(circle.getBounds());
                        });
                    }
                };
                if(searchElement){
                    searchElement.onfocus = geolocate();
                }
                var fillInAddress = function(){
                    var place = autocomplete.getPlace();
                    var info = exportLocationInfo(place);
                    if(type && type == 'billing'){
                        fillAddressBillingForm(info);
                    }else{
                        fillAddressForm(info);
                    }
                };

                var fillAddressForm = function (locationInfo) {
                    var $street = $('#'+formId).find('[name$="street1"]');
                    if (locationInfo.street.street1) {
                        $street.eq(0).val(locationInfo.street.street1);
                        $street.trigger('change');
                    }
                    $street.eq(1).val(locationInfo.street.street2);
                    var needReloadShipping = false;
                    var triggerElement = false;
                    if(locationInfo.country_id){
                        $('#'+formId).find('[name$="country_id"]').val(locationInfo.country_id);
                        needReloadShipping = true;
                        triggerElement = $('#'+formId).find('[name$="country_id"]');
                        triggerElement.trigger('change');
                    }
                    if(locationInfo.region){
                        $('#'+formId).find('[name$="region"]').val(locationInfo.region).trigger('change');
                    }
                    if(locationInfo.region_id){
                        $('#'+formId).find('[name$="region_id"]').find('*[data-title="' + locationInfo.region_id + '"]').prop('selected', true);
                        needReloadShipping = true;
                        triggerElement = $('#'+formId).find('[name$="region_id"]');
                        triggerElement.trigger('change');
                    }
                    if(locationInfo.city){
                        $('#'+formId).find('[name$="city"]').val(locationInfo.city).trigger('change');
                    }
                    if(locationInfo.postcode){
                        $('#'+formId).find('[name$="postcode"]').val(locationInfo.postcode);
                        needReloadShipping = true;
                        triggerElement = $('#'+formId).find('[name$="postcode"]');
                    }
                    if(needReloadShipping == true && triggerElement != false){
                        triggerElement.trigger('change');
                    }
                }
                var fillAddressBillingForm = function (locationInfo) {
                    var $street = $('#'+formId).find('[name$="street1"]');
                    if (locationInfo.street.street1) {
                        $street.eq(0).val(locationInfo.street.street1);
                        $street.trigger('change');
                    }
                    $street.eq(1).val(locationInfo.street.street2);
                    if(locationInfo.country_id){
                        $('#'+formId).find('[name$="country_id"]').val(locationInfo.country_id).trigger('change');
                    }
                    if(locationInfo.region){
                        $('#'+formId).find('[name$="region"]').val(locationInfo.region).trigger('change');
                    }
                    if(locationInfo.region_id){
                        $('#'+formId).find('[name$="region_id"]').find('*[data-title="' + locationInfo.region_id + '"]').prop('selected', true).trigger('change');
                        $('#'+formId).find('[name$="region_id"]').trigger('change');
                    }
                    if(locationInfo.city){
                        $('#'+formId).find('[name$="city"]').val(locationInfo.city).trigger('change');
                    }
                    if(locationInfo.postcode){
                        $('#'+formId).find('[name$="zipcode"]').val(locationInfo.postcode).trigger('change');
                    }
                }
                var exportLocationInfo = function (place) {
                    var street, city, region_id, region, country, postcode, sublocality;

                    for (var i = 0; i < place.address_components.length; i++) {
                        var addressType = place.address_components[i].types[0];
                        if (componentForm[addressType]) {
                            if (addressType == 'street_number') {
                                if (street)
                                    street += ' ' + place.address_components[i][componentForm['street_number']];
                                else
                                    street = place.address_components[i][componentForm['street_number']];
                            }
                            if (addressType == 'route') {
                                if (street)
                                    street += ' ' + place.address_components[i][componentForm['route']];
                                else
                                    street = place.address_components[i][componentForm['route']];
                            }
                            if (addressType == 'locality')
                                city = place.address_components[i][componentForm['locality']];
                            if (addressType == 'administrative_area_level_1') {
                                region_id = place.address_components[i]['long_name'];
                                region = place.address_components[i]['long_name'];
                            }
                            if (addressType == 'country')
                                country = place.address_components[i][componentForm['country']];
                            if (addressType == 'postal_code')
                                postcode = place.address_components[i][componentForm['postal_code']];

                            if (addressType == 'sublocality_level_1')
                                sublocality = place.address_components[i][componentForm['sublocality_level_1']];
                        }
                    }

                    return {
                        street: {
                            street1: street,
                            street2: sublocality,
                        },
                        city: city,
                        region_id: region_id,
                        region: region,
                        country_id: country,
                        postcode: postcode
                    }
                }
                autocomplete.addListener('place_changed', fillInAddress);
            }

        };




        return {

            init:initAutocomplete
        };
    }
);
