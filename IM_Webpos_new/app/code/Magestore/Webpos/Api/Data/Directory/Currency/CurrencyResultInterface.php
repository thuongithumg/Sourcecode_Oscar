<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Directory\Currency;

interface CurrencyResultInterface
{
    /**
     * Set currencies list.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get currencies list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyInterface[]
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
