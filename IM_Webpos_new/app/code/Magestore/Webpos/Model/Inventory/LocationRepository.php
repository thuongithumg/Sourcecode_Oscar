<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Inventory;

use \Magento\Framework\ObjectManagerInterface as ObjectManagerInterface;


use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;


/**
 * Class StockItemRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LocationRepository implements \Magestore\Webpos\Api\Inventory\LocationRepositoryInterface {

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface  
     */
    protected $_objectManager;
    
    /**
     *
     * @var \Magento\Framework\App\RequestInterface 
     */
    protected $_request;
    
    /**
     *
     * @var \Magestore\Webpos\Helper\Permission 
     */
    protected $_permissionHelper;
    
    public function __construct(
         \Magento\Framework\App\RequestInterface $request,  
        \Magestore\Webpos\Helper\Permission $permissionHelper,    
        ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_permissionHelper = $permissionHelper;
        $this->_request = $request;
    }
    
    /**
     * @inheritdoc
     */
    public function get() {
        $staff = $this->_permissionHelper->getCurrentStaffModel();
        $location = $this->_objectManager->create('\Magestore\Webpos\Model\Location\Location')->load($staff->getLocationId());
        
        if(!$location->getId()) {
            throw new NoSuchEntityException(__('Location not found!'));
        }
        
        return $location;
    }

}