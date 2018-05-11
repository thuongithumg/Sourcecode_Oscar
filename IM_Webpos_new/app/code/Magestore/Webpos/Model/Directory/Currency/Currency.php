<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Directory\Currency;

/**
 * Class Magestore\Webpos\Model\Directory\Currency\Currency
 *
 */
class Currency extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyInterface
{
    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

        /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode(){
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code){
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get name
     *
     * @api
     * @return string
     */
    public function getCurrencyName(){
        return $this->getData(self::CURRENCY_NAME);
    }

    /**
     * Set currency name
     *
     * @api
     * @param string $currencyName
     * @return $this
     */
    public function setCurrencyName($currencyName){
        return $this->setData(self::CURRENCY_NAME, $currencyName);
    }

    /**
     * Get currency rate
     *
     * @api
     * @return string|null
     */
    public function getCurrencyRate(){
        return $this->getData(self::CURRENCY_RATE);
    }

    /**
     * Set currency rate
     *
     * @api
     * @param string $currencyRate
     * @return $this
     */
    public function setCurrencyRate($currencyRate){
        return $this->setData(self::CURRENCY_RATE, $currencyRate);
    }

    /**
     * Get currency symbol
     *
     * @api
     * @return string
     */
    public function getCurrencySymbol(){
        return $this->getData(self::CURRENCY_SYMBOL);
    }

    /**
     * Set currency symbol
     *
     * @api
     * @param string $currencySymbol
     * @return $this
     */
    public function setCurrencySymbol($currencySymbol){
        return $this->setData(self::CURRENCY_SYMBOL, $currencySymbol);
    }

    /**
     * Get is default
     *
     * @api
     * @return string
     */
    public function getIsDefault(){
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * Set is default
     *
     * @api
     * @param string $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault){
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    public function getCurrencyList()
    {
        $currency = $this->_currencyFactory->create();
        $collection = $currency->getConfigAllowCurrencies();
        $baseCurrencies = $currency->getConfigBaseCurrencies();
        if(!isset($baseCurrencies[0])){
            $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrency()->getData('currency_code');
        }else{
            $baseCurrencyCode = $baseCurrencies[0];
        }
        $baseCurrency = $this->_currencyFactory->create()->load($baseCurrencyCode);
        $currencyList = array();
        if(count($collection) > 0) {
            foreach ($collection as $code) {
                $currencyRate = $baseCurrency->getRate($code);
                if(!$currencyRate) {
                    continue;
                }
                $currencySymbol= $code;
                if($this->_localeCurrency->getCurrency($code)->getSymbol()) {
                    $currencySymbol = $this->_localeCurrency->getCurrency($code)->getSymbol();
                }
                $currencyName= $this->_localeCurrency->getCurrency($code)->getName();
                $isDefault = '0';
                if($code == $baseCurrencyCode)
                    $isDefault = '1';
                $currency->setCode($code);
                $currency->setCurrencyName($currencyName);
                $currency->setCurrencySymbol($currencySymbol);
                $currency->setIsDefault($isDefault);
                $currency->setCurrencyRate($currencyRate);
                $currencyList[] = $currency->getData();
            }
        }
        return $currencyList;
    }
}
