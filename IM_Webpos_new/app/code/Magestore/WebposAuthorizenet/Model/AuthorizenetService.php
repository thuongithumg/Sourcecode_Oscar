<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Model;

class AuthorizenetService implements \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface
{
    /**
     * @var \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface
     */
    protected $authorizenet;

    /**
     * AuthorizenetService constructor.
     * @param \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface $authorizenet
     */
    public function __construct(
        \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface $authorizenet
    ) {
        $this->authorizenet = $authorizenet;
    }

    /**
     * @return bool
     */
    public function isEnable(){
        $hasSDK = $this->authorizenet->validateRequiredSDK();
        $configs = $this->authorizenet->getConfig();
        return ($hasSDK && $configs['enable'] && !empty($configs['transaction_key']) && !empty($configs['api_login']))?true:false;
    }

    /**
     * @return string
     */
    public function getConfigurationError(){
        $message = '';
        $hasSDK = $this->authorizenet->validateRequiredSDK();
        $configs = $this->authorizenet->getConfig();
        if(!$hasSDK){
            $message = __('Authorizenet SDK not found, please go to the configuration to get the instruction to install the SDK');
        }else{
            if($configs['enable']){
                if(empty($configs['transaction_key']) || empty($configs['api_login'])){
                    $message = __('Authorizenet application client id and client secret are required');
                }
            }else{
                $message = __('Authorizenet integration is disabled');
            }
        }
        return $message;
    }

    /**
     * @param string $quoteId
     * @param string $token
     * @param string $amount
     * @return string
     */
    public function finishPayment($quoteId, $token, $amount){
        return $this->authorizenet->completePayment($quoteId, $token, $amount);
    }

    /**
     * @return bool
     */
    public function canConnectToApi(){
        return $this->authorizenet->canConnectToApi();
    }
}
