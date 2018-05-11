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
 * Class Magestore_Webpos_Model_Source_Adminhtml_Paymentoffline
 */
class Magestore_Webpos_Model_Source_Adminhtml_Paymentoffline {
    /**
     * @var array
     */
    protected $_allowPayments = array();

    /**
     * Magestore_Webpos_Model_Source_Adminhtml_Paymentoffline constructor.
     */
    public function __construct() {
        $this->_allowPayments = array('cashforpos','codforpos','ccforpos','cp1forpos', 'cp2forpos');
        $this->_paymentHelper = Mage::helper('webpos/payment');
    }

    /**
     * @return array|void
     */
    public function toOptionArray() {
        $collection = Mage::getModel('payment/config')->getActiveMethods();

        if (!count($collection))
            return;

        $options = array();
        foreach ($collection as $item) {
            if (!in_array($item->getId(), $this->_allowPayments))
                continue;
            $title = $item->getTitle() ? $item->getTitle() : $item->getId();
            $options[] = array('value' => $item->getId(), 'label' => $title);
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getAllowPaymentMethods() {
        return $this->_allowPayments;
    }

    /**
     * @return array
     */
    public function getOfflinePaymentData(){
        $collection = Mage::getModel('payment/config')->getActiveMethods();
        $paymentList = array();
        if(count($collection) > 0) {
            foreach ($collection as $item) {
                if (!in_array($item->getId(), $this->_allowPayments))
                    continue;
                if (!$this->_paymentHelper->isAllowOnWebPOS($item->getId()))
                    continue;
                $isDefault = Magestore_Webpos_Api_PaymentInterface::NO;
                if($item->getId() == $this->_paymentHelper->getDefaultPaymentMethod()) {
                    $isDefault = Magestore_Webpos_Api_PaymentInterface::YES;
                }
                $isReferenceNumber = Magestore_Webpos_Api_PaymentInterface::NO;
                if ($item->getConfigData('use_reference_number')){
                    $isReferenceNumber = Magestore_Webpos_Api_PaymentInterface::YES;
                }
                $isPayLater = 0;
                if ($item->getConfigData('pay_later')){
                    $isPayLater = Magestore_Webpos_Api_PaymentInterface::YES;
                }
                $iconClass = 'icon-iconPOS-payment-cp1forpos';
                $multiable = false;
                if ($this->_paymentHelper->isWebposPayment($item->getId())){
                    $iconClass = 'icon-iconPOS-payment-'.$item->getId();
                    $multiable = true;
                }
                $paymentModel =  Mage::getModel('webpos/payment_payment');
                $paymentModel->setCode($item->getId());
                $paymentModel->setIconClass($iconClass);
                $paymentModel->setTitle($item->getTitle());
                $paymentModel->setInformation('');
                $paymentModel->setType(Magestore_Webpos_Api_PaymentInterface::NO);
                $paymentModel->setIsDefault($isDefault);
                $paymentModel->setIsReferenceNumber($isReferenceNumber);
                $paymentModel->setIsPayLater($isPayLater);
                $paymentModel->setMultiable($multiable);
                $paymentSource = Mage::getModel('webpos/source_adminhtml_payment');
                $formData = $paymentSource->getPaymentFormInfo($item->getId());
                $paymentModel->setTemplate($formData['template']);
                $paymentModel->setFormData($formData['data']);
                $paymentList[] = $paymentModel->getData();
            }
        }
        return $paymentList;
    }
}
