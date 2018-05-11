<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Limit
 *
 * Sync source model
 * Methods:
 *  toOptionArray
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class DayToShowSessionHistory implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Don\'t show'), 'value' => -1],
            ['label' => __('Last 7 days'), 'value' => 7],
            ['label' => __('Last 15 days'), 'value' => 15],
            ['label' => __('Last 30 days'), 'value' => 30],
//            ['label' => __('Last 90 days'), 'value' => 90],
//            ['label' => __('Last 180 days'), 'value' => 180],
//            ['label' => __('Last 365 days'), 'value' => 365],
            ['label' => __('All Time'), 'value' => 0],
        ];
    }

}