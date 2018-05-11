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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Customercredit Model
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Model_Customercredit extends Mage_Core_Model_Abstract
{

    /**
     * @param $email
     * @param $value
     * @param $message
     * @param $keycode
     * @return $this
     */
    public function sendVerifyEmail($email, $value, $message, $keycode)
    {
        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
        $customerData = Mage::getModel('customer/customer')->load($customer_id);
        $send_name = $customerData->getFirstname() . " " . $customerData->getLastname();
        $store = Mage::app()->getStore($this->getStoreId());
        $share_url = Mage::getSingleton('core/url')->getUrl('customercredit/index/validateCustomer');
        $veriryurl = Mage::getSingleton('core/url')->getUrl('customercredit/index/sharepost?keycode=' . $keycode);
        $veriryurl = substr($veriryurl, 0, strlen($veriryurl) - 1);
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template')
            ->setDesignConfig(array(
            'area' => 'frontend',
            'store' => $store->getStoreId()
        ));
        $mailTemplate->sendTransactional(
            Mage::helper('customercredit')->getEmailConfig('verify', $store->getStoreId()), Mage::helper('customercredit')->getEmailConfig('sender', $store->getStoreId()), $customerData->getEmail(), $customerData->getName(), array(
            'store' => $store,
            'recipient_email' => $email,
            'value' => $this->getLabel($value),
            'send_name' => $send_name,
            'message' => $message,
            'emailcode' => $keycode,
            'verifyurl' => $veriryurl,
            'share_url' => $share_url,
            )
        );
        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * @param $credit_amount
     * @param null $customer_id
     */
    public function changeCustomerCredit($credit_amount, $customer_id = null)
    {
        if ($customer_id == null)
            $customer = Mage::helper('customercredit')->getCustomer();
        else
            $customer = Mage::getModel('customer/customer')->load($customer_id);
        $customer->setCreditValue($customer->getCreditValue() + $credit_amount)->save();
    }

    /**
     * @param $credit_amount
     * @param $customer_id
     * @return string
     */
    public function addCreditToFriend($credit_amount, $customer_id)
    {
        $friend_account = Mage::getModel('customer/customer')->load($customer_id);
        if (isset($friend_account)) {
            $friend_credit_balance = $friend_account->getCreditValue() + $credit_amount;
            $friend_account->setCreditValue($friend_credit_balance);
            try {
                $friend_account->save();
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    /**
     * @param $credit_amount
     * @param $friend_email
     * @param $message
     * @param $customerId
     */
    public function sendCreditToFriendByEmail($credit_amount, $friend_email, $message, $customerId)
    {
        $credit_code = Mage::getModel('customercredit/creditcode');
        $code = $credit_code->addCreditCode($friend_email, $credit_amount, Magestore_Customercredit_Model_Status::STATUS_UNUSED, $customerId);
        $this->sendCodeToFriendEmail($friend_email, $credit_amount, $message, $code);
    }

    /**
     * @param $credit_code_id
     * @param $credit_amount
     * @param $friend_email
     * @param $message
     */
    public function sendCreditToFriendByEmailAfterVerify($credit_code_id, $credit_amount, $friend_email, $message)
    {
        $credit_code = Mage::getModel('customercredit/creditcode')->load($credit_code_id);
        if (isset($credit_code) && isset($friend_email) && isset($credit_amount)) {
            $this->sendCodeToFriendEmail($friend_email, $credit_amount, $message, $credit_code->getCreditCode());
        }
    }

    /**
     * @param $email
     * @param $amount
     * @param $message
     * @param $creditcode
     * @return $this
     */
    public function sendCodeToFriendEmail($email, $amount, $message, $creditcode)
    {
        $receiver_name = Mage::helper('customercredit')->getNameCustomerByEmail($email);
        $customerData = Mage::helper('customercredit')->getCustomer();

        $send_name = $customerData->getFirstname() . " " . $customerData->getLastname();
        $store = Mage::app()->getStore($this->getStoreId());
        $login_url = Mage::getSingleton('core/url')->getUrl('customer/account/login');
        $redeem_page_url = Mage::getSingleton('core/url')->getUrl('customercredit/index/redeem');
        $redeemurl = Mage::getSingleton('core/url')->getUrl('customercredit/index/redeem?code=' . $creditcode);
        $redeemurl = substr($redeemurl, 0, strlen($redeemurl) - 1);
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template')
            ->setDesignConfig(array(
            'area' => 'frontend',
            'store' => $store->getStoreId()
        ));
        $mailTemplate->sendTransactional(
            Mage::helper('customercredit')->getEmailConfig('creditcode', $store->getStoreId()),
            Mage::helper('customercredit')->getEmailConfig('sender', $store->getStoreId()), $email, $send_name, array(
            'store' => $store,
            'sendername' => $send_name,
            'receivename' => $receiver_name,
            'value' => $amount,
            'login_url' => $login_url,
            'redeem_page_url' => $redeem_page_url,
            'message' => $message,
            'creditcode' => $creditcode,
            'redeemurl' => $redeemurl,
            )
        );
        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function getBaseLabel($amount)
    {
        $base_currency = Mage::app()->getStore()->getBaseCurrency();
        return $base_currency->format($amount);
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function getLabel($amount)
    {
        $current_currency = Mage::app()->getStore()->getCurrentCurrency();
        return $current_currency->format($amount);
    }

    /**
     * @return mixed
     */
    public function getBaseCustomerCredit()
    {
        return Mage::helper('customercredit')->getCustomer()->getCreditValue();
    }

    /**
     * @return mixed
     */
    public function getBaseCustomerCreditLabel()
    {
        $customer_credit = $this->getBaseCustomerCredit();
        return $this->getBaseLabel($customer_credit);
    }

    /**
     * @return float
     */
    public function getCustomerCredit()
    {
        $session = Mage::getSingleton('checkout/session');
        $store = Mage::app()->getStore();
        $customer_credit = $this->getBaseCustomerCredit();
        return round($store->convertPrice($customer_credit), 3);
    }

    /**
     * @return mixed
     */
    public function getCustomerCreditLabel()
    {
        return $this->getLabel($this->getCustomerCredit());
    }

    /**
     * @return float
     */
    public function getAvaiableCustomerCredit()
    {
        $session = Mage::getSingleton('checkout/session');
        $store = Mage::app()->getStore();
        $customer_credit = $this->getBaseCustomerCredit() - $session->getBaseCustomerCreditAmount();
        return round($store->convertPrice($customer_credit), 3);
    }

    /**
     * @return mixed
     */
    public function getAvaiableCustomerCreditLabel()
    {
        return $this->getLabel($this->getAvaiableCustomerCredit());
    }

    /**
     * @param $credit_amount
     * @return mixed
     */
    public function getConvertedFromBaseCustomerCredit($credit_amount)
    {
        $store = Mage::app()->getStore();
        return $store->convertPrice($credit_amount);
    }

    /**
     * @param $credit_amount
     * @return string
     */
    public function getConvertedToBaseCustomerCredit($credit_amount)
    {
        $rate = Mage::app()->getStore()->convertPrice(1);
        return $credit_amount / $rate;
    }

    public function createCreditProduct()
    {
        $product1 = new Mage_Catalog_Model_Product();
        $product1->setStoreId(0)
            ->setId(null)
            ->setAttributeSetId(9)
            ->setTypeId("customercredit")
            ->setName("Credit product 1")
            ->setSku("creditproduct")
            ->setStatus("1")
            ->setTaxClassId("0")
            ->setVisibility("4")
            ->setEnableGooglecheckout("1")
            ->setCreditAmount("100")
            ->setDescription("Credit product")
            ->setShortDescription("credit")
            ->save();
        $product2 = new Mage_Catalog_Model_Product();
        $product2->setStoreId(0)
            ->setId(null)
            ->setAttributeSetId(9)
            ->setTypeId("customercredit")
            ->setName("Credit product 2")
            ->setSku("creditproduct")
            ->setStatus("1")
            ->setTaxClassId("0")
            ->setVisibility("4")
            ->setEnableGooglecheckout("1")
            ->setCreditAmount("1-10000")
            ->setDescription("Credit product")
            ->setShortDescription("credit")
            ->save();
    }

    /**
     * @param $email
     * @param $name
     * @param $credit_value
     * @param $balance
     * @param $message
     * @return $this
     */
    public function sendNotifytoCustomer($email, $name, $credit_value, $balance, $message)
    {
        $store = Mage::app()->getStore($this->getStoreId());
        $storeurl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template')
            ->setDesignConfig(array(
            'area' => 'frontend',
            'store' => $store->getStoreId()
        ));
        if ($credit_value > 0) {
            $mailTemplate->sendTransactional(
                Mage::helper('customercredit')->getEmailConfig('notify', $store->getStoreId()), Mage::helper('customercredit')->getEmailConfig('sender', $store->getStoreId()), $email, $name, array(
                'store' => $store,
                'storeurl' => $storeurl,
                'receivename' => $name,
                'value' => $credit_value,
                'message' => $message,
                'balance' => $balance,
                'addcredit' => true,
                )
            );
        } else {
            $mailTemplate->sendTransactional(
                Mage::helper('customercredit')->getEmailConfig('notify', $store->getStoreId()), Mage::helper('customercredit')->getEmailConfig('sender', $store->getStoreId()), $email, $name, array(
                'store' => $store,
                'storeurl' => $storeurl,
                'receivename' => $name,
                'value' => $credit_value,
                'message' => $message,
                'balance' => $balance,
                'deductcredit' => true,
                )
            );
        }

        $translate->setTranslateInline(true);
        return $this;
    }
    
     /**
     * Send the success notification email
     * 
     * @return Magestore_Customercredit_Model_Customercredit
     */
    public function sendSuccessEmail($email, $customer, $receivename, $check)
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        if ($email) {
            $store = Mage::app()->getStore($this->getStoreId());
            $mailTemplate = Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $store->getStoreId()
            ));
            $mailTemplate->sendTransactional(
                Mage::helper('customercredit')->getEmailConfig('notify_success', $store->getStoreId()), 
                    Mage::helper('customercredit')->getEmailConfig('sender', $store->getStoreId()), 
                        $email, $customer, array(
                            'receivename' => $receivename,
                            'buycreditproduct' => $check,
                )
            );
        }
        $translate->setTranslateInline(false);
        return $this;
    }

}
