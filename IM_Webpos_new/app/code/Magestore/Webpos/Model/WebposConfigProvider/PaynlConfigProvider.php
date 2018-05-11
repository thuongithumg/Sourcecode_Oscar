<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\WebposConfigProvider;
use Magestore\WebposPaynl\Model\Config;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class PaynlConfigProvider implements ConfigProviderInterface {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function getConfig() {
        $results = array();
        if ($this->_moduleManager->isEnabled('Magestore_WebposPaynl')) {
            $results['paynlBank'] =  $this->getBanks();
        }
        return $results;
    }

    public function getBanks()
    {
//        $show_banks = $this->_scopeConfig->getValue('payment/' . $this->_code . '/bank_selection', 'store');
//        if (!$show_banks) return [];
        $cache = $this->getCache();
        $cacheName = 'paynl_terminals_' . '1729';
        $banksJson = $cache->load($cacheName);
        if ($banksJson) {
            $banks = json_decode($banksJson);
        } else {
            $banks = array();
            try {
                $config = new Config($this->_scopeConfig);
                if ($config->configureSDK()) {
                    $terminals = \Paynl\Instore::getAllTerminals();
                    $terminals = $terminals->getList();

                    foreach ($terminals as $terminal) {
                        $terminal['visibleName'] = $terminal['name'];
                        array_push($banks, $terminal);
                    }
                    $cache->save(json_encode($banks), $cacheName);
                }
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
