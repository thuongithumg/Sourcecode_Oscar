<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\WebposConfigProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class RewardpointConfigProvider implements ConfigProviderInterface {

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

    public function getConfig() {
        $results = [];
        $configs = [];

        if ($this->_moduleManager->isEnabled('Magestore_Rewardpoints')) {
            $configs = $this->_scopeConfig->getValue('rewardpoints', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        /* convert configs to flat path */
        if($configs) {
            foreach ($configs as $index => $subConfigs) {
                foreach ($subConfigs as $subIndex => $value) {
                    $results['rewardpoints/' . $index . '/' . $subIndex] = $value;
                }
            }
        }

        return $results;
    }

}
