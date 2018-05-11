<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * class \Magestore\Webpos\Ui\Component\Listing\Column\Role
 * 
 * Web POS Role
 * Methods:
 *  getOptionArray
 *  getOptions
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Ui\Component\Listing\Column
 * @module      Webpos
 * @author      Magestore Developer
 */
class Role implements OptionSourceInterface
{

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection
     */
    protected $_roleCollection;

    /**
     * 
     * @param \Magestore\Webpos\Model\ResourceModel\Staff\Role\Collection $roleCollection
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Staff\Role\Collection $roleCollection
    ) {
        $this->_roleCollection = $roleCollection;
    }

    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $roles = $this->_roleCollection->loadData();
        if (count($roles) > 0) {
            foreach ($roles as $role) {
                $options[$role->getId()] = (string) $role->getDisplayName();
            }
        }
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

    /**
     * Get product type labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

}
