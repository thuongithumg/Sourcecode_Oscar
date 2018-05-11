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
class InventoryConfigProvider implements ConfigProviderInterface {

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

        $configs = $this->_scopeConfig->getValue('cataloginventory', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        foreach($configs['item_options'] as $config => $value) {

            if($config == 'min_sale_qty') {
                try {
                    $options = unserialize($value);
                } catch(\Exception $e) {
                    $options = $value;
                }
                $values = [];
                if(is_array($options)) {
                    foreach($options as $groupId => $minQty) {
                        $values[] = ['group' => $groupId, 'value' => $minQty];
                    }     
                } else {
                    $values = $value;
                }
                $configs['item_options']['min_sale_qty'] = $values;
            }
        }
        /* convert configs to flat path */
        foreach($configs as $index => $subConfigs) {
            foreach($subConfigs as $subIndex => $value) {
                $results['cataloginventory/'.$index.'/'.$subIndex] = $value;
            }
        }

        return $results;
    }

}
