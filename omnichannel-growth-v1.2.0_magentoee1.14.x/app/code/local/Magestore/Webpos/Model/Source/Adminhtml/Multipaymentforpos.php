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

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Source_Adminhtml_Multipaymentforpos {

    protected $_allowPayments = array();
    protected $_allowPaymentsWithLabel = array();

    public function __construct() {
        $this->_allowPayments = array('cashforpos', 'ccforpos', 'codforpos', 'cp1forpos', 'cp2forpos');
        $this->_allowPaymentsWithLabel = array(
            'cashforpos' => Mage::helper('webpos/payment')->getCashMethodTitle(),
            'ccforpos' => Mage::helper('webpos/payment')->getCcMethodTitle(),
            'codforpos' => Mage::helper('webpos/payment')->getCodMethodTitle(),
            'cp1forpos' => Mage::helper('webpos/payment')->getCp1MethodTitle(),
            'cp2forpos' => Mage::helper('webpos/payment')->getCp2MethodTitle()
        );
    }

    public function toOptionArray() {
        $collection = Mage::getModel('payment/config')->getAllMethods();

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

    public function getAllowPaymentMethods() {
        return $this->_allowPayments;
    }

    public function getAllowPaymentMethodsWithLabel() {
        return $this->_allowPaymentsWithLabel;
    }

}
