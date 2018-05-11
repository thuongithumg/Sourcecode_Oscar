<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripe\Api;

/**
 * Interface StripeServiceInterface
 * @package Magestore\WebposStripe\Api
 */
interface StripeServiceInterface
{
    /**
     * @return bool
     */
    public function isEnable();

    /**
     * @return string
     */
    public function getConfigurationError();

    /**
     * @return bool
     */
    public function canConnectToApi();

    /**
     * @param string $token
     * @param string $amount
     * @return string
     */
    public function finishAppPayment($token, $amount);

}
