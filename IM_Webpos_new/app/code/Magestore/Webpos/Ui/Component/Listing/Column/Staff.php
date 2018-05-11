<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * class \Magestore\Webpos\Ui\Component\Listing\Column\Staff
 * 
 * Web POS Staff
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
class Staff implements OptionSourceInterface
{

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Staff\Collection
     */
    protected $_webposStaff;

    /**
     * Construct
     *
     * @param \Magestore\Webpos\Model\ResourceModel\Staff\Staff\Collection $webposStaff
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Staff\Staff\Collection $webposStaff
    ) {
        $this->_webposStaff = $webposStaff;
    }

    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $staffs = $this->_webposStaff->loadData();
        if (count($staffs) > 0) {
            foreach ($staffs as $staff) {
                $options[$staff->getId()] = (string) $staff->getDisplayName();
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
