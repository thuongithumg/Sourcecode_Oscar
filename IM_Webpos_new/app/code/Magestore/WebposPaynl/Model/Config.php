<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Model;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Description of Config
 *
 * @author Andy Pieters <andy@pay.nl>
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigInterface;

    public function __construct(
        ScopeConfigInterface $configInterface
    )
    {
        $this->_scopeConfigInterface = $configInterface;
    }

    public function getApiToken()
    {
        return $this->_scopeConfigInterface->getValue('webpos/payment/paynl/client_id', 'store');
    }

    public function getServiceId()
    {
        return $this->_scopeConfigInterface->getValue('webpos/payment/paynl/client_secret', 'store');
    }

    public function isTestMode()
    {
        return $this->_scopeConfigInterface->getValue('webpos/payment/paynl/enable', 'store') == 1;
    }
    public function isNeverCancel()
    {
        return $this->_scopeConfigInterface->getValue('payment/paynl/never_cancel', 'store') == 1;
    }
    public function getLanguage(){
        $language = $this->_scopeConfigInterface->getValue('payment/paynl/language', 'store');
        return $language?$language:'nl'; //default nl
    }

    public function getPaymentOptionId($methodCode){
        return $this->_scopeConfigInterface->getValue('payment/'.$methodCode.'/payment_option_id', 'store');
    }

    /**
     * Configures the sdk with the API token and serviceId
     *
     * @return bool TRUE when config loaded, FALSE when the apitoken or serviceId are empty
     */
    public function configureSDK(){
        if ($this->validateRequiredSDK()) {
            $apiToken = $this->getApiToken();
            $serviceId = $this->getServiceId();
            if(!empty($apiToken) && !empty($serviceId)){
                \Paynl\Config::setApiToken($apiToken);
                \Paynl\Config::setServiceId($serviceId);
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function validateRequiredSDK(){
        return (class_exists("\\Paynl\\Instore"))?true:false;
    }
}