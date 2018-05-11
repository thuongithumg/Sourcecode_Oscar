<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Config_Currency extends Magestore_Webpos_Model_Abstract
{
    public function getConfig()
    {
        $output = array();
        $currency = Mage::getModel('directory/currency');
        $collection = $currency->getConfigAllowCurrencies();
//        $baseCurrencies = $currency->getConfigBaseCurrencies();
//        $baseCurrencyCode = $baseCurrencies[0];
        $baseCurrencyCode = Mage::app()->getBaseCurrencyCode();
        $baseCurrency = $currency->load($baseCurrencyCode);
        $currencyList = array();
        if(count($collection) > 0) {
            foreach ($collection as $code) {
                $currencyRate = $baseCurrency->getRate($code);
                if(!$currencyRate) {
                    continue;
                }
                $currencySymbol= Mage::app()->getLocale()->currency( $code )->getSymbol();
                if(!$currencySymbol) {
                    $currencySymbol = $code;
                }
                $currencyName= Mage::app()->getLocale()->currency( $code )->getName();
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

        if(!empty($currencyList)){
            $output['currencies'] = $currencyList;
        }
        return $output;
    }
}
