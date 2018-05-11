<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Selectorshipping
 * 
 * Selectorpayment source model
 * Methods:
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class SelectorShipping implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => __('All Allowed Shipping')),
            array('value' => 1, 'label' => __('Specific Shipping')),
        );
    }

}
