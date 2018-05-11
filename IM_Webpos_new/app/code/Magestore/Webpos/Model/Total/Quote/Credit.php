<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Total\Quote;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address;

/**
 * Class Total
 * @package Magestore\Webpos\Model\Cart\Quote
 */
class Credit extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;


    /**
    * @var \Magestore\Webpos\Helper\Data
    */
    protected $_helperWebpos;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Session $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magestore\Webpos\Helper\Data $helperWebpos
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_helperData = $helperData;
        $this->_storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->_request = $request;
        $this->_objectManager = $objectmanager;
        $this->_helperWebpos = $helperWebpos;
        $this->setCode('customerbalance');
    }

    /**
     * Collect customer balance totals for specified address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return Customerbalance
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$this->_helperWebpos->checkMagentoEE()) {
            return $this;
        }
        $isWebpos = $this->_request->getParam('session');
        if (!$isWebpos) {
            return $this;
        }

        $customerId = $quote->getCustomer()->getId();
        $baseBalance = $balance = 0;
        if ($quote->getCustomer()->getId()) {
            $store = $this->_storeManager->getStore($quote->getStoreId());
            $customerData = $this->_objectManager->create('\Magento\CustomerBalance\Model\BalanceFactory')->create()->getCollection()
                ->addFieldToFilter(
                'customer_id',
                $customerId
            )->getFirstItem();
            $baseBalance = $customerData->getAmount();
            $balance = $this->priceCurrency->convert($baseBalance, $quote->getStore());
        }

        $baseAmountLeft = $baseBalance - $quote->getBaseCustomerBalAmountUsed();
        $amountLeft = $balance - $quote->getCustomerBalanceAmountUsed();

        $isUsedCredit =  $this->_checkoutSession->getData('use_storecredit_ee');

        if ($isUsedCredit) {
            if ($baseAmountLeft >= $total->getBaseGrandTotal()) {
                $baseUsed = $total->getBaseGrandTotal();
                $used = $total->getGrandTotal();

                $total->setBaseGrandTotal(0);
                $total->setGrandTotal(0);
            } else {
                $baseUsed = $baseAmountLeft;
                $used = $amountLeft;

                $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseAmountLeft);
                $total->setGrandTotal($total->getGrandTotal() - $amountLeft);
            }
            $baseTotalUsed = $quote->getBaseCustomerBalAmountUsed() + $baseUsed;
            $totalUsed = $quote->getCustomerBalanceAmountUsed() + $used;

            $quote->setBaseCustomerBalAmountUsed($baseTotalUsed);
            $quote->setCustomerBalanceAmountUsed($totalUsed);
            $quote->setUseCustomerBalance(true);
            $this->_checkoutSession->setData('credit_amount', $used);

            $total->setBaseCustomerBalanceAmount($baseUsed);
            $total->setCustomerBalanceAmount($used);
        } else {
            $this->_checkoutSession->setData('credit_amount', 0);
            $quote->setUseCustomerBalance(false);
        }
        return $this;
    }

    /**
     * Return shopping cart total row items
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Total $total
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if ($this->_checkoutSession->getData('credit_amount')) {
            return [
                'code' => $this->getCode(),
                'title' => __('Store Credit'),
                'value' => -$this->_checkoutSession->getData('credit_amount')
            ];
        }
        return null;
    }

}
