<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Payment
 * 
 * Web POS Payment source model
 * Methods:
 *  getAllowPaymentMethods
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Payment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Helper payment object
     *
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $_paymentHelper;

    /**
     * webpos payment model
     *
     * @var \Magestore\Webpos\Model\Payment\Payment
     */
    protected $_paymentModel;

    /**
     * Allow payments array
     *
     * @var array
     */
    protected $_allowPayments;

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfigModel;

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_corePaymentHelper;

    /**
     * @param \Magestore\Webpos\Helper\Payment $paymentHelper
     * @param \Magestore\Webpos\Model\Payment\Payment $paymentModel
     * @param \Magento\Payment\Model\Config $paymentConfigModel
     */
    public function __construct(
        \Magestore\Webpos\Helper\Payment $paymentHelper,
        \Magestore\Webpos\Model\Payment\PaymentFactory $paymentModel,
        \Magento\Payment\Model\Config $paymentConfigModel,
        \Magento\Payment\Helper\Data $corePaymentHelper
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_paymentModel = $paymentModel;
        $this->_paymentConfigModel = $paymentConfigModel;
        $this->_corePaymentHelper = $corePaymentHelper;
        $this->_allowPayments = array(
        	'cashforpos',
        	'codforpos',
        	'ccforpos',
        	'cp1forpos',
        	'cp2forpos',
        	'paypal_direct',
        	'authorizenet_directpost',
        	'cryozonic_stripe',
            'payflowpro_integration',
            'paynl_payment_instore'
        );
        $this->_ccPayments = array(
        	'authorizenet_directpost',
        	'cryozonic_stripe',
            'payflowpro_integration',
            'paynl_payment_instore'
        );
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_paymentConfigModel->getActiveMethods();
        $storeMethods = $this->_corePaymentHelper->getStoreMethods();
        $ignores = array();
        $options = array();
        $options[] = array('value' => 0, 'label'=> __(' '));
        $ignores = $this->addPaymentsOptions($options, $collection, $ignores, true);
        $ignores = $this->addPaymentsOptions($options, $storeMethods, $ignores, true);
        // check payment method payflowpro
//        $payflowproIntegrationPayment = $this->_corePaymentHelper->getMethodInstance('payflowpro_integration');
//        if($payflowproIntegrationPayment->isActiveWebpos()) {
//            $payment = [$payflowproIntegrationPayment];
//            $ignores = $this->addPaymentsOptions($options, $payment, $ignores, true);
//        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $collection = $this->_paymentConfigModel->getActiveMethods();
        $storeMethods = $this->_corePaymentHelper->getStoreMethods();
        $ignores = array();
        $options = array(0 => __('Please select a payment'));
        $ignores = $this->addPaymentsOptions($options, $collection, $ignores);
        $ignores = $this->addPaymentsOptions($options, $storeMethods, $ignores);
        // check payment method payflowpro
        $payflowproIntegrationPayment = $this->_corePaymentHelper->getMethodInstance('payflowpro_integration');
        if($payflowproIntegrationPayment->isActiveWebpos()) {
            $payment = [$payflowproIntegrationPayment];
            $ignores = $this->addPaymentsOptions($options, $payment, $ignores, true);
        }
        return $options;
    }

    /**
     * get payment methods for pos
     *
     * @return array
     */
    public function getPosPaymentMethods()
    {
        $collection = $this->_paymentConfigModel->getActiveMethods();
        $storeMethods = $this->_corePaymentHelper->getStoreMethods();
        $paymentList = array();
        $ignores = array();
        $ignores = $this->addPosPayments($paymentList, $collection, $ignores);
        $ignores = $this->addPosPayments($paymentList, $storeMethods, $ignores);
        return $paymentList;
    }

    /**
     * get array of allow payment methods
     * @return array
     */
    public function getAllowPaymentMethods()
    {
        return $this->_allowPayments;
    }

    public function addPaymentsOptions(&$list, $collection, $ignores, $widthLabel = false){
    	$addedMethods = array();
		if(count($collection) > 0) {
            foreach ($collection as $item) {
                if(
            		in_array($item->getCode(), $ignores)
            		|| !in_array($item->getCode(), $this->_allowPayments)
            		// || !$this->_paymentHelper->isAllowOnWebPOS($item->getCode())
            	){
            		continue;
            	}
				$title = $item->getTitle() ? $item->getTitle() : $item->getCode();
            	if($widthLabel){
                	$list[] = array('value' => $item->getCode(), 'label' => $title);
            	}else{
                	$list["'".$item->getCode()."'"] = $title;
                }
                $addedMethods[] = $item->getCode();
            }
        }
        return $addedMethods;
    }

    public function addPosPayments(&$list, $collection, $ignores){
    	$addedMethods = array();
		if(count($collection) > 0) {
            foreach ($collection as $item) {
            	if(
            		in_array($item->getCode(), $ignores)
            		|| !in_array($item->getCode(), $this->_allowPayments)
            		|| !$this->_paymentHelper->isAllowOnWebPOS($item->getCode())
            	){
            		continue;
            	}
                $isDefault = '0';
                if($item->getCode() == $this->_paymentHelper->getDefaultPaymentMethod()) {
                    $isDefault = '1';
                }
                $isReferenceNumber = '0';
                if ($item->getConfigData('use_reference_number')){
                    $isReferenceNumber = '1';
                }
                $isPayLater = 0;
                if ($item->getConfigData('pay_later')){
                    $isPayLater = '1';
                }
                $paymentModel = $this->_paymentModel->create();
                $paymentModel->setCode($item->getCode());
                $paymentModel->setTitle($item->getTitle());
                $paymentModel->setInformation('');
                $paymentModel->setType('0');
                $paymentModel->setIsDefault($isDefault);
                $paymentModel->setIsReferenceNumber($isReferenceNumber);
                $paymentModel->setIsPayLater($isPayLater);
                if(in_array($item->getCode(), $this->_ccPayments))
                    $paymentModel->setType('1');
                $list[] = $paymentModel->getData();
                $addedMethods[] = $item->getCode();
            }
        }
        return $addedMethods;
    }

}
