<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Location
 *
 * Web POS Staff source model
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Staff
{
    /**
    /**
     * Model Staff object
     *
     * @var \Magestore\Webpos\Model\Staff\Staff
     */
    protected $_staffModel;

    /**
     * @param \Magestore\Webpos\Model\Staff\Staff $staffModel
     */
    public function __construct(
        \Magestore\Webpos\Model\Staff\Staff $staffModel
    ) {
        $this->_staffModel = $staffModel;
    }

    /**
     * get locations array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $collection = $this->_staffModel->getCollection();
        $staffList = array(0 => __('Please select a staff'));
        if(count($collection) > 0) {
            foreach ($collection as $item) {
                $staffList[$item->getId()] = $item->getDisplayName();
            }
        }
        return $staffList;
    }

}
