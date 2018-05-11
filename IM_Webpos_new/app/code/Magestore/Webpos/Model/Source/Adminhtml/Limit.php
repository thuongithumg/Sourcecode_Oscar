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
class Limit implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Last week'), 'value' => 7],
            ['label' => __('Last month'), 'value' => 30],
            ['label' => __('Last 3 months'), 'value' => 90],
            ['label' => __('Last 6 months'), 'value' => 180],
            ['label' => __('Last year'), 'value' => 365],
        ];
    }

}