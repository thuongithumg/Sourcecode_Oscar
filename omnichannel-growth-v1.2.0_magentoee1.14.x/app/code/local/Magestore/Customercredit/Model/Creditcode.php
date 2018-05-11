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
class Magestore_Customercredit_Model_Creditcode extends Mage_Rule_Model_Rule
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('customercredit/creditcode');
    }

    /**
     * @return mixed
     */
    protected function _beforeSave()
    {
        //  if (!$this->getId())
        //      $this->setStatus(Magestore_Customercredit_Model_Status::STATUS_PENDING);
        if (!$this->getCreditCode())
            $this->setCreditCode('[N.4]-[AN.5]-[A.4]');
        if ($this->_codeIsExpression())
            $this->setCreditCode($this->_getCreditCode());
        return parent::_beforeSave();
    }

    /**
     * @return mixed
     */
    protected function _codeIsExpression()
    {
        return Mage::helper('customercredit')->isExpression($this->getCreditCode());
    }

    /**
     * @return mixed
     */
    protected function _getCreditCode()
    {
        $code = Mage::helper('customercredit')->calcCode($this->getCreditCode());
        $times = 10;
        while ($this->loadByCode($code)->getId() && $times) {
            $code = Mage::helper('customercredit')->calcCode($this->getCreditCode());
            $times--;
            if ($times == 0) {
                throw new Mage_Core_Exception('Exceeded maximum retries to find available random credit code!');
            }
        }
        return $code;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'credit_code');
    }

    /**
     * @param $credit_code_id
     * @param $status
     * @return string
     */
    public function changeCodeStatus($credit_code_id, $status)
    {
        $credit_code = Mage::getModel('customercredit/creditcode')->load($credit_code_id);
        $credit_code->setStatus($status);
        try {
            $credit_code->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $friend_email
     * @param $credit_amount
     * @param $status
     * @param $customer_id
     * @return string
     */
    public function addCreditCode($friend_email, $credit_amount, $status, $customer_id)
    {

        $store = Mage::app()->getStore();
        $currentCurrencyCode = $store->getCurrentCurrency()->getCode();
        $credit_code = Mage::getModel('customercredit/creditcode');
        if ($status) {
            $credit_code->setStatus($status);
        }
        $credit_code->setRecipientEmail($friend_email)
            ->setDescription('send code to friend')
            ->setTransactionTime(now())
            ->setAmountCredit($credit_amount)
            ->setCustomerId($customer_id)
            ->setCurrency($currentCurrencyCode);
        try {
            $credit_code->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $credit_code->getCreditCode();
    }

}
