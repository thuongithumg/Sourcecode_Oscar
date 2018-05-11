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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Observer_Integration_WebposUseCustomerCreditAfter extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        try{
            if (!$this->_helper->isStoreCreditEnable()) {
                return $this;
            }
            $order = $observer->getEvent()->getOrder();
            $data = $observer->getEvent()->getExtensionData();
            if(isset($order) && $order->getId() && !empty($data) && isset($data['base_customercredit_discount'])){
                $amount = $data['base_customercredit_discount'];
                $customerId = $order->getCustomerId();
                $transaction = $this->_getModel('customercredit/transaction');
                $customercredit = $this->_getModel('customercredit/customercredit');
                if ($transaction && $customercredit && !empty($amount)) {
                    $transaction->addTransactionHistory($customerId, Magestore_Customercredit_Model_TransactionType::TYPE_CHECK_OUT_BY_CREDIT, $this->__('check out by credit for order #') . $order->getIncrementId(), $order->getId(), -$amount);
                    $customercredit->changeCustomerCredit(-$amount, $customerId);
                }
            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }
}