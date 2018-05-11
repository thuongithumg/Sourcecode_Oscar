<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripe\Model;

class StripeService implements \Magestore\WebposStripe\Api\StripeServiceInterface
{
    /**
     * @var \Magestore\WebposStripe\Api\StripeInterface
     */
    protected $stripe;

    /**
     * StripeService constructor.
     * @param \Magestore\WebposStripe\Api\StripeInterface $stripe
     */
    public function __construct(
        \Magestore\WebposStripe\Api\StripeInterface $stripe
    ) {
        $this->stripe = $stripe;
    }

    /**
     * @return bool
     */
    public function isEnable(){
        $hasSDK = $this->stripe->validateRequiredSDK();
        $configs = $this->stripe->getConfig();
        return ($hasSDK && $configs['enable'] && !empty($configs['publishable_key']) && !empty($configs['api_key']))?true:false;
    }

    /**
     * @return string
     */
    public function getConfigurationError(){
        $message = '';
        $hasSDK = $this->stripe->validateRequiredSDK();
        $configs = $this->stripe->getConfig();
        if(!$hasSDK){
            $message = __('Stripe SDK not found, please go to the configuration to get the instruction to install the SDK');
        }else{
            if($configs['enable']){
                if(empty($configs['publishable_key']) || empty($configs['api_key'])){
                    $message = __('Stripe application client id and client secret are required');
                }
            }else{
                $message = __('Stripe integration is disabled');
            }
        }
        return $message;
    }

    /**
     * @param string $token
     * @param string $amount
     * @return string
     */
    public function finishAppPayment($token, $amount){
        return $this->stripe->completeAppPayment($token, $amount);
    }

    /**
     * @return bool
     */
    public function canConnectToApi(){
        return $this->stripe->canConnectToApi();
    }
}
