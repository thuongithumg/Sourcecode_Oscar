<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml\Online;

/**
 * Class Sections
 * @package Magestore\Webpos\Model\Source\Adminhtml\Online
 */
class Sections implements \Magento\Framework\Option\ArrayInterface {

    /**
     * @var array
     */
    protected $_options;

    /**
     * Sections constructor.
     */
    public function __construct() {
        $this->_options = [
            'checkout' => __('Checkout'),
            'products' => __('Product Search'),
            'customers' => __('Customer Search'),
            'stocks' => __('Stock Search'),
            'orders' => __('Order Search'),
            'categories' => __('Categories'),
            'sessions' => __('Session Management')
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => 'all', 'label' => __('All')];
        foreach ($this->_options as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }
        $options[] = ['value' => 'none', 'label' => __('None')];
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $options = ['all' => __('All')];
        foreach ($this->_options as $value => $label) {
            $options[$value] = $label;
        }
        $options[] = ['value' => 'none', 'label' => __('None')];
        return $options;
    }

}
