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
 * Giftvoucher Api Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */

class Magestore_Giftvoucher_Model_Api extends Mage_Api_Model_Resource_Abstract
{

    /**
     * Filter Gift voucher collection
     *
     * @param mixed|null $filters
     * @return Magestore_Giftvoucher_Model_Giftvoucher_Collection
     */
    public function items($filters = NULL)
    {
        $collection = Mage::getModel('giftvoucher/giftvoucher')->getCollection();
        if (is_array($filters)) {
            try {
                foreach ($filters as $key => $value) {
                    $collection->addFieldToFilter($key, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }
        $result = array();
        foreach ($collection as $giftvoucher) {
            $result[] = $giftvoucher->toArray();
        }
        return $result;
    }

    /**
     * Check a gift code whether it is available
     *
     * @param string $code
     * @return array
     */
    public function check($code)
    {
        $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
        if (!$giftVoucher->getId()) {
            $this->_fault('not_exists', Mage::helper('giftvoucher')->__('Gift card "%s" is not exists', $code));
        }
        return $giftVoucher->toArray();
    }

    /**
     * Create a gift code
     *
     * @param mixed $data
     * @return array
     */
    public function create($data)
    {
        $data = $this->_prepareData($data);
        try {
            if (!isset($data['balance']) || $data['balance'] <= 0) {
                $this->_fault('data_invalid', 
                    Mage::helper('giftvoucher')->__('Your Gift Card credit balance must be greater than 0.'));
            }
            $data['amount'] = $data['balance'];
            $data = $this->processData($data);

            $model = Mage::getModel('giftvoucher/giftvoucher');
            $model->setData($data)
                ->setIncludeHistory(true)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $model->toArray();
    }

    /**
     * Create many gift codes
     *
     * @param mixed $data
     * @return array
     */
    public function massCreate($data)
    {
        $data = $this->_prepareData($data);
        try {
            if (!isset($data['balance']) || $data['balance'] <= 0) {
                $this->_fault('data_invalid', 
                    Mage::helper('giftvoucher')->__('Your Gift Card credit balance must be greater than 0.'));
            }
            if (!Mage::helper('giftvoucher')->isExpression($data['pattern'])) {
                $this->_fault('data_invalid', 
                    Mage::helper('giftvoucher')->__('Invalid pattern'));
            }
            $data = $this->processData($data);

            $model = Mage::getModel('giftvoucher/template');
            $model->setData($data)
                ->save();
            $giftcard = $model->getData();
            $giftcard['gift_code'] = $giftcard['pattern'];
            $giftcard['template_id'] = $model->getId();
            $giftcard['amount'] = $giftcard['balance'];
            $giftcard['status'] = Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE;
            $amount = $model->getAmount();
            $result = array();
            for ($i = 1; $i <= $amount; $i++) {
                $giftvoucher = Mage::getModel('giftvoucher/giftvoucher')
                    ->setData($giftcard)
                    ->setIncludeHistory(true)
                    ->save();
                $result[] = $giftvoucher->toArray();
            }
            $model->setIsGenerated(1)->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $result;
    }

    /**
     * Get gift codes's history
     *
     * @param $code
     * @return array
     * @throws Mage_Api_Exception
     * @internal param mixed $data
     */
    public function history($code)
    {
        $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
        if (!$giftVoucher->getId()) {
            $this->_fault('invalid_code', 
                Mage::helper('giftvoucher')->__('Gift card "%s" is not exists', $code));
        }
        $collection = Mage::getModel('giftvoucher/history')
            ->getCollection()
            ->addFieldToFilter('giftvoucher_id', $giftVoucher->getId());
        $result = array();
        foreach ($collection as $giftvoucherhistory) {
            $result[] = $giftvoucherhistory->toArray();
        }
        return $result;
    }

    /**
     * Update the data of a gift code
     *
     * @param mixed $data
     * @param string $code
     * @return boolean
     */
    public function update($code, $data)
    {
        $data = $this->_prepareData($data);
        $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
        if (!$giftVoucher->getId()) {
            $this->_fault('invalid_code', 
                Mage::helper('giftvoucher')->__('Gift card "%s" is not exists', $code));
        }
        try {
            if (isset($data['balance'])) {
                $data['amount'] = $data['balance'];
            } else {
                $data['amount'] = $giftVoucher->getBalance();
            }
            $data['action'] = Magestore_Giftvoucher_Model_Actions::ACTIONS_UPDATE;
            $data['extra_content'] = Mage::helper('giftvoucher')->__('Updated by Api user');
            $giftVoucher->addData($data)
                ->setIncludeHistory(true)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return True;
    }

    /**
     * Send Gift Card emails
     *
     * @param mixed $data
     * @return boolean
     */
    public function sendEmail($data)
    {
        $data = $this->_prepareData($data);
        if (!isset($data['gift_code']) || !isset($data['type'])) {
            $this->_fault('data_invalid', Mage::helper('giftvoucher')->__('Invalid data'));
        }
        $code = $data['gift_code'];
        $sendtofriend = $data['type'];
        $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
        if (!$giftVoucher->getId()) {
            $this->_fault('invalid_code', 
                Mage::helper('giftvoucher')->__('Gift card "%s" is not exists', $code));
        }
        if ($sendtofriend == 'to_friend') {
            if ($giftVoucher->getRecipientEmail()) {
                $giftVoucher->sendEmailToFriend();
            } else {
                $this->_fault('data_invalid', 
                    Mage::helper('giftvoucher')->__('The email address of Gift Card recipient does not exist.'));
            }
        } elseif ($sendtofriend == 'to_all') {
            if ($giftVoucher->getCustomerEmail()) {
                $giftVoucher->sendEmail();
            } else {
                $this->_fault('data_invalid', 
                    Mage::helper('giftvoucher')->__('Email of Gift card Purchaser does not exist.'));
            }
        } elseif ($sendtofriend == 'to_owner') {
            if ($giftVoucher->getCustomerEmail()) {
                $giftVoucher->setRecipientEmail('');
                $giftVoucher->sendEmail();
            } else {
                $this->_fault('data_invalid', 
                    Mage::helper('giftvoucher')->__('Email of Gift card Purchaser does not exist.'));
            }
        } else {
            $this->_fault('data_invalid', Mage::helper('giftvoucher')->__('Invalid data'));
        }
        return TRUE;
    }

    /**
     * Redeem gift codes to Gift Card credit
     *
     * @param string $code
     * @param int $customerId
     * @return array
     */
    public function redeemToCredit($customerId, $code)
    {
        $giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
        if (!$giftVoucher->getId()) {
            $this->_fault('invalid_code', 
                Mage::helper('giftvoucher')->__('Gift card "%s" is not exists', $code));
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getEmail()) {
            $this->_fault('data_invalid', 
                Mage::helper('giftvoucher')->__('Customer ID does not exist.'));
        }

        if (!Mage::helper('giftvoucher')->canUseCode($giftVoucher)) {
            $this->_fault('limited_code', $code . 
                Mage::helper('giftvoucher')->__('This gift code limits the number of users'));
        }

        if ($giftVoucher->getBalance() == 0) {
            $this->_fault('invalid_code', $code . 
                Mage::helper('giftvoucher')->__(' - The current balance of this gift code is 0.'));
        }
        if ($giftVoucher->getStatus() != 2 && $giftVoucher->getStatus() != 4) {
            $this->_fault('invalid_code', $code . 
                Mage::helper('giftvoucher')->__('Gift card "%s" is not avaliable'));
        } else {
            $balance = $giftVoucher->getBalance();

            $credit = Mage::getModel('giftvoucher/credit')->getCreditByCustomerId($customerId);

            $creditCurrencyCode = $credit->getCurrency();
            $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
            if (!$creditCurrencyCode) {
                $creditCurrencyCode = $baseCurrencyCode;
                $credit->setCurrency($creditCurrencyCode);
                $credit->setCustomerId($customer->getId());
            }

            $voucherCurrency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
            $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
            $creditCurrency = Mage::getModel('directory/currency')->load($creditCurrencyCode);

            $amountTemp = $balance * $balance / $baseCurrency->convert($balance, $voucherCurrency);
            $amount = $baseCurrency->convert($amountTemp, $creditCurrency);

            $credit->setBalance($credit->getBalance() + $amount);

            $credithistory = Mage::getModel('giftvoucher/credithistory')
                ->setCustomerId($customer->getId())
                ->setAction('Api_re')
                ->setCurrencyBalance($credit->getBalance())
                ->setGiftcardCode($giftVoucher->getGiftCode())
                ->setBalanceChange($balance)
                ->setCurrency($giftVoucher->getCurrency())
                ->setCreatedDate(now());
            $history = Mage::getModel('giftvoucher/history')->setData(array(
                'order_increment_id' => '',
                'giftvoucher_id' => $giftVoucher->getId(),
                'created_at' => now(),
                'action' => Magestore_Giftvoucher_Model_Actions::ACTIONS_REDEEM,
                'amount' => $balance,
                'balance' => 0.0,
                'currency' => $giftVoucher->getCurrency(),
                'status' => $giftVoucher->getStatus(),
                'order_amount' => '',
                'comments' => Mage::helper('giftvoucher')->__('Redeem to Gift Card credit balance'),
                'extra_content' => Mage::helper('giftvoucher')->__('Redeemed by Api'),
            ));
            try {
                $credit->save();
            } catch (Exception $e) {
                $this->_fault('data_invalid', $e->getMessage());
            }
            try {
                $giftVoucher->setBalance(0)->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_USED)->save();
            } catch (Exception $e) {
                $credit->setBalance($credit->getBalance() - $amount)->save();
                $this->_fault('data_invalid', $e->getMessage());
            }
            try {
                $history->save();
                $credithistory->save();
            } catch (Exception $e) {
                $giftVoucher
                    ->setBalance($balance)
                    ->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE)->save();
                $credit->setBalance($credit->getBalance() - $amount)->save();
                $this->_fault('data_invalid', $e->getMessage());
            }
        }
        return $credithistory->toArray();
    }

    /**
     * Update Gift Card credit to a customer account
     *
     * @param int $customerId
     * @param int $balance
     * @param string $currency
     * @return array
     */
    public function updateCredit($customerId, $balance, $currency = NULL)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getEmail()) {
            $this->_fault('data_invalid', Mage::helper('giftvoucher')->__('Customer ID does not exist.'));
        }
        $credit = Mage::getModel('giftvoucher/credit')->getCreditByCustomerId($customerId);
        if (!$currency) {
            $currency = Mage::app()->getStore()->getDefaultCurrencyCode();
        }
        $creditCurrencyCode = $credit->getCurrency();
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        if (!$creditCurrencyCode) {
            $creditCurrencyCode = $baseCurrencyCode;
            $credit->setCurrency($creditCurrencyCode);
            $credit->setCustomerId($customerId);
        }

        $voucherCurrency = Mage::getModel('directory/currency')->load($currency);
        $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
        $creditCurrency = Mage::getModel('directory/currency')->load($creditCurrencyCode);

        $amountTemp = $balance * $balance / $baseCurrency->convert($balance, $voucherCurrency);
        $amount = $baseCurrency->convert($amountTemp, $creditCurrency);

        $credit->setBalance($credit->getBalance() + $amount);

        $credithistory = Mage::getModel('giftvoucher/credithistory')
            ->setCustomerId($customerId)
            ->setAction('Apiupdate')
            ->setCurrencyBalance($credit->getBalance())
            ->setBalanceChange($balance)
            ->setCurrency($currency)
            ->setCreatedDate(now());

        try {
            $credit->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        try {
            $credithistory->save();
        } catch (Mage_Core_Exception $e) {
            $credit->setBalance($credit->getBalance() - $amount)->save();
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $credithistory->toArray();
    }

    /**
     * Get Gift Card credit information by customer ID
     *
     * @param int $customerId
     * @return array
     */
    public function info($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getEmail()) {
            $this->_fault('data_invalid', Mage::helper('giftvoucher')->__('Customer ID does not exist.'));
        }
        $credit = Mage::getModel('giftvoucher/credit')->getCreditByCustomerId($customer->getId());
        return $credit->toArray();
    }

    /**
     * @param $array
     * @param $dateFields
     * @return mixed
     */
    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

    /**
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function processData($data)
    {
        if (!isset($data['template_name'])) {
            $data['template_name'] = Mage::helper('giftvoucher')->__('Created by Api user');
        }
        if (!isset($data['currency'])) {
            $data['currency'] = Mage::app()->getStore()->getDefaultCurrencyCode();
        }
        if (!isset($data['expired_at'])) {
            $data['expired_at'] = null;
        }
        if (!isset($data['status'])) {
            $data['status'] = Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE;
        }
        $data['extra_content'] = Mage::helper('giftvoucher')->__('Created by Api user');
        return $data;
    }

    /**
     * Prepare data to create/massCreate/update/sendEmail.
     * Using API v1
     * 
     * @param array $data
     * @return array
     */
    protected function _prepareData($data)
    {
        return $data;
    }

}
