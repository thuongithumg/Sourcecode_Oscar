<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Directory\Currency;
/**
 * class \Magestore\Webpos\Model\Currency\CurrencyRepository
 *
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class CurrencyRepository implements \Magestore\Webpos\Api\Directory\Currency\CurrencyRepositoryInterface
{
    /**
     * webpos currency model
     *
     * @var \Magestore\Webpos\Model\Directory\Currency\Currency
     */
    protected $_currencyModel;

    /**
     * webpos currency result interface
     *
     * @var \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyResultInterfaceFactory
     */
    protected $_currencyResultInterface;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magestore\Webpos\Model\Directory\Currency\Currency $currencyModel
     * @param \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyResultInterfaceFactory $currencyResultInterface
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magestore\Webpos\Model\Directory\Currency\Currency $currencyModel,
        \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyResultInterfaceFactory $currencyResultInterface,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_currencyModel= $currencyModel;
        $this->_currencyResultInterface = $currencyResultInterface;
        $this->_currencyFactory = $currencyFactory;
        $this->_localeFormat = $localeFormat;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get currencies list
     *
     * @api
     * @return array|null
     */
    public function getList() {
        $currencyList = $this->_currencyModel->getCurrencyList();
        $currencies = $this->_currencyResultInterface->create();
        $currencies->setItems($currencyList);
        $currencies->setTotalCount(count($currencyList));
        return $currencies;
    }

    /**
     *
     * @param string $currency
     * @return string
     */
    public function changeCurrency($currency)
    {
        $output = array();
        $output['priceFormat'] = $this->_localeFormat->getPriceFormat(
            null,
            $currency
        );
        $currencyFactory = $this->_currencyFactory->create();
        $baseCurrencies = $currencyFactory->getConfigBaseCurrencies();
        $baseCurrencyCode = $baseCurrencies[0];
        $baseCurrency = $this->_currencyFactory->create()->load($baseCurrencyCode);
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        if ($currency) {
            $storeManager->getStore()->setCurrentCurrencyCode($currency);
        }
        $currencyRate = $baseCurrency->getRate($currency);
        $isDefault = '0';
        if($currency == $baseCurrencyCode)
            $isDefault = '1';
        $currentCurrency = $storeManager->getStore()->getCurrentCurrency();
        $this->_currencyModel->setCode($currentCurrency->getCurrencyCode());
        $this->_currencyModel->setCurrencyName($currentCurrency->getCurrencyCode());
        $this->_currencyModel->setCurrencyRate($currencyRate);
        $this->_currencyModel->setCurrencySymbol($currentCurrency->getCurrencySymbol());
        $this->_currencyModel->setIsDefault($isDefault);
        $output['currency'] = $this->_currencyModel->getData();

        return \Zend_Json::encode($output);
    }
}