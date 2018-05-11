<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Color
 *
 * Color source model
 * Methods:
 *  toOptionArray
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Color
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Default'), 'value' => '00A679'],
            ['label' => __('Blue'), 'value' => '0E86E2'],
            ['label' => __('Green'), 'value' => '70B000'],
            ['label' => __('Orange'), 'value' => 'FF8500'],
            ['label' => __('Red'), 'value' => 'D01914'],
        ];
    }

}