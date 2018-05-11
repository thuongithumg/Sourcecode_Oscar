<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Inventory;

/**
 * Interface LocationRepository
 * @api
 */
interface LocationRepositoryInterface
{
    /**
     * Get Current Location
     *
     * @return \Magestore\Webpos\Api\Data\Inventory\LocationInterface
     */
    public function get();    
}