<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Integration;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;



class WebposCreateOrderWithGiftcardAfter implements ObserverInterface
{

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * WebposUseCustomerCreditAfter constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Webpos\Helper\Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        try{
            if (!$this->_moduleManager->isEnabled('Magestore_Giftvoucher')) {
                return $this;
            }
            $order = $observer->getEvent()->getOrder();
            $data = $observer->getEvent()->getExtensionData();
            if(isset($order) && $order->getId() && $order->getBaseGiftVoucherDiscount() > 0){
                if(count($data) > 0){
                    $baseCurrencyCode = $order->getBaseCurrencyCode();
                    $orderCurrencyCode = $order->getData('order_currency_code');

                    $helper = $this->_objectManager->create('Magestore\Giftvoucher\Helper\Data');
                    if(method_exists($helper, 'isRebuildVersion')){
                        if($helper->isRebuildVersion()){
                            $checkoutService = $this->_objectManager->create('\Magestore\Giftvoucher\Api\Redeem\CheckoutServiceInterface');
                            $codes = array_keys($data);
                            $codesBaseDiscount = array_values($data);
                            $codesDiscount = $this->getCodesDiscount($data, $baseCurrencyCode, $orderCurrencyCode);
                            $order->setGiftVoucherGiftCodes(implode(',',$codes ));
                            $order->setCodesBaseDiscount(implode(',',$codesBaseDiscount ));
                            $order->setCodesDiscount(implode(',',$codesDiscount ));
                            $checkoutService->processOrderPlaceAfter($order);
                            return $this;
                        }
                    }

                    foreach ($data as $code => $baseDiscount){
                        $giftVoucher = $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher')
                            ->loadByCode($code);
                        $baseCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                            ->load($baseCurrencyCode);
                        $currentCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                            ->load($giftVoucher->getData('currency'));

                        $codeDiscount = $this->_objectManager->create('Magento\Directory\Helper\Data')
                            ->currencyConvert($baseDiscount, $baseCurrencyCode, $giftVoucher->getData('currency'));
                        $codeCurrentDiscount = $this->_objectManager->create('Magento\Directory\Helper\Data')
                            ->currencyConvert($baseDiscount, $baseCurrencyCode, $orderCurrencyCode);
                        $balance = $giftVoucher->getBalance() - $codeDiscount;
                        if ($balance > 0) {
                            $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                        } else {
                            $baseBalance = 0;
                        }
                        $currentBalance = $this->_objectManager->create('Magento\Directory\Helper\Data')
                            ->currencyConvert($baseBalance, $baseCurrencyCode, $orderCurrencyCode);
                        $giftVoucher->setData('balance', $balance)->save();
                        if ($order->getData('customer_id') == null) {
                            $customerName = __('Used by Guest');
                        } else {
                            $customerName = __('Used by %1 %1', $order->getData('customer_firstname'), $order->getData('customer_lastname'));
                        }
                        $history = $this->_objectManager->create('Magestore\Giftvoucher\Model\History')->setData(array(
                            'order_increment_id' => $order->getIncrementId(),
                            'giftvoucher_id' => $giftVoucher->getId(),
                            'created_at' => date("Y-m-d H:i:s"),
                            'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_SPEND_ORDER,
                            'amount' => $codeCurrentDiscount,
                            'balance' => $currentBalance,
                            'currency' => $orderCurrencyCode,
                            'status' => $giftVoucher->getStatus(),
                            'order_amount' => $codeCurrentDiscount,
                            'comments' => __('Spent on order %1', $order->getIncrementId()),
                            'extra_content' => $customerName,
                            'customer_id' => $order->getData('customer_id'),
                            'customer_email' => $order->getData('customer_email')
                        ));
                        $history->save();

                        // add gift code to customer list
                        if ($order->getCustomerId()) {
                            $collection = $this->_objectManager
                                ->create('Magestore\Giftvoucher\Model\ResourceModel\Customervoucher\Collection')
                                ->addFieldToFilter('customer_id', $order->getCustomerId())
                                ->addFieldToFilter('voucher_id', $giftVoucher->getId());
                            if (!$collection->getSize()) {
                                try {
                                    $timeSite = date(
                                        "Y-m-d",
                                        $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->timestamp(time())
                                    );
                                    $this->_objectManager->create('Magestore\Giftvoucher\Model\Customervoucher')
                                        ->setCustomerId($order->getCustomerId())
                                        ->setVoucherId($giftVoucher->getId())
                                        ->setAddedDate($timeSite)
                                        ->save();
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }
                    }
                }
            }
        }catch(\Exception $e){
            $this->helper->addLog($e->getMessage());
        }
    }

    /**
     * @param $data
     * @param $baseCurrencyCode
     * @param $orderCurrencyCode
     * @return array
     */
    public function getCodesDiscount($data, $baseCurrencyCode, $orderCurrencyCode){
        $codesDiscount = array();
        foreach ($data as $code => $baseDiscount){
            $codeCurrentDiscount = $this->_objectManager->create('Magento\Directory\Helper\Data')
                ->currencyConvert($baseDiscount, $baseCurrencyCode, $orderCurrencyCode);
            $codesDiscount[] = $codeCurrentDiscount;
        }
        return $codesDiscount;
    }
}