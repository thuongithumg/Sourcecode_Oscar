<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposAuthorizenet\Api;

/**
 * Interface AuthorizenetServiceInterface
 * @package Magestore\WebposAuthorizenet\Api
 */
interface AuthorizenetServiceInterface
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
     * @param string $quoteId
     * @param string $token
     * @param string $amount
     * @return string
     */
    public function finishPayment($quoteId, $token, $amount);

}
