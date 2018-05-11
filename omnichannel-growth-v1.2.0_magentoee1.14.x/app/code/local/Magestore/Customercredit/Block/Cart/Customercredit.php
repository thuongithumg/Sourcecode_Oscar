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
 * Customercredit Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Cart_Customercredit extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customercredit/cart/customercredit.phtml');
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItemOnly()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $hasOnly = false;
        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductType() == 'customercredit') {
                $hasOnly = true;
            } else {
                $hasOnly = false;
                break;
            }
        }
        return $hasOnly;
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItem()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductType() == 'customercredit') {
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function isLoggedIn()
    {
        return Mage::helper('customercredit/account')->isLoggedIn();
    }

    /**
     * @return mixed
     */
    public function isEnableCredit()
    {
        return Mage::helper('customercredit')->getGeneralConfig('enable');
    }

    /**
     * @return mixed
     */
    public function getCurrentCreditAmount()
    {
        $baseAmount = Mage::getSingleton('checkout/session')->getBaseCustomerCreditAmount();
        return Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($baseAmount);
    }

    /**
     * @return mixed
     */
    public function getCustomerCredit()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCredit();
    }

    /**
     * @return mixed
     */
    public function getCustomerCreditLabel()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCreditLabel();
    }

    /**
     * @return mixed
     */
    public function getAvaiableCustomerCreditLabel()
    {
        return Mage::getModel('customercredit/customercredit')->getAvaiableCustomerCreditLabel();
    }
}
