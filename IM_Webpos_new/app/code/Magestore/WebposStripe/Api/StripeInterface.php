<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripe\Api;

/**
 * Interface StripeInterface
 * @package Magestore\WebposStripe\Api
 */
interface StripeInterface
{
    /**
     * @return bool
     */
    public function validateRequiredSDK();

    /**
     * @param string $key
     * @return anyType
     */
    public function getConfig($key = '');
}
