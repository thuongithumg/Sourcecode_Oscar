<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * class \Magestore\Webpos\Helper\Payment
 * 
 * Web POS Payment helper
 * Methods:
 *  getCasgMethodTitle
 *  getCcMethodTitle
 *  getCodMethodTitle
 *  getCp1MethodTitle
 *  getCp2MethodTitle
 *  getDefaultPaymentMethod
 *  getMultipaymentActiveMethodTitle
 *  getMultipaymentMethodTitle
 *  isAllowOnWebPOS
 *  isCashPaymentEnabled
 *  isCcPaymentEnabled
 *  isCodPaymentEnabled
 *  isCp1PaymentEnabled
 *  isCp2PaymentEnabled
 *  isMultiPaymentEnabled
 *  isWebposShippingEnabled
 *  updateCashTransactionFromOrder
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Payment extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * webpos transaction model
     *
     * @var \Magestore\Webpos\Model\Transaction
     */
    protected $_modelTransaction;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magestore\Webpos\Model\Transaction
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Webpos\Model\Transaction $modelTransaction
    ) {
        $this->_modelTransaction = $modelTransaction;
        parent::__construct($context);
    }

    /**
     *
     * @return string
     */
    public function getPaymentTitle($code)
    {
        $title = $this->scopeConfig->getValue('payment/'.$code.'/title', ScopeInterface::SCOPE_STORE);
        return $title;
    }

    /**
     * get title of Cash payment method
     * @return string
     */
    public function getCashMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/cashforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Cash ( For Web POS only)");
        }
        return $title;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCashPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/cashforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('cashforpos'));
    }
    
    /**
     * 
     * @return string
     */
    public function getCcMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/ccforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Cash ( For Web POS only)");
        }
        return $title;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCcPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/ccforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('ccforpos'));
    }
    
    /**
     * 
     * @return string
     */
    public function isWebposShippingEnabled()
    {
        return $this->scopeConfig->getValue('carriers/webpos_shipping/active', ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * 
     * @return string
     */
    public function getCp1MethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/cp1forpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Web POS - Custom Payment 1");
        }
        return $title;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCp1PaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/cp1forpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('cp1forpos'));
    }

    /**
     * 
     * @return string
     */
    public function getCp2MethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/cp2forpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Web POS - Custom Payment 2");
        }
        return $title;
    }

    /**
     * 
     * @return boolean
     */
    public function isCp2PaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/cp2forpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('cp2forpos'));
    }

    /**
     * 
     * @return string
     */
    public function getCodMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/codforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Web POS - Cash On Delivery");
        }
        return $title;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCodPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/codforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('codforpos'));
    }

    /**
     * 
     * @return string
     */
    public function getMultipaymentMethodTitle()
    {
        $title = $this->scopeConfig->getValue('payment/multipaymentforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Web POS - Split Payments");
        }
        return $title;
    }

    /**
     * 
     * @return array
     */
    public function getMultipaymentActiveMethodTitle()
    {
        $payments = $this->scopeConfig->getValue('payment/multipaymentforpos/payments', ScopeInterface::SCOPE_STORE);
        if ($payments == '') {
            $payments = explode(',', 'cp1forpos,cp2forpos,cashforpos,ccforpos,codforpos');
        }
        return explode(',', $payments);
    }
    
    /**
     * 
     * @return boolean
     */
    public function isMultiPaymentEnabled()
    {
        return ($this->scopeConfig->getValue('payment/multipaymentforpos/active', ScopeInterface::SCOPE_STORE) && $this->isAllowOnWebPOS('multipaymentforpos'));
    }
    
    /**
     * 
     * @param string $code
     * @return boolean
     */
    public function isAllowOnWebPOS($code)
    {
        if ($this->scopeConfig->getValue('webpos/payment/allowspecific_payment', ScopeInterface::SCOPE_STORE) == '1') {
            $specificpayment = $this->scopeConfig->getValue('webpos/payment/specificpayment', ScopeInterface::SCOPE_STORE);
            $specificpayment = explode(',', $specificpayment);
            if (in_array($code, $specificpayment)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 
     * @return string
     */
    public function getDefaultPaymentMethod()
    {
        return $this->scopeConfig->getValue('webpos/payment/defaultpayment', ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * 
     * @param \Magento\Sales\Model\Order $order
     * @param string $newCash
     */
    public function updateCashTransactionFromOrder($order, $newCash)
    {
        try {
            $enable_till = $this->scopeConfig->getValue('webpos/general/enable_tills', ScopeInterface::SCOPE_STORE);
            if ($order->getIncrementId() && $enable_till) {
                $payment_method = $order->getPayment()->getMethodInstance()->getCode();
                $cashIn = (float) $order->getData('webpos_cash');
                if ($newCash >= $cashIn) {
                    $cashIn = (float) ($newCash - $cashIn);
                    $cashOut = 0;
                } else {
                    $cashOut = (float) ($cashIn - $newCash);
                    $cashIn = 0;
                }
                $data_transaction = array(
                    'payment_method' => $payment_method,
                    'cash_in' => $cashIn,
                    'cash_out' => $cashOut,
                    'store_id' => $order->getStoreId(),
                    'user_id' => $order->getWebposAdminId(),
                    'order_id' => $order->getIncrementId(),
                    'till_id' => $order->getTillId(),
                    'location_id' => $order->getLocationId(),
                    'note' => '',
                    'type' => ($cashIn == 0)?'out':'in',
                    'amount' => ($cashIn == 0)?$cashOut:$cashIn
                );

                if ($order->getData('webpos_cash') > 0) {
                    $this->_modelTransaction->saveTransactionData($data_transaction);
                }
            }
        } catch (LocalizedException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Check webpos payment
     *
     * @param string
     * @return boolean
     */
    public function isWebposPayment($code)
    {
        $payments = array('multipaymentforpos','cp1forpos','cp2forpos','cashforpos','ccforpos','codforpos');
        return in_array($code, $payments);
    }

    /**
     * Check webpos payment is pay later
     *
     * @param string
     * @return boolean
     */
    public function isPayLater($code)
    {
        $isPayLater = $this->scopeConfig->getValue('payment/'.$code.'/pay_later', ScopeInterface::SCOPE_STORE);
        return $isPayLater;
    }

    /**
     * Check webpos payment is pay later
     *
     * @param string
     * @return boolean
     */
    public function isReferenceNumber($code)
    {
        $isReferenceNumber = $this->scopeConfig->getValue('payment/'.$code.'/use_reference_number', ScopeInterface::SCOPE_STORE);
        return $isReferenceNumber;
    }

    /**
     * Check webpos paypal enable
     *
     * @param string
     * @return boolean
     */
    public function isPaypalEnable()
    {
        $isPaypalEnable = $this->scopeConfig->getValue('webpos/payment/paypal/enable', ScopeInterface::SCOPE_STORE);
        return $isPaypalEnable;
    }

    /**
     * get use cvv
     * @return string
     */
    public function useCvv($code)
    {
        $useCvv = $this->scopeConfig->getValue('payment/'.$code.'/useccv', ScopeInterface::SCOPE_STORE);
        return $useCvv;
    }

    /**
     * @return bool
     */
    public function isRetailerPos()
    {
        if(isset($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if ((strpos(strtolower($userAgent), 'ipad')!==false || strpos(strtolower($userAgent), 'android')!==false)
                && (!strpos(strtolower($userAgent), 'mozilla')!==false)
            ) {
                return true;
            }
        }
        return false;
    }

}
