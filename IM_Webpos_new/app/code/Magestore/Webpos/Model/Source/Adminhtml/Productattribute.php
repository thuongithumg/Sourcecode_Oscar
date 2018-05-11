<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Productattribute
 * 
 * Productattribute source model
 * Methods:
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Productattribute implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $attributes = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection'
        )
        ->addFieldToFilter('frontend_input', ['in' => ['text', 'textarea']]);
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $options[] = array('value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel());
            }
        }
        return $options;
    }

}
