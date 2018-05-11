<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposAuthorizenet\Api;

/**
 * Interface AuthorizenetInterface
 * @package Magestore\WebposAuthorizenet\Api
 */
interface AuthorizenetInterface
{
    /**
     * @return bool
     */
    public function validateRequiredSDK();

    /**
     * test connect authorizenet API
     *
     * @return bool
     */
    public function canConnectToApi();

    /**
     * @param string $key
     * @return anyType
     */
    public function getConfig($key = '');

    /**
     * @param string $quoteId
     * @param string $token
     * @param string $amount
     *
     * @return string
     * @throws \Exception
     */
    public function completePayment($quoteId, $token, $amount);
}
