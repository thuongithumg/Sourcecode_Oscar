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
		'helper/general'
	],
	function (jQuery, Helper) {
		"use strict";
		return {
			swipeParserObj: function (strParse) {
				this.input_trackdata_str = strParse;
				this.account_name = null;
				this.surname = null;
				this.firstname = null;
				this.account = null;
				this.exp_month = null;
				this.exp_year = null;
				this.track1 = null;
				this.track2 = null;
				this.hasTrack1 = false;
				this.hasTrack2 = false;

				var sTrackData = this.input_trackdata_str;
				if (strParse != '') {
					var nHasTrack1 = strParse.indexOf("^");
					var nHasTrack2 = strParse.indexOf("=");

					var bHasTrack1 = this.hasTrack1 = false;
					var bHasTrack2 = this.hasTrack2 = false;
					if (nHasTrack1 > 0) {
						this.hasTrack1 = bHasTrack1 = true;
					}
					if (nHasTrack2 > 0) {
						this.hasTrack2 = bHasTrack2 = true;
					}

					var bTrack1_2 = false;
					var bTrack1 = false;
					var bTrack2 = false;

					if (( bHasTrack1) && ( bHasTrack2)) {
						bTrack1_2 = true;
					}
					if (( bHasTrack1) && (!bHasTrack2)) {
						bTrack1 = true;
					}
					if ((!bHasTrack1) && ( bHasTrack2)) {
						bTrack2 = true;
					}
					var bShowAlert = false;

					if (bTrack1_2) {
						var strCutUpSwipe = '' + strParse + ' ';
						var arrayStrSwipe = new Array(4);
						var arrayStrSwipe = strCutUpSwipe.split("^");
						var sAccountNumber, sName, sShipToName, sMonth, sYear;
						if (arrayStrSwipe.length > 2) {
							this.account = stripAlpha(arrayStrSwipe[0].substring(1, arrayStrSwipe[0].length));
							this.account_name = arrayStrSwipe[1];
							this.exp_month = arrayStrSwipe[2].substring(2, 4);
							this.exp_year = '20' + arrayStrSwipe[2].substring(0, 2);
							if (sTrackData.substring(0, 1) == '%') {
								sTrackData = sTrackData.substring(1, sTrackData.length);
							}
							var track2sentinel = sTrackData.indexOf(";");
							if (track2sentinel != -1) {
								this.track1 = sTrackData.substring(0, track2sentinel);
								this.track2 = sTrackData.substring(track2sentinel);
							}
							var nameDelim = this.account_name.indexOf("/");
							if (nameDelim != -1) {
								this.surname = this.account_name.substring(0, nameDelim);
								this.firstname = this.account_name.substring(nameDelim + 1);
							}
						} else {
							bShowAlert = true;
						}
					}
					if (bTrack1) {
						strCutUpSwipe = '' + strParse + ' ';
						arrayStrSwipe = new Array(4);
						arrayStrSwipe = strCutUpSwipe.split("^");

						var sAccountNumber, sName, sShipToName, sMonth, sYear;

						if (arrayStrSwipe.length > 2) {
							this.account = sAccountNumber = stripAlpha(arrayStrSwipe[0].substring(1, arrayStrSwipe[0].length));
							this.account_name = sName = arrayStrSwipe[1];
							this.exp_month = sMonth = arrayStrSwipe[2].substring(2, 4);
							this.exp_year = sYear = '20' + arrayStrSwipe[2].substring(0, 2);
							if (sTrackData.substring(0, 1) == '%') {
								this.track1 = sTrackData = sTrackData.substring(1, sTrackData.length);
							}
							this.track2 = ';' + sAccountNumber + '=' + sYear.substring(2, 4) + sMonth + '111111111111?';
							sTrackData = sTrackData + this.track2;
							var nameDelim = this.account_name.indexOf("/");
							if (nameDelim != -1) {
								this.surname = this.account_name.substring(0, nameDelim);
								this.firstname = this.account_name.substring(nameDelim + 1);
							}

						} else {
							bShowAlert = true;
						}
					}
					if (bTrack2) {
						var nSeperator = strParse.indexOf("=");
						var sCardNumber = strParse.substring(1, nSeperator);
						sYear = strParse.substr(nSeperator + 1, 2);
						sMonth = strParse.substr(nSeperator + 3, 2);
						this.account = sAccountNumber = stripAlpha(sCardNumber);
						this.exp_month = sMonth = sMonth;
						this.exp_year = sYear = '20' + sYear;
						if (sTrackData.substring(0, 1) == '%') {
							sTrackData = sTrackData.substring(1, sTrackData.length);
						}

					}
					if (((!bTrack1_2) && (!bTrack1) && (!bTrack2)) || (bShowAlert)) {
						//alert('Difficulty Reading Card Information.\n\nPlease Swipe Card Again.');
					}

				}
				this.dump = function () {
					var s = "";
					var sep = "\r"; // line separator
					s += "Name: " + this.account_name + sep;
					s += "Surname: " + this.surname + sep;
					s += "first name: " + this.firstname + sep;
					s += "account: " + this.account + sep;
					s += "exp_month: " + this.exp_month + sep;
					s += "exp_year: " + this.exp_year + sep;
					s += "has track1: " + this.hasTrack1 + sep;
					s += "has track2: " + this.hasTrack2 + sep;
					s += "TRACK 1: " + this.track1 + sep;
					s += "TRACK 2: " + this.track2 + sep;
					s += "Raw Input Str: " + this.input_trackdata_str + sep;

					return s;
				}

				function stripAlpha(sInput) {
					if (sInput == null)    return '';
					return sInput.replace(/[^0-9]/g, '');
				}

				return this;

			},
			trimNumber: function (s) {
				while (s.substr(0, 1) == '0' && s.length > 1) {
					s = s.substr(1, 9999);
				}
				return s;
			},
			parseSwiperData: function (value) {
				if (value.charAt(0) !== '%') {
					return -1;
				}
				var p = this.swipeParserObj(value);
				var result = new Array();
				if (p.hasTrack1) {
					if (p.account_name != null)
						result[0] = p.account_name;
					else
						result[0] = p.surname + ' ' + p.firstname;						
					result[1] = p.account;
					result[2] = p.exp_month;
					result[3] = p.exp_year;
				}
				return result;
			},
			swipeNow: function (method) {
				if (jQuery('input#' + method + '-swiper-data')) {
					jQuery('input#' + method + '-swiper-data').focus();
					jQuery('input#' + method + '-swiper-data').val(' ');
					jQuery('input#' + method + '-swiper-data').select();
				}
			},
			initSwipe: function (method) {
				var self = this;
				jQuery('input#' + method + '-swiper-data').focus(function (event) {
					jQuery('#' + method + '-swiper-status').html('Ready to swipe');
					jQuery('#' + method + '-swiper-status').addClass('active');
					jQuery('#' + method + '-swiper-status').select();
				});
				jQuery('input#' + method + '-swiper-data').blur(function (event) {
					jQuery('#' + method + '-swiper-status').html('Click here to swipe');
					jQuery('#' + method + '-swiper-status').removeClass('active');
				});

				jQuery('div.input-box input').blur(function (event) {
					//swipeNow(method);
				});
				jQuery('#' + method + '-swiper-status').click(function (event) {
					self.swipeNow(method);
				});

				jQuery('input#' + method + '-swiper-data').keyup(function (event) {
					if (event.keyCode == 13) {
						var ccinfo = self.parseSwiperData(jQuery(this).val());
						if (ccinfo === -1) return;
						if (jQuery('input#' + method + '-swiper-data').val().length > 0) {
							jQuery('#loading-mask').show();
							jQuery('input#' + method + '_cc_owner').val('');
							jQuery('input#' + method + '_cc_number').val('');
							jQuery('select#' + method + '_cc_type').val('');
							jQuery('select#' + method + '_cc_exp_month').val('');
							jQuery('select#' + method + '_cc_exp_year').val('');
							jQuery('input#' + method + '_cc_cid').val('');
						}
						if (ccinfo != null && ccinfo.length > 0) {
							jQuery('input#' + method + '_cc_owner').val(ccinfo[0]);
							if (ccinfo[1] == null) {
								Helper.alert({
									priority: 'danger',
									title: Helper.__('Error'),
									message: Helper.__('Card number not detected!')
								});
							} else {
								jQuery('input#' + method + '_cc_number').val(ccinfo[1]);
								jQuery('input#' + method + '_cc_number_label').val(ccinfo[1].replace(/.*(\d{4})$/, "xxxx xxxx xxxx $1"));
								var startdigit = parseInt(ccinfo[1].charAt(0));
								if (startdigit == 3)
									jQuery('select#' + method + '_cc_type').val('AE');
								if (startdigit == 4)
									jQuery('select#' + method + '_cc_type').val('VI');
								if (startdigit == 5)
									jQuery('select#' + method + '_cc_type').val('MC');
								if (startdigit == 6)
									jQuery('select#' + method + '_cc_type').val('DI');
								if (jQuery('select#' + method + '_cc_type').val() == ""){
									Helper.alert({
										priority: 'danger',
										title: Helper.__('Error'),
										message: Helper.__('Card type not detected!')
									});
								}
							}
							if (ccinfo[2] == null) {
								Helper.alert({
									priority: 'danger',
									title: Helper.__('Error'),
									message: Helper.__('Expiration month not detected!')
								});
							} else {
								jQuery('select#' + method + '_cc_exp_month').val(self.trimNumber(ccinfo[2]));
							}
							if (ccinfo[3] == null) {
								Helper.alert({
									priority: 'danger',
									title: Helper.__('Error'),
									message: Helper.__('Expiration year not detected!')
								});
							} else {
								jQuery('select#' + method + '_cc_exp_year').val(ccinfo[3]);
								if (jQuery('select#' + method + '_cc_exp_year').val() == ""){
									Helper.alert({
										priority: 'danger',
										title: Helper.__('Error'),
										message: Helper.__('The card might be expired!')
									});
								}
							}
						} else {
							Helper.alert({
								priority: 'danger',
								title: Helper.__('Error'),
								message: Helper.__('Cannot read the data!')
							});
						}
						jQuery(this).val('');
					}
				});

				self.swipeNow(method);
			}
		};
	}
);
