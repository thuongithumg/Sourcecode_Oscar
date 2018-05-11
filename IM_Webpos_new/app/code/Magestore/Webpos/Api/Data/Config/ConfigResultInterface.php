<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Config;

interface ConfigResultInterface
{
    /**
     * Set config list.
     *
     * @api
     * @param anyType
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get config list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Config\ConfigInterface[]
     */
    public function getItems();

    /**
     * Set total count
     *
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount();

}
