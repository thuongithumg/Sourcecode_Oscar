<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Directory\Country;

/**
 * Class Magestore\Webpos\Model\Directory\Country\Country
 *
 */
class Country extends \Magento\Framework\Model\AbstractModel

{
    /**
     * webpos country result interface
     *
     * @var \Magestore\Webpos\Api\Data\Directory\Country\CountryResultInterfaceFactory
     */
    protected $_countryResultInterface;

    /**
     * @var \Magento\Directory\Model\Data\RegionInformationFactory
     */
    protected $regionInformationFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Api\Data\Directory\Country\CountryResultInterfaceFactory $countryResultInterface
     * @param \Magento\Directory\Model\Data\RegionInformationFactory $regionInformationFactory
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Api\Data\Directory\Country\CountryResultInterfaceFactory $countryResultInterface,
        \Magento\Directory\Model\Data\RegionInformationFactory $regionInformationFactory,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_countryResultInterface = $countryResultInterface;
        $this->regionInformationFactory = $regionInformationFactory;
        $this->directoryHelper = $directoryHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get countries information
     *
     * @api
     * @return array|null
     */
    public function getList()
    {
        $countriesInfo = [];
        $store = $this->_storeManager->getStore();
        $storeLocale = $this->_scopeConfig->getValue(
            'general/locale/code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $store->getCode()
        );
        $countries = $this->directoryHelper->getCountryCollection();
        $regions = $this->directoryHelper->getRegionData();
        foreach ($countries as $data) {
            $countryModel = $this->getCountryInfoForApi($data, $regions, $storeLocale);
            $countriesInfo[] = $countryModel->getData();
        }

        return $countriesInfo;
    }

    /**
     * Creates and initializes the information for \Magento\Directory\Model\Data\CountryInformation
     *
     * @param \Magento\Directory\Model\ResourceModel\Country $country
     * @param array $regions
     * @param string $storeLocale
     * @return \Magestore\Webpos\Model\Directory\Country\Country
     */
    public function getCountryInfoForApi($country, $regions, $storeLocale)
    {
        $countryId = $country->getCountryId();
        $countryModel = $this;
        $countryModel->unsetData('regions');
        $countryModel->setCountryId($countryId);
        $countryModel->setCountryName($country->getName($storeLocale));
        if (array_key_exists($countryId, $regions)) {
            $regionsInfo = [];
            foreach ($regions[$countryId] as $id => $regionData) {
                $regionInfo = $regionData;
                $regionInfo['id'] = $id;
                $regionsInfo[] = $regionInfo;
            }
            $countryModel->setRegions($regionsInfo);
        }

        return $countryModel;
    }
}
