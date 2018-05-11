<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Denomination;

/**
 * @api
 */
interface DenominationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[] Array of collection items
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\Webpos\Api\Data\Denomination\DenominationInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
