<?php
/**
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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher Checkout controller
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_CheckoutController extends Mage_Core_Controller_Front_Action
{

    /**
     * Remove gift codes from Cart page
     */
    public function removegiftAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $code = trim($this->getRequest()->getParam('code'));
        $codes = trim($session->getGiftCodes());
        $success = false;
        if ($code && $codes) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $key => $value) {
                if ($value == $code) {
                    unset($codesArray[$key]);
                    $success = true;
                    $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
                    if (is_array($giftMaxUseAmount) && array_key_exists($code, $giftMaxUseAmount)) {
                        unset($giftMaxUseAmount[$code]);
                        $session->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                    }
                    break;
                }
            }
        }

        if ($success) {
            $codes = implode(',', $codesArray);
            $session->setGiftCodes($codes);
            $session->addSuccess($this->__('Gift Card "%s" has been removed successfully!', 
                Mage::helper('giftvoucher')->getHiddenCode($code)));
        } else {
            $session->addError($this->__('Gift card "%s" not found!', $code));
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * Add gift codes from Cart page
     */
    public function giftcardPostAction()
    {
        $session = Mage::getSingleton('checkout/session');

        if ($session->getQuote()->getCouponCode() 
            && !Mage::helper('giftvoucher')->getGeneralConfig('use_with_coupon')) {
            $session->addNotice(
                Mage::helper('giftvoucher')->__('A coupon code has been used. You cannot apply gift codes or Gift Card credit with the coupon to get discount.'));
        } else {
            $request = $this->getRequest();
            if ($request->isPost()) {
                if ($request->getParam('giftvoucher_credit') 
                    && Mage::helper('giftvoucher')->getGeneralConfig('enablecredit')) {
                    $session->setUseGiftCardCredit(1);
                    $session->setMaxCreditUsed(floatval($request->getParam('credit_amount')));
                } else {
                    $session->setUseGiftCardCredit(0);
                    $session->setMaxCreditUsed(null);
                }
                if ($request->getParam('giftvoucher')) {
                    $session->setUseGiftCard(1);
                    $giftcodesAmount = $request->getParam('giftcodes');
                    if (count($giftcodesAmount)) {
                        $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
                        if (!is_array($giftMaxUseAmount)) {
                            $giftMaxUseAmount = array();
                        }
                        $giftMaxUseAmount = array_merge($giftMaxUseAmount, $giftcodesAmount);
                        $session->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                    }
                    $addcodes = array();
                    if ($request->getParam('existed_giftvoucher_code')) {
                        $addcodes[] = trim($request->getParam('existed_giftvoucher_code'));
                    }
                    if ($request->getParam('giftvoucher_code')) {
                        $addcodes[] = trim($request->getParam('giftvoucher_code'));
                    }
                    if (count($addcodes)) {
                        if (Mage::helper('giftvoucher')->isAvailableToAddCode()) {
                            foreach ($addcodes as $code) {
                                $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
                                $quote = Mage::getSingleton('checkout/session')->getQuote();
                                if (!$giftVoucher->getId() || ($giftVoucher->getSetId() > 0)) {
                                    $codes = Mage::getSingleton('giftvoucher/session')->getCodes();
                                    $codes[] = $code;
                                    Mage::getSingleton('giftvoucher/session')->setCodes(array_unique($codes));
                                    $session->addError($this->__('Gift card "%s" is invalid.', $code));
                                    $max = Mage::helper('giftvoucher')->getGeneralConfig('maximum');
                                    if ($max - count($codes)) {
                                        $session->addError(
                                            $this->__('You have %d time(s) remaining to re-enter your Gift Card code.', 
                                                $max - count($codes)));
                                    }
                                } else if ($giftVoucher->getBaseBalance() > 0 
                                    && $giftVoucher->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE
                                ) {
                                    if (Mage::helper('giftvoucher')->canUseCode($giftVoucher)) {
                                        $flag = false;
                                        foreach ($quote->getAllItems() as $item) {
                                            if ($giftVoucher->getActions()->validate($item)) {
                                                $flag = true;
                                            }
                                        }
                                        $giftVoucher->addToSession($session);
                                        if ($giftVoucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId() 
                                            && $giftVoucher->getRecipientName() && $giftVoucher->getRecipientEmail() 
                                            && $giftVoucher->getCustomerId()
                                        ) {
                                            $session->addNotice(
                                                $this->__('Please note that gift code "%s" has been sent to your friend. When using, both you and your friend will share the same balance in the gift code.', Mage::helper('giftvoucher')->getHiddenCode($code)));
                                        }
                                        $quote->setTotalsCollectedFlag(false)->collectTotals();
                                        if ($flag && $giftVoucher->validate($quote->setQuote($quote))) {
                                            $isGiftVoucher = true;
                                            foreach ($quote->getAllItems() as $item) {
                                                if ($item->getProductType() != 'giftvoucher') {
                                                    $isGiftVoucher = false;
                                                    break;
                                                }
                                            }
                                            if (!$isGiftVoucher) {
                                                $session->addSuccess(
                                                    $this->__('Gift code "%s" has been applied successfully.', 
                                                        Mage::helper('giftvoucher')->getHiddenCode($code)));
                                            } else {
                                                $session->addNotice(
                                                    $this->__('Please remove your Gift Card information since you cannot use either gift codes or Gift Card credit balance to purchase other Gift Card products.'));
                                            }
                                        } else {
                                            $session->addError(
                                                $this->__('You can’t use this gift code since its conditions haven’t been met. <p>Please check these conditions by entering your gift code <a href="' . Mage::getUrl('giftvoucher/index/check') . '">here</a>.'));
                                        }
                                    } else {
                                        $session->addError($this->__('This gift code limits the number of users', 
                                            Mage::helper('giftvoucher')->getHiddenCode($code)));
                                    }
                                } else {
                                    $session->addError(
                                        $this->__('Gift code "%s" is no longer available to use.', $code));
                                }
                            }
                        } else {
                            $session->addError($this->__('The maximum number of times to enter gift codes is %d!', 
                                Mage::helper('giftvoucher')->getGeneralConfig('maximum')));
                        }
                    } else {
                        $session->addSuccess($this->__('Your Gift Card(s) has been applied successfully.'));
                    }
                } elseif ($session->getUseGiftCard()) {
                    $session->setUseGiftCard(null);
                    $session->addSuccess($this->__('Your Gift Card information has been removed successfully.'));
                }
            }
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * Add gift codes from Checkout page
     */
    public function addgiftAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $result = array();

        if ($quote->getCouponCode() && !Mage::helper('giftvoucher')->getGeneralConfig('use_with_coupon') 
            && (!$session->getUseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('giftvoucher')->__('A coupon code has been used. You cannot apply gift codes with the coupon to get discount.');
        } else {
            $addCodes = array();
            if ($code = trim($this->getRequest()->getParam('code'))) {
                $addCodes[] = $code;
            }
            if ($code = trim($this->getRequest()->getParam('addcode'))) {
                $addCodes[] = $code;
            }

            $max = Mage::helper('giftvoucher')->getGeneralConfig('maximum');
            $codes = Mage::getSingleton('giftvoucher/session')->getCodes();
            if (!count($addCodes)) {
                $errorMessage = Mage::helper('giftvoucher')->__('Invalid gift code. Please try again. ');
                if ($max) {
                    $errorMessage .= 
                        Mage::helper('giftvoucher')->__('You have %d time(s) remaining to re-enter Gift Card code.', 
                            $max - count($codes));
                }
                $result['error'] = $errorMessage;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
            if (!Mage::helper('giftvoucher')->isAvailableToAddCode()) {
                $giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                $result['html'] = $giftVoucherBlock->toHtml();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
            $quote->setTotalsCollectedFlag(false)->collectTotals()->save();

            foreach ($addCodes as $code) {
                $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);

                if (!$giftVoucher->getId() || ($giftVoucher->getSetId() > 0)) {
                    $codes[] = $code;
                    $codes = array_unique($codes);
                    Mage::getSingleton('giftvoucher/session')->setCodes($codes);
                    if (isset($errorMessage)) {
                        $result['error'] = $errorMessage . '<br/>';
                    } elseif (isset($result['error'])) {
                        $result['error'] .= '<br/>';
                    } else {
                        $result['error'] = '';
                    }
                    $errorMessage = Mage::helper('giftvoucher')->__('Gift card "%s" is invalid.', $code);
                    $maxErrorMessage = '';

                    if ($max) {
                        $maxErrorMessage = 
                            Mage::helper('giftvoucher')->__('You have %d times left to enter gift codes.', 
                                $max - count($codes));
                    }
                    $result['error'] .= $errorMessage . ' ' . $maxErrorMessage;
                } elseif ($giftVoucher->getId() 
                    && $giftVoucher->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE 
                    && $giftVoucher->getBaseBalance() > 0 && $giftVoucher->validate($quote->setQuote($quote))
                ) {
                    if (Mage::helper('giftvoucher')->canUseCode($giftVoucher)) {
                        $giftVoucher->addToSession($session);
                        $updatepayment = ($quote->getGrandTotal() < 0.001);
                        $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
                        if ($updatepayment xor ( $quote->getGrandTotal() < 0.001)) {
                            $result['updatepayment'] = 1;
                            break;
                        } else {
                            if ($giftVoucher->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId() 
                                && $giftVoucher->getRecipientName() && $giftVoucher->getRecipientEmail() 
                                && $giftVoucher->getCustomerId() 
                            ) {
                                if (!isset($result['notice'])) {
                                    $result['notice'] = '';
                                } else {
                                    $result['notice'] .= '<br/>';
                                }
                                $result['notice'] .= $this->__('Please note that gift code "%s" has been sent to your friend. When using, both you and your friend will share the same balance in the gift code.', Mage::helper('giftvoucher')->getHiddenCode($code));
                            }
                            $result['html'] = 1;
                        }
                    } else {
                        if (!isset($result['error'])) {
                            $result['error'] = '';
                        } else {
                            $result['error'] .= '<br/>';
                        }
                        $result['error'] .= $this->__('This gift code limits the number of users', $code);
                    }
                } else {
                    if (isset($errorMessage)) {
                        $result['error'] = $errorMessage . '<br/>';
                    } elseif (isset($result['error'])) {
                        $result['error'] .= '<br/>';
                    } else {
                        $result['error'] = '';
                    }
                    $result['error'] .= 
                        Mage::helper('giftvoucher')->__('Gift code "%s" is no longer available to use.', $code);
                }
            }
            if (isset($result['html']) && !isset($result['updatepayment'])) {
                $giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                $result['html'] = $giftVoucherBlock->toHtml();
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Remove gift codes from Checkout page
     */
    public function removeAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $code = trim($this->getRequest()->getParam('code'));
        $codes = $session->getGiftCodes();

        $success = false;
        if ($code && $codes) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $key => $value) {
                if ($value == $code) {
                    unset($codesArray[$key]);
                    $success = true;
                    $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
                    if (is_array($giftMaxUseAmount) && array_key_exists($code, $giftMaxUseAmount)) {
                        unset($giftMaxUseAmount[$code]);
                        $session->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                    }
                    break;
                }
            }
        }

        $result = array();
        if ($success) {
            $codes = implode(',', $codesArray);
            $session->setGiftCodes($codes);
            $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
            $session->getQuote()->collectTotals()->save();
            if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                $result['updatepayment'] = 1;
            } else {
                $giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                $result['html'] = $giftVoucherBlock->toHtml();
            }
        } else {
            $result['error'] = Mage::helper('giftvoucher')->__('Gift card "%s" is not found.', $code);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Change use gift card to spend
     */
    public function giftcardAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setUseGiftCard($this->getRequest()->getParam('giftvoucher'));
        $result = array();
        $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
        $session->getQuote()->collectTotals()->save();
        if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
            $result['updatepayment'] = 1;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Update the amount of gift codes
     */
    public function updateAmountAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $codes = $session->getGiftCodes();

        $code = $this->getRequest()->getParam('code');
        $amount = floatval($this->getRequest()->getParam('amount'));
        $result = array();
        if ($quote->getCouponCode() && !Mage::helper('giftvoucher')->getGeneralConfig('use_with_coupon') 
            && (!$session->getUseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('giftvoucher')->__('A coupon code has been used. You cannot apply Gift Card credit with the coupon to get discount.');
        } else {
            if (in_array($code, explode(',', $codes))) {
                $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
                if (!is_array($giftMaxUseAmount)) {
                    $giftMaxUseAmount = array();
                }
                $giftMaxUseAmount[$code] = $amount;
                $session->setGiftMaxUseAmount(serialize($giftMaxUseAmount));
                $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
                $quote->collectTotals()->save();
                if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                    $result['updatepayment'] = 1;
                } else {
                    $giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                    $result['html'] = $giftVoucherBlock->toHtml();
                }
            } else {
                $result['error'] = Mage::helper('giftvoucher')->__('Gift card "%s" is not added.', $code);
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Change use gift card to spend
     */
    public function giftcardcreditAction()
    {
        if (!Mage::helper('giftvoucher')->getGeneralConfig('enablecredit')) {
            return;
        }
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $session->setUseGiftCardCredit($this->getRequest()->getParam('giftcredit'));
        $result = array();
        if ($quote->getCouponCode() && !Mage::helper('giftvoucher')->getGeneralConfig('use_with_coupon') 
            && (!$session->getUseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('giftvoucher')->__('A coupon code has been used. You cannot apply Gift Card credit with the coupon to get discount.');
        } else {
            $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
            $quote->collectTotals()->save();
            if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                $result['updatepayment'] = 1;
            } else {
                $giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                $result['html'] = $giftVoucherBlock->toHtml();
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Update the amount of Gift Card credit
     */
    public function creditamountAction()
    {
        if (!Mage::helper('giftvoucher')->getGeneralConfig('enablecredit')) {
            return;
        }
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $result = array();

        if ($quote->getCouponCode() && !Mage::helper('giftvoucher')->getGeneralConfig('use_with_coupon') 
            && (!$session->getUseGiftCreditAmount() || !$session->getGiftVoucherDiscount())) {
            $result['notice'] = Mage::helper('giftvoucher')->__('A coupon code has been used. You cannot apply Gift Card credit with the coupon to get discount.');
        } else {
            $amount = floatval($this->getRequest()->getParam('amount'));
            if ($amount > -0.0001 && (abs($amount - $session->getMaxCreditUsed()) > 0.0001 
                || !$session->getMaxCreditUsed())
            ) {
                $session->setMaxCreditUsed($amount);
                $updatepayment = ($session->getQuote()->getGrandTotal() < 0.001);
                $session->getQuote()->collectTotals()->save();
                if ($updatepayment xor ( $session->getQuote()->getGrandTotal() < 0.001)) {
                    $result['updatepayment'] = 1;
                } else {
                    $giftVoucherBlock = $this->getLayout()->createBlock('giftvoucher/payment_form');
                    $result['html'] = $giftVoucherBlock->toHtml();
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Clear Gift Card session
     * @param $session
     */
    public function clearGiftcardSession($session)
    {
        if ($session->getUseGiftCard()) {
            $session->setUseGiftCard(null)
                ->setGiftCodes(null)
                ->setBaseAmountUsed(null)
                ->setBaseGiftVoucherDiscount(null)
                ->setGiftVoucherDiscount(null)
                ->setCodesBaseDiscount(null)
                ->setCodesDiscount(null)
                ->setGiftMaxUseAmount(null);
        }
        if ($session->getUseGiftCardCredit()) {
            $session->setUseGiftCardCredit(null)
                ->setMaxCreditUsed(null)
                ->setBaseUseGiftCreditAmount(null)
                ->setUseGiftCreditAmount(null);
        }
    }

}
