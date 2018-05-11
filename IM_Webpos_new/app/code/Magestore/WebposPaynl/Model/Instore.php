<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Model;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magestore\WebposPaynl\Model\Config;

/**
 * Description of Instore
 *
 * @author Andy Pieters <andy@pay.nl>
 */
class Instore extends \Magestore\WebposPaynl\Model\Paymentmethod\Paymentmethod
{
    protected $_code = 'paynl_payment_instore';

    public function startTransaction($quote, $total, $currency, $bankId)
    {
//        $additionalData = $order->getPayment()->getAdditionalInformation();
//        $bankId = null;
//        if (isset($additionalData['bank_id'])) {
//            $bankId = $additionalData['bank_id'];
//        }
//        unset($additionalData['bank_id']);

        $transaction = $this->doStartTransaction($quote, $total, $currency, $bankId);
        $bankId = '';
        $instorePayment = \Paynl\Instore::payment([
            'transactionId' => $transaction->getTransactionId(),
            'terminalId' => $bankId
        ]);

        $additionalData['terminal_hash'] = $instorePayment->getHash();

//        $order->getPayment()->setAdditionalInformation($additionalData);
//        $order->save();

        return $instorePayment->getRedirectUrl();
    }

    public function getBanks()
    {
//        $show_banks = $this->_scopeConfig->getValue('payment/' . $this->_code . '/bank_selection', 'store');
//        if (!$show_banks) return [];

        $cache = $this->getCache();
        $cacheName = 'paynl_terminals_' . $this->getPaymentOptionId();
        $banksJson = $cache->load($cacheName);
        if ($banksJson) {
            $banks = json_decode($banksJson);
        } else {
            $banks = [];
            try {
                $config = new Config($this->_scopeConfig);

                $config->configureSDK();

                $terminals = \Paynl\Instore::getAllTerminals();
                $terminals = $terminals->getList();

                foreach ($terminals as $terminal) {
                    $terminal['visibleName'] = $terminal['name'];
                    array_push($banks, $terminal);
                }
                $cache->save(json_encode($banks), $cacheName);
            } catch (\Paynl\Error\Error $e) {
                // Probably instore is not activated, no terminals present
            }
        }
        array_unshift($banks, array(
            'id' => '',
            'name' => __('Choose the pin terminal'),
            'visibleName' => __('Choose the pin terminal')
        ));
        return $banks;
    }

    /**
     * @return \Magento\Framework\App\CacheInterface
     */
    private function getCache()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\CacheInterface $cache */
        $cache = $om->get('Magento\Framework\App\CacheInterface');
        return $cache;
    }
}