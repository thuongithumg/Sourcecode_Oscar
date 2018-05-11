<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Sync
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
class Sync implements \Magento\Framework\Option\ArrayInterface
{
   /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('None'), 'value' => 0],
            ['label' => __('1 min'), 'value' => 1],
            ['label' => __('5 mins'), 'value' => 5],
            ['label' => __('10 mins'), 'value' => 10],
            ['label' => __('30 mins'), 'value' => 30],
            ['label' => __('1 hour'), 'value' => 60],
            ['label' => __('2 hours'), 'value' => 120],
            ['label' => __('4 hours'), 'value' => 240],
            ['label' => __('8 hours'), 'value' => 480],
            ['label' => __('Daily'), 'value' => 1440],
            ['label' => __('Weekly'), 'value' => 10080],
        ];
    }

}