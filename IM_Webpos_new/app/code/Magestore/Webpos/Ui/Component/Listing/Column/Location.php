<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * class \Magestore\Webpos\Ui\Component\Listing\Column\Location
 * 
 * Web POS Location
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
class Location implements OptionSourceInterface
{

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection
     */
    protected $_webposLocation;

    /**
     * Construct
     *
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection $webposLocation
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection $webposLocation
    ) {
        $this->_webposLocation = $webposLocation;
    }

    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $locations = $this->_webposLocation->loadData();
        if (count($locations) > 0) {
            foreach ($locations as $location) {
                $options[$location->getId()] = (string) $location->getDisplayName();
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
