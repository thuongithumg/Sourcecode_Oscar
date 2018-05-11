<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block;

/**
 * class \Magestore\Webpos\Block\AbstractBlock
 * 
 * Web POS abstract block  
 * Methods:
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Block
 * @module      Webpos
 * @author      Magestore Developer
 */
class Menu extends \Magestore\Webpos\Block\AbstractBlock
{
    public function getStaffName()
    {
        $currentUserModel = $this->_permissionHelper->getCurrentStaffModel();
        if ($currentUserModel->getId()) {
            return $currentUserModel->getDisplayName();
        } else {
            return '';
        }
    }

    public function getLocationName()
    {
        $locationId = $this->_permissionHelper->getCurrentLocation();
        $locationModel = $this->_objectManager->create('Magestore\Webpos\Model\Location\Location')
            ->load($locationId);
        if ($locationModel->getId()) {
            return $locationModel->getDisplayName();
        } else {
            return '';
        }
    }
}
