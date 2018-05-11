<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

interface SwatchResultInterface
{
    /**
     * Set swatch list.
     *
     * @api
     * @param anyType
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get swatch list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Catalog\SwatchInterface[]
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
