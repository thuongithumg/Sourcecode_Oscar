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
/**
 * Class GiftvoucherConfigProvider
 * @package Magestore\Webpos\Model\WebposConfigProvider
 */
class GiftvoucherConfigProvider implements ConfigProviderInterface {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Timezone
     */
    protected $_timezone;

    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * GiftvoucherConfigProvider constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Config\Model\Config\Source\Locale\Timezone $timezone
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Config\Model\Config\Source\Locale\Timezone $timezone,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_moduleManager = $moduleManager;
        $this->_timezone = $timezone;
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function getConfig() {
        $results = [];

        if ($this->_moduleManager->isEnabled('Magestore_Giftvoucher')) {
            $giftVoucherHelper = $this->objectManager->get('Magestore\Giftvoucher\Helper\Data');
            $results['enable_giftvoucher'] = 1;
            $timezones = $this->_timezone->toOptionArray();
            $timezoneArray = array();
            foreach ($timezones as $timezone) {
                $timezoneArray[] = $timezone;
            }
            $results['timezones'] = $timezoneArray;
            $results['messageMaxLength'] = $this->objectManager->get('Magestore\Giftvoucher\Helper\Data')->getInterfaceConfig('max');
            $results['imageBaseUrl'] = $this->storeManager
                    ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'giftvoucher/template/images';
            if ($giftVoucherHelper->getInterfaceConfig('postoffice_date')) {
                $results['postOfficeDate'] = $giftVoucherHelper->getInterfaceConfig('postoffice_date');
            } else {
                $results['postOfficeDate'] = '';
            }
            $results['enablePostOffice'] = $giftVoucherHelper->getInterfaceConfig('postoffice');
            $results['displayImageItem'] = $this->_scopeConfig->getValue('giftvoucher/interface_checkout/display_image_item');
        }


        return $results;
    }

}
