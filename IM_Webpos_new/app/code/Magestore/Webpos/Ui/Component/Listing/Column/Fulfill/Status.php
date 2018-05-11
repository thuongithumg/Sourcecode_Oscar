<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\Component\Listing\Column\Fulfill;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * class \Magestore\Webpos\Ui\Component\Listing\Column\Status
 *
 * Web POS Status Actions
 * Methods:
 *  toOptionArray
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Ui\Component\Listing\Column
 * @module      Webpos
 * @author      Magestore Developer
 */
class Status implements OptionSourceInterface
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Yes'),'value' => 1],
            ['label' => __('No'),'value' => 0],
        ];
    }
}
