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
 * Customercredit Helper
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Helper_Data extends Mage_Core_Helper_Data
{

    /**
     * @param null $store
     * @return mixed
     */
    public function isDisabled($store = null)
    {
        return Mage::getStoreConfig('advanced/modules_disable_output/Magestore_Customercredit', $store);
    }

    /**
     * @param $code
     * @param null $store
     * @return mixed
     */
    public function getGeneralConfig($code, $store = null)
    {
        return Mage::getStoreConfig('customercredit/general/' . $code, $store);
    }

    /**
     * @param $code
     * @param null $store
     * @return mixed
     */
    public function getEmailConfig($code, $store = null)
    {
        return Mage::getStoreConfig('customercredit/email/' . $code, $store);
    }

    /**
     * @param $code
     * @param null $store
     * @return mixed
     */
    public function getSpendConfig($code, $store = null)
    {
        return Mage::getStoreConfig('customercredit/spend/' . $code, $store);
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        if (Mage::app()->getStore()->isAdmin())
            return Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        else
            return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * @param $amountStr
     * @return array
     */
    public function getCreditAmount($amountStr)
    {
        $amountStr = trim(str_replace(array(' ', "\r", "\t"), '', $amountStr));
        if ($amountStr == '' || $amountStr == '-') {
            return array('type' => 'any');
        }

        $values = explode('-', $amountStr);
        if (count($values) == 2) {
            return array('type' => 'range', 'from' => $values[0], 'to' => $values[1]);
        }

        $values = explode(',', $amountStr);
        if (count($values) > 1) {
            return array('type' => 'dropdown', 'options' => $values);
        }

        $value = floatval($amountStr);
        return array('type' => 'static', 'value' => $value);
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return '<a href="' . $this->getInfoLink() . '" class="customercredit-icon" title="' . $this->__('More information') . '">' . $this->getIconImage() . '</a>';
    }

    /**
     * @return string
     */
    public function getIconImage()
    {
        if (Mage::getVersion() < '1.9.0.0') {
            return '<img src="' . Mage::getDesign()->getSkinUrl('images/customercredit/point.png') . '" />';
        } else {
            return '<img style="display:inline;float:left;margin-right:3px;" src="' . Mage::getDesign()->getSkinUrl('images/customercredit/point.png') . '" />';
        }
    }

    /**
     * @return mixed
     */
    public function getInfoLink()
    {
        return Mage::getUrl('customercredit');
    }

    /**
     * @param $expression
     * @return mixed
     */
    public function calcCode($expression)
    {
        if ($this->isExpression($expression)) {
            return preg_replace_callback('#\[([AN]{1,2})\.([0-9]+)\]#', array($this, 'convertExpression'), $expression);
        } else {
            return $expression;
        }
    }

    /**
     * @param $param
     * @return mixed
     */
    public function convertExpression($param)
    {
        $alphabet = (strpos($param[1], 'A')) === false ? '' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= (strpos($param[1], 'N')) === false ? '' : '0123456789';
        return $this->getRandomString($param[2], $alphabet);
    }

    /**
     * @param $string
     * @return int
     */
    public function isExpression($string)
    {
        return preg_match('#\[([AN]{1,2})\.([0-9]+)\]#', $string);
    }

    /**
     * @param $code
     * @return string
     */
    public function getHiddenCode($code)
    {
        $prefix = 4;
        $prefixCode = substr($code, 0, $prefix);
        $suffixCode = substr($code, $prefix);
        if ($suffixCode) {
            $hiddenChar = 'X'; // $this->getGeneralConfig('hiddenchar');
            if (!$hiddenChar)
                $hiddenChar = 'X';
            else
                $hiddenChar = substr($hiddenChar, 0, 1);
            $suffixCode = preg_replace('#([A-Z,0-9]{1})#', $hiddenChar, $suffixCode);
        }
        return $prefixCode . $suffixCode;
    }

    /**
     * @param $code
     * @return bool
     */
    public function canUseCode($code)
    {
        if (!$code) {
            return false;
        }
        if (is_string($code)) {
            $code = Mage::getModel('customercredit/creditcode')->loadByCode($code);
        }
        if (!($code instanceof Magestore_Customercredit_Model_Creditcode)) {
            return false;
        }
        if (!$code->getId()) {
            return false;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }
    }

    /**
     * @param $code
     * @param null $store
     * @return mixed
     */
    public function getReportConfig($code, $store = null)
    {
        return Mage::getStoreConfig('customercredit/report/' . $code, $store);
    }

    //other module
    /**
     * @param $customer
     * @param $amount_credit
     * @param $type
     * @param $transaction_detail
     * @param $order_id
     * @return mixed
     */
    public function updateCredit($customer, $amount_credit, $type, $transaction_detail, $order_id)
    {
        return Mage::getModel('customercredit/api')->updateCredit($customer, $amount_credit, $type, $transaction_detail, $order_id);
    }

    /**
     * @param $customer
     * @return mixed
     */
    public function getCreditBalance($customer)
    {
        return Mage::getModel('customercredit/api')->getCreditBalance($customer);
    }

    /**
     * @param $customer
     * @param $creditcode
     * @return mixed
     */
    public function redeemCredit($customer, $creditcode)
    {
        return Mage::getModel('customercredit/api')->redeemCredit($customer, $creditcode);
    }

    /**
     * @return mixed
     */
    public function topFiveCustomerMaxCredit()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToFilter('credit_value', array('gt' => 0.00))
            ->setOrder('credit_value', 'DESC');
        $collection->getSelect()->limit(5);
        return $collection->getData();
    }

    /**
     * @param $order_id
     * @return bool
     */
    public function isBuyCreditProduct($order_id)
    {
        $order = Mage::getSingleton('sales/order');
        $order->load($order_id);
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == 'customercredit') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $email
     * @return string
     */
    public function getNameCustomerByEmail($email)
    {
        $collecions = Mage::getModel('customer/customer')->getCollection()
            ->addFieldToFilter('email', $email);

        $name = $email;
        foreach ($collecions as $customer) {
            $lastname = $customer->getLastname();
            $firstName = $customer->getFirstname();
            $name = $firstName . " " . $lastname;
        }
        
        return $name;
    }

    /*
     * Get max credit can user
     */

    /**
     * @param $customer_id
     * @param $subtotal
     * @param $tax
     * @param $shipping_fee
     * @param $shipping_tax
     * @return mixed
     */
    public function getMaxCreditCanUse($customer_id, $subtotal, $tax, $shipping_fee, $shipping_tax)
    {
        $session = Mage::getModel('checkout/session');
        $onestepGiftwrap = $session->getData('onestepcheckout_giftwrap_amount');
        //Zend_Debug::Dump($onestepGiftwrap);die();
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $credit_value = $customer->getCredit_value();
        $maxtotal = $subtotal;
        if ($this->getSpendConfig("tax") == "1")
            $maxtotal +=$tax;
        if ($this->getSpendConfig("shipping") == "1")
            $maxtotal +=$shipping_fee;

        if ($this->getSpendConfig("shipping_tax") != "1")
            $maxtotal -=$shipping_tax;
        if ($maxtotal > $credit_value)
            $maxtotal = $credit_value;


        return $maxtotal;
    }

    /**
     * @return mixed
     */
    public function getCustomercreditLabel()
    {
        $icon = $this->getIconImage();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customercredit = Mage::getModel('customer/customer')->load($customer->getId());
        $balance = $customercredit->getCreditValue();
        $moneyText = Mage::helper('core')->currency($balance);
        return $this->__('My Credit  %s %s', $moneyText, $icon);
    }

    /**
     * @param $itemCollection
     * @return null|string
     */
    public function getValueToCsv($itemCollection)
    {
        $groups = Mage::getModel('customer/group')
            ->load($itemCollection->getGroupId());
        $website = Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true);
        $callback = null;
        $data = array();
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getId()) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getName()) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getEmail()) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getCreditValue()) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $groups->getData('customer_group_code')) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getBillingTelephone()) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getData('billing_postcode')) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getData('billing_country_id')) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $itemCollection->getData('billing_region')) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp($itemCollection->getCreatedAt()))) . '"';
        $data [] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), $website[$itemCollection->getData('website_id')]) . '"';
        $callback = implode(',', $data);
        return $callback;
    }
    
    /**
     * Get the full Ccredit product options
     *
     * @return array
     */
    public function getFullCreditProductOptions()
    {
        return array(
            'customer_name' => $this->__('Sender Name'),
            'send_friend' => $this->__('Send credit to friend'),
            'recipient_name' => $this->__('Recipient name'),
            'recipient_email' => $this->__('Recipient email'),
            'message' => $this->__('Custom message'),
            'amount' => $this->__('Amount')
        );
    }

}
