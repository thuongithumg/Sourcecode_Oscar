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
 * Giftvoucher Payment Form Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Payment_Form extends Mage_Payment_Block_Form
{

    protected $_checkoutLayout = 'giftvoucher/payment/form.phtml';

    protected function _construct()
    {
        $storeId = Mage::app()->getStore()->getId();
        parent::_construct();

        if (Mage::helper('giftvoucher')->getGeneralConfig('active', $storeId) 
            && Mage::getStoreConfig('giftvoucher/interface_payment/show_gift_card')) {
            $class = get_class(Mage::app()->getFrontController()->getAction());
            if (isset($class) && $class != null) {
                $infor = explode('_', $class);
                $extensionName = $infor[0] . '_' . $infor[1];
                if (($extensionName != 'Mage_Checkout') && ($extensionName != 'Magestore_Giftvoucher')) {
                    if (is_file(Mage::getDesign()->getTemplateFilename('giftvoucher' . DS . 'onestepcheckout' . DS . 
                        $extensionName . DS . 'form.phtml'))) {
                        $this->_checkoutLayout = 'giftvoucher/onestepcheckout/' . $extensionName . '/form.phtml';
                    }
                }
                $this->setTemplate($this->_checkoutLayout);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Mage::getStoreConfig('payment/giftvoucher/description');
    }

    /**
     * @return bool
     */
    public function isPassed()
    {
        return (Mage::app()->getStore()->roundPrice($this->getGrandTotal()) == 0);
    }

    /**
     * @return array
     */
    public function getGiftVoucherDiscount()
    {
        $session = Mage::getSingleton('checkout/session');
        $discounts = array();
        if ($codes = $session->getGiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesDiscountArray = explode(',', $session->getCodesDiscount());
            $discounts = array_combine($codesArray, $codesDiscountArray);
        }
        return $discounts;
    }

    /**
     * @return mixed
     */
    public function getGrandTotal()
    {
        if (!$this->hasData('grand_total')) {
            $quote = Mage::getSingleton('checkout/session')->getQuote()->collectTotals();
            $grandTotal = $quote->getGrandTotal();
            /*
              $addresses = Mage::getSingleton('checkout/session')->getQuote()->getAllShippingAddresses();
              $grandTotal = 0;
              foreach ($addresses as $address)
              $grandTotal += $address->getGrandTotal();
             */
            $this->setData('grand_total', $grandTotal);
        }
        return $this->getData('grand_total');
    }

    /**
     * @return string
     */
    public function getAddGiftVoucherUrl()
    {
        return trim($this->getUrl('giftvoucher/checkout/addgift'), '/');
    }

    /**
     * check customer use gift card to checkout
     * 
     * @return boolean
     */
    public function getUseGiftVoucher()
    {
        return Mage::getSingleton('checkout/session')->getUseGiftCard();
    }

    /**
     * @return mixed
     */
    public function checkCustomerIsLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * get existed gift Card
     * 
     * @return array
     */
    public function getExistedGiftCard()
    {
        $session = Mage::getSingleton('customer/session');
        if (!$session->isLoggedIn()) {
            return array();
        }
        $customerId = $session->getCustomer()->getId();
        $storeId = Mage::app()->getStore()->getStoreId();
        $collection = Mage::getResourceModel('giftvoucher/customervoucher_collection')
            ->addFieldToFilter('main_table.customer_id', $customerId);
        $voucherTable = $collection->getTable('giftvoucher/giftvoucher');
        $collection->getSelect()
            ->join(array('v' => $voucherTable), 'main_table.voucher_id = v.giftvoucher_id', array(
                    'gift_code', 'balance', 'currency', 'conditions_serialized', 'store_id')
            )->where('v.status = ?', Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE)
            ->where("v.recipient_name IS NULL OR v.recipient_name = '' OR (v.customer_email <> ?)", $session->getCustomer()->getEmail())
            ->where("v.set_id IS NULL OR v.set_id <= 0 ")
        ;
        $giftCards = array();
        $addedCodes = array();
        if ($codes = Mage::getSingleton('checkout/session')->getGiftCodes()) {
            $addedCodes = explode(',', $codes);
        }
        $helper = Mage::helper('giftvoucher');
        $conditions = Mage::getSingleton('giftvoucher/giftvoucher')->getConditions();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote->setQuote($quote);
        foreach ($collection as $item) {
            $itemId = $item->getStoreId();
            if ($itemId != 0 && $itemId != $storeId) {
                continue;
            }
            if (in_array($item->getGiftCode(), $addedCodes)) {
                continue;
            }
            if ($item->getConditionsSerialized()) {
                $conditionsArr = unserialize($item->getConditionsSerialized());
                if (!empty($conditionsArr) && is_array($conditionsArr)) {
                    $conditions->setConditions(array())->loadArray($conditionsArr);
                    if (!$conditions->validate($quote)) {
                        continue;
                    }
                }
            }
            $giftCards[] = array(
                'store_id' => $item->getStoreId(),
                'gift_code' => $item->getGiftCode(),
                'hidden_code' => $helper->getHiddenCode($item->getGiftCode()),
                'balance' => $this->getGiftCardBalance($item)
            );
        }
        return $giftCards;
    }

    /**
     * Get the balance of Gift Card
     *
     * @param mixed $item
     * @return string
     */
    public function getGiftCardBalance($item)
    {
        $cardCurrency = Mage::getModel('directory/currency')->load($item->getCurrency());
        /* @var Mage_Core_Model_Store */
        $store = Mage::app()->getStore();
        $baseCurrency = $store->getBaseCurrency();
        $currentCurrency = $store->getCurrentCurrency();
        if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
            return $store->formatPrice($item->getBalance());
        }
        if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
            return $store->convertPrice($item->getBalance(), true);
        }
        if ($baseCurrency->convert(100, $cardCurrency)) {
            $amount = $item->getBalance() * $baseCurrency->convert(100, $currentCurrency) 
                / $baseCurrency->convert(100, $cardCurrency);
            return $store->formatPrice($amount);
        }
        return $cardCurrency->format($item->getBalance(), array(), true);
    }

    /**
     * Get customer Credit to checkout
     * 
     * @return Magestore_Giftvoucher_Model_Credit
     */
    public function getCustomerCredit()
    {
        if ($this->checkCustomerIsLoggedIn()) {
            $credit = Mage::getModel('giftvoucher/credit')->load(
                Mage::getSingleton('customer/session')->getCustomerId(), 'customer_id'
            );
            if ($credit->getBalance() > 0.0001) {
                return $credit;
            }
        }
        return false;
    }

    /**
     * Returns the formatted Gift Card balance
     *
     * @param mixed $credit
     * @param boolean $showUpdate
     * @return string
     */
    public function formatBalance($credit, $showUpdate = false)
    {
        if ($showUpdate) {
            $cardCurrency = Mage::getModel('directory/currency')->load($credit->getCurrency());
            /* @var Mage_Core_Model_Store */
            $store = Mage::app()->getStore();
            $baseCurrency = $store->getBaseCurrency();
            $currentCurrency = $store->getCurrentCurrency();
            if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
                return $store->formatPrice($credit->getBalance() - $this->getUseGiftCreditAmount());
            }
            if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
                $amount = $store->convertPrice($credit->getBalance(), false);
                return $store->formatPrice($amount - $this->getUseGiftCreditAmount());
            }
            if ($baseCurrency->convert(100, $cardCurrency)) {
                $amount = $credit->getBalance() * $baseCurrency->convert(100, $currentCurrency) 
                    / $baseCurrency->convert(100, $cardCurrency);
                return $store->formatPrice($amount - $this->getUseGiftCreditAmount());
            }
            return $cardCurrency->format($credit->getBalance(), array(), true);
        }
        return $this->getGiftCardBalance($credit);
    }

    /**
     * Check customer use gift credit to checkout
     * 
     * @return boolean
     */
    public function getUseGiftCredit()
    {
        return Mage::getSingleton('checkout/session')->getUseGiftCardCredit();
    }

    /**
     * @return string
     */
    public function getUsingAmount()
    {
        return Mage::app()->getStore()->formatPrice(
                Mage::getSingleton('checkout/session')->getUseGiftCreditAmount()
        );
    }

    /**
     * @return mixed
     */
    public function getUseGiftCreditAmount()
    {
        return Mage::getSingleton('checkout/session')->getUseGiftCreditAmount();
    }

    /**
     * @return bool
     */
    public function getOneStepCheckOutEnabled()
    {
        return Mage::helper('giftvoucher')->isModuleOutputEnabled('Magestore_Onestepcheckout');
    }

}
