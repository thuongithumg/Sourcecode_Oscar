<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Api\Synchronization;

/**
 * Interface ProductInterface
 * @package Magestore\Webpos\Api\Synchronization
 */
interface ProductInterface extends SynchronizationInterface {
    /**
     * @return array
     */
    public function getListAttributesProduct();
}