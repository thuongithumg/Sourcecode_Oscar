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

class Magestore_Webpos_Model_Api2_Currencies_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**
     * Get countries information
     *
     * @api
     * @return array|null
     */
    public function getList()
    {
        $configurations = array();
        $currencyConfigData = Mage::getModel('webpos/config_currency')->getConfig();
        if(count($currencyConfigData)) {
            foreach($currencyConfigData['currencies'] as $key => $data) {
                if(is_array($data)) {
                    //$data = \Zend_Json::encode($data);
                }
                $configurations[] = $data;
            }
        }

        $result = array(
            'items' => $configurations,
            'total_count' => count($configurations)
        );

        return $result;
    }

    /**
     * change currency
     *
     * @api
     * @return array|null
     */
    public function changeCurrency($params)
    {
        $output = array();
        if(isset($params['currency'])) {
            $currency = $params['currency'];
            $output['priceFormat'] = $this->getPriceFormat(
                null,
                $currency
            );
            $currencyModel = Mage::getModel('directory/currency');
            $baseCurrencyCode = Mage::app()->getBaseCurrencyCode();
            $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
            if ($currency) {
                Mage::app()->getStore()->setCurrentCurrencyCode($currency);
            }
            $currencyRate = $baseCurrency->getRate($currency);
            $isDefault = '0';
            if ($currency == $baseCurrencyCode)
                $isDefault = '1';
            $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
            $currentCurrency = Mage::app()->getLocale()->currency($currentCurrencyCode);
            $currencyModel->setCode($currentCurrencyCode);
            $currencyModel->setCurrencyName($currentCurrency->getName());
            $currencyModel->setCurrencyRate($currencyRate);
            $currencyModel->setCurrencySymbol($currentCurrency->getSymbol());
            $currencyModel->setIsDefault($isDefault);
            $output['currency'] = $currencyModel->getData();
            return Zend_Json::encode($output);
        }
        Mage::throwException($this->__('Cannot change currency'));
    }

    public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            /* Create */
            case self::ACTION_TYPE_COLLECTION . self::OPERATION_RETRIEVE:
                $this->_errorIfMethodNotExist('getList');
                $retrievedData = $this->getList();
                $this->_render($retrievedData);
                break;
            case self::OPERATION_UPDATE . self::OPERATION_CREATE:
                $params = $this->getRequest()->getBodyParams();
                $retrievedData = $this->changeCurrency($params);
                $this->_render($retrievedData);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

    /**
     * Functions returns array with price formatting info for js function
     * formatCurrency in js/varien/js.js
     *
     * @return array
     */
    public function getPriceFormat($locale = null, $currencyCode)
    {
        if(!$locale) {
            $locale = Mage::getModel('core/locale');
        }
        $currency = Mage::getModel('directory/currency')->load($currencyCode);
        $format = Zend_Locale_Data::getContent($locale->getLocaleCode(), 'currencynumber');
        $symbols = Zend_Locale_Data::getList($locale->getLocaleCode(), 'symbols');

        $pos = strpos($format, ';');
        if ($pos !== false){
            $format = substr($format, 0, $pos);
        }
        $format = preg_replace("/[^0\#\.,]/", "", $format);
        $totalPrecision = 0;
        $decimalPoint = strpos($format, '.');
        if ($decimalPoint !== false) {
            $totalPrecision = (strlen($format) - (strrpos($format, '.')+1));
        } else {
            $decimalPoint = strlen($format);
        }
        $requiredPrecision = $totalPrecision;
        $t = substr($format, $decimalPoint);
        $pos = strpos($t, '#');
        if ($pos !== false){
            $requiredPrecision = strlen($t) - $pos - $totalPrecision;
        }
        $group = 0;
        if (strrpos($format, ',') !== false) {
            $group = ($decimalPoint - strrpos($format, ',') - 1);
        } else {
            $group = strrpos($format, '.');
        }
        $integerRequired = (strpos($format, '.') - strpos($format, '0'));

        $result = array(
            'pattern' => $currency->getOutputFormat(),
            'precision' => $totalPrecision,
            'requiredPrecision' => $requiredPrecision,
            'decimalSymbol' => $symbols['decimal'],
            'groupSymbol' => $symbols['group'],
            'groupLength' => $group,
            'integerRequired' => $integerRequired
        );

        return $result;
    }
}
