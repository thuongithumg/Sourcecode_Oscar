<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Multipaymentforpos
 * 
 * Web POS Multipaymentforpos source model
 * Methods:
 *  getAllowPaymentMethods
 *  getAllowPaymentMethodsWithLabel
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Multipaymentforpos implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Options array
     *
     * @var array
     */
    protected $_options;

    /**
     * Allow payments array
     *
     * @var array
     */
    protected $_allowPayments;

    /**
     * Allow payments with label array
     *
     * @var array
     */
    protected $_allowPaymentsWithLabel;

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfigModel;

    /**
     * @param \Magento\Payment\Model\Config $paymentConfigModel
     * @param \Magestore\Webpos\Helper\Payment $webposPaymentHelper
     */
    public function __construct(
        \Magento\Payment\Model\Config $paymentConfigModel, 
        \Magestore\Webpos\Helper\Payment $webposPaymentHelper
    ) {
        $this->_paymentConfigModel = $paymentConfigModel;
        $this->_allowPayments = array('cashforpos', 'ccforpos', 'codforpos', 'cp1forpos', 'cp2forpos');
        $this->_allowPaymentsWithLabel = array(
            'cashforpos' => $webposPaymentHelper->getCashMethodTitle(),
            'ccforpos' => $webposPaymentHelper->getCcMethodTitle(),
            'codforpos' => $webposPaymentHelper->getCodMethodTitle(),
            'cp1forpos' => $webposPaymentHelper->getCp1MethodTitle(),
            'cp2forpos' => $webposPaymentHelper->getCp2MethodTitle()
        );
        $collection = $this->_paymentConfigModel->getActiveMethods();

        if (count($collection) > 0) {
            $options = array();
            foreach ($collection as $item) {
                if (!in_array($item->getCode(), $this->_allowPayments))
                    continue;
                $title = $item->getTitle() ? $item->getTitle() : $item->getCode();
                $options[] = array('value' => $item->getCode(), 'label' => $title);
            }
            $this->_options = $options;
        }
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_options;
    }

    /**
     * get array of allow payment methods
     * @return array
     */
    public function getAllowPaymentMethods()
    {
        return $this->_allowPayments;
    }
    
    /**
     * get array of allow payment methods with label
     * @return array
     */
    public function getAllowPaymentMethodsWithLabel() {
        return $this->_allowPaymentsWithLabel;
    }
}
