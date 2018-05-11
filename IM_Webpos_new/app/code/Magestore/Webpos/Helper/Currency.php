<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;


class Currency extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Currency cache
     *
     * @var array
     */
    protected $_currencyCache = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Permission constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        $this->_objectManager = $objectManager;
        $this->_currencyFactory = $currencyFactory;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     *
     * @param $amount
     * @param $from \Magento\Directory\Model\Currency
     * @param $to \Magento\Directory\Model\Currency
     */
    public function currencyConvert($amount, $from, $to){
        $converted = $amount;

        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency()->getCode();

        if(($to == $baseCurrencyCode) || ($from == $baseCurrencyCode))
        {
            if($to == $baseCurrencyCode){
                //convert $amount to base currency
                $converted = $this->convertToBase($amount, $from);
            }else{
                //convert $amount from base currency to $to
                $converted = $this->convertFromBase($amount, $to);
            }
        }else{
            //convert $amount to base currency
            $converted = $this->convertToBase($amount, $from);
            //conver $converted to $to
            $converted = $this->convertFromBase($converted, $to);
        }
        return $converted;
    }

    /** convert $amount from baseCurrency
     * @param $amount
     * @param $to
     * @return mixed
     */
    public function convertFromBase($amount, $to){
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency()->getCode();

        //init object for $to currency code. If there is no currency with $to code, return $amount
        if (empty($this->_currencyCache[$to])) {
            $this->_currencyCache[$to] = $this->_currencyFactory->create()->load($to);
            if(!$this->_currencyCache[$to]->getCode()){
                return $amount;
            }
        }

        //init object for $baseCurrencyCode currency code. If there is no currency with $baseCurrencyCode code, return $amount
        if (empty($this->_currencyCache[$baseCurrencyCode])) {
            $this->_currencyCache[$baseCurrencyCode] = $this->_currencyFactory->create()->load($baseCurrencyCode);
            if(!$this->_currencyCache[$baseCurrencyCode]->getCode()){
                return $amount;
            }
        }

        $converted = $this->_currencyCache[$baseCurrencyCode]->convert($amount, $this->_currencyCache[$to]);
        return $converted;
    }

    /** convert $amount from baseCurrency
     * @param $amount
     * @param $to
     * @return mixed
     */
    public function convertToBase($amount, $from){
        if ($amount == 0){
            return 0;
        }
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency()->getCode();

        //init object for $from currency code. If there is no currency with $from code, return $amount
        if (empty($this->_currencyCache[$from])) {
            $this->_currencyCache[$from] = $this->_currencyFactory->create()->load($from);
            if(!$this->_currencyCache[$from]->getCode()){
                return $amount;
            }
        }

        //init object for $baseCurrencyCode currency code. If there is no currency with $baseCurrencyCode code, return $amount
        if (empty($this->_currencyCache[$baseCurrencyCode])) {
            $this->_currencyCache[$baseCurrencyCode] = $this->_currencyFactory->create()->load($baseCurrencyCode);
            if(!$this->_currencyCache[$baseCurrencyCode]->getCode()){
                return $amount;
            }
        }

        $converted = $this->_currencyCache[$baseCurrencyCode]->convert(1/$amount, $this->_currencyCache[$from]);
        $converted = 1 / $converted;

        return $converted;
    }

    /** get base currency code
     * @return mixed
     */
    public function getBaseCurrencyCode(){
        return $this->_storeManager->getStore()->getBaseCurrency()->getCode();
    }

    /**get current currency code
     * @return mixed
     */
    public function getCurrentCurrencyCode(){
        return  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }




}
