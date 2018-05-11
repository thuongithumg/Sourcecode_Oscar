<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\Location
 *
 * Web POS Location source model
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Location
{
    /**
    /**
     * model location object
     *
     * @var \Magestore\Webpos\Model\Location\Location
     */
    protected $_locationModel;

    /**
     * @param \Magestore\Webpos\Model\Location\Location $location
     */
    public function __construct(
        \Magestore\Webpos\Model\Location\Location $locationModel
    ) {
        $this->_locationModel = $locationModel;
    }

    /**
     * get locations array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $collection = $this->_locationModel->getCollection();
        $locationList = array(0 => __('Please select a location'));
        if(count($collection) > 0) {
            foreach ($collection as $item) {
                $locationList[$item->getId()] = $item->getDisplayName();
            }
        }
        return $locationList;
    }

}
