<?php
/**
 * Created by Wazza Rooney on 10/31/17 4:26 PM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 10/31/17 4:26 PM
 */

/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 10/31/2017
 * Time: 4:26 PM
 */

class Magestore_Webpos_Block_Adminhtml_User_Renderer_Userlocation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function _getValue(Varien_Object $row)
    {
        $locationIds = explode(',' ,$row->getLocationId());

        if (empty($locationIds)) return '';

        /* @var Magestore_Webpos_Model_Mysql4_Userlocation_Collection $locations */
        $locations = Mage::getModel('webpos/userlocation')->getCollection()
            ->addFieldToFilter('location_id', $locationIds);
        return implode($locations->getColumnValues('display_name'), ', ');
    }

}