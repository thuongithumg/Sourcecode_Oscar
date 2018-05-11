<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Producttaxclass
 * 
 * Producttaxclass source model
 * Methods:
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Producttaxclass implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Options array
     *
     * @var array
     */
    protected $_options;

    /**
     * tax Class
     *
     * @var \Magento\Tax\Model\ResourceModel\TaxClass\Collection
     */
    protected $_taxClassCollection;

    /**
     * @param \Magento\Tax\Model\ResourceModel\TaxClass\Collection $taxClassCollection
     */
    public function __construct(\Magento\Tax\Model\ResourceModel\TaxClass\Collection $taxClassCollection)
    {
        $this->_taxClassCollection = $taxClassCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = $this->_taxClassCollection->addFieldToFilter('class_type', 'PRODUCT')->loadData()->toOptionArray(false);
        $options = $this->_options;
        array_unshift($options, ['value' => '0', 'label' => __('None')]);
        return $options;
    }

}
