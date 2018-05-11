/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by Luna on 6/7/2016.
 */
/**
 *
 * Search in header
 */
define(
    [
        'jquery',
        'Magestore_Webpos/js/menu'
    ],
    function ($j) {
        "use strict";
        $j(document).ready(function () {
            window.addEventListener("orientationchange", function() {
                if (navigator.userAgent.match(/(iPhone|iPod|iPad)/i)) {
                    document.documentElement.innerHTML = document.documentElement.innerHTML;
                }
            }, false);

            var inputsearch = $j('#search-header'); // input text field
            var inputsearch2 = $j('#search-customer'); // input text field
            var buttonClear = $j('.o-header .remove-text');
            var buttonClear2 = $j('#popup-change-customer .remove-text');
            $j(inputsearch).focus(function () {
                if ($j.trim($j(inputsearch).val()) != 'Search by SKU, name and Barcode') {
                    $j(buttonClear).show();
                }
                else {
                    $j(buttonClear).hide();
                }
            });
            $j(buttonClear).click(function () {
                $j(inputsearch).val("");
                $j(buttonClear).hide();
            });
            $j(inputsearch2).focus(function () {
                if ($j.trim($j(inputsearch2).val()) != 'Search by name/ email/ phone') {
                    $j(buttonClear2).show();
                }
                else {
                    $j(buttonClear2).hide();
                }
            });
            $j(buttonClear2).click(function () {
                $j(inputsearch2).val("");
                $j(buttonClear2).hide();
            });

            /** Left Menu  */
            var pushLeft = new Menu({
                wrapper: '#o-wrapper',
                type: 'push-left',
                menuOpenerClass: '.c-button',
                maskId: '#c-mask'
            });

            var pushLeftBtn = document.querySelector('#c-button--push-left');
            pushLeftBtn.addEventListener('click', function (e) {
                e.preventDefault;
                pushLeft.open();
            });

            // setTimeout(function(){
            //     //Style for slide of Category
            //     //$j("#list-cat-header").owlCarousel({
            //     //    items: 7,
            //     //    itemsDesktop: [1000, 7],
            //     //    itemsDesktopSmall: [900, 7],
            //     //    itemsTablet: [600, 5],
            //     //    itemsMobile: false,
            //     //    navigation: true,
            //     //    navigationText: ["", ""]
            //     //});
            //     $j('div#all-categories .item').on('click', function (event) {
            //         var $this = $j(this);
            //         $j('div#all-categories .item').removeClass('clicked');
            //         $this.addClass('clicked');
            //
            //     });
            // }, 10000);

            //Style for Images of Product in detail popup
            /*
             $j("#product-img-slise").owlCarousel({
             items: 1,
             itemsDesktop: [1000, 1],
             itemsDesktopSmall: [900, 1],
             itemsTablet: [600, 1],
             itemsMobile: false,
             navigation: true,
             pagination:true,
             navigationText: ["", ""]
             });
             */
            // style for Add note order
            // $j("[data-toggle=popover]").popover({
            //     html: true,
            //     content: function () {
            //         var content = $j(this).attr("data-popover-content");
            //         return $j(content).children(".popover-body").html();
            //     },
            //     title: function () {
            //         var title = $j(this).attr("data-popover-content");
            //         return $j(title).children(".popover-heading").html();
            //     }
            // });
            // $j(funiosCheckbox

            // Setup height for list product to scroll
            $j('.catalog-header').click(function () {
                if ($j('#all-categories').hasClass('in')) {
                    $j('.ms-webpos .main-content').css('height', 'calc(100vh - 50px)');
                    $j('.ms-webpos .main-content .wrap-list-product').css('height', 'calc(100vh - 135px)');
                }
                else {
                    $j('.ms-webpos .main-content').css('height', 'calc(100vh - 196px)');
                    $j('.ms-webpos .main-content .wrap-list-product').css('height', 'calc(100vh - 253px)');
                }
            });

            //Style for Switch in From add new customer
            // $j(".ios").iosCheckbox();

            //Style for popup edit product
            $j('.main-item-order ul li.product-item .product-img, .main-item-order ul li.product-item .product-info, .main-item-order ul li.product-item .price-box ').click(function (event) {
                /*var pleft = event.pageX - 355;*/
                var ptop = event.pageY - 30;
                var heightvp = $j('body').height();
                var subheight = heightvp - ptop;

                if (subheight > 422) {
                    $j("#popup-edit-product").css({display: "block", position: "absolute", top: ptop + 'px'});
                    $j("#popup-edit-product .arrow").css({top: '24px'});
                } else {
                    var disheight = 422 - subheight;
                    var lasttop = ptop - disheight;
                    var aftertop = 24 + disheight;
                    $j("#popup-edit-product").css({display: "block", position: "absolute", top: lasttop + 'px'});
                    $j("#popup-edit-product .arrow").css({top: aftertop + 'px'});
                }
                $j(".wrap-backover").show();
            });
            $j('.actions-customer .add-customer').click(function (event) {
                var ptop = event.pageY - 30;
                $j("#popup-change-customer").css({display: "block", position: "absolute", top: ptop + 'px'});
                $j(".wrap-backover").show();
            });

            $j('.list-product-footer .custom-sale').click(function (event) {

                var ptop = event.pageY - 130;
                $j("#popup-custom-sale").css({display: "block", position: "absolute"});
                $j(".wrap-backover").show();
            });

            $j('#form-add-customer .shipping-title .icon-iconPOS-add-discount, #form-add-customer .billing-title .icon-iconPOS-add-discount').click(function(){
                $j("#form-add-customer").modal('hide');
            });

            $j('.wrap-backover').click(function () {
                $j("#popup-edit-product").hide();
                $j("#popup-change-customer").hide();
                $j("#webpos_cart_discountpopup").hide();
                $j("#popup-custom-sale").hide();
                $j(".wrap-backover").hide();
                $j("#popup-product-detail").hide();
                $j("#popup-custom-sale").addClass("fade");
                $j("#popup-custom-sale").removeClass("show");
                $j("#popup-custom-sale").removeClass("fade-in");
                $j('.notification-bell').show();
                if($j('#checkout_container').hasClass('showMenu')){
                    $j('#c-button--push-left').show();
                }else{
                    $j('#c-button--push-left').hide();
                }
            });


            $j('#btn-add-new-customer').click(function () {
                $j("#popup-change-customer").hide();
                $j(".wrap-backover").hide();
            });
            $j('.panel-collapse .edit').click(function () {
                $j("#form-add-customer").modal('hide');
            });

            $j('.c-menu__link').click(function () {
                $j('body').removeClass('has-active-menu');
                $j('.o-wrapper').removeClass('has-push-left');
            });

            //$j('.modal').on('shown.bs.modal', function (e) {
            //    $j('.modal-backdrop').remove();
            //    $j(document.createElement('div'))
            //        .addClass('modal-backdrop in')
            //        .appendTo('#checkout_container');
            //})

            $j('.modal').on('shown.bs.modal', function (e) {
                $j('.modal-backdrop').remove();
                $j(document.createElement('div')).addClass('modal-backdrop in').appendTo('#checkout_container');
            });

            $j('#orders_history_container .modal').on('shown.bs.modal', function (e) {
                $j('.modal-backdrop').remove();
                $j(document.createElement('div')).addClass('modal-backdrop in').appendTo('#orders_history_container .wrap-order');
            });
            $j('.modal').on('hide.bs.modal', function (e) {
                $j('.modal-backdrop').remove();
            });

            /* open payments popup */
            // $j('.checkout-footer .add-payment').click(function (event) {
            //     var ptop = event.pageY - 130;
            //     $j("#add-more-payment").addClass('fade-in');
            //
            //     $j(".wrap-backover").show();
            // });

            /* close popup */
            $j('.wrap-backover').click(function () {
                $j(".popup-for-right").hide();
                $j(".popup-for-right").removeClass('fade-in');
                $j(".wrap-backover").hide();
            });


            // Prevent events from getting pass .popup

            // $j(".notification-bell").click(function(e){
            //     e.stopPropagation();
            //     $j(".notification-info").show();
            // });
            // If an event gets to the body
        });
        
        window.webposBackoverClicked = function(){
            $j("#popup-edit-product").hide();
            $j("#popup-change-customer").hide();
            $j("#webpos_cart_discountpopup").hide();
            $j("#popup-custom-sale").hide();
            $j(".wrap-backover").hide();
            $j("#popup-product-detail").hide();
            $j("#popup-custom-sale").addClass("fade");
            $j("#popup-custom-sale").removeClass("show");
            $j("#popup-custom-sale").removeClass("fade-in");
            $j('.notification-bell').show();
            if($j('#checkout_container').hasClass('showMenu')){
                $j('#c-button--push-left').show();
                $j('#c-button--push-left').removeClass('hide');
            }else{
                $j('#c-button--push-left').hide();
                $j('#c-button--push-left').addClass('hide');
            }
        }
    }
);
