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
/**
 * class Magestore_Webpos_Model_Payment_PaymentRepository
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Magestore_Webpos_Model_Payment_PaymentRepository
{

    protected $_paymentSource = false;
    protected $_offlinePaymentSource = false;

    /**
     * Magestore_Webpos_Model_Shipping_ShippingRepository constructor.
     */
    public function __construct(
    ) {
        $this->_paymentSource = Mage::getSingleton('webpos/source_adminhtml_payment');
        $this->_offlinePaymentSource = Mage::getSingleton('webpos/source_adminhtml_paymentoffline');
    }

    /**
     * @return array
     */
    public function getList() {
        $paymentList = ($this->_paymentSource)?$this->_paymentSource->getPaymentData():array();

        $shippings = array();
        $shippings['items'] = $paymentList;
        $shippings['total_count'] = count($paymentList);
        return $shippings;
    }

    /**
     * @return Magestore_Webpos_Api_PaymentInterface[]
     */
    public function getOfflinePaymentData(){
        $shippings = ($this->_offlinePaymentSource)?$this->_offlinePaymentSource->getOfflinePaymentData():array();
        return $shippings;
    }
}