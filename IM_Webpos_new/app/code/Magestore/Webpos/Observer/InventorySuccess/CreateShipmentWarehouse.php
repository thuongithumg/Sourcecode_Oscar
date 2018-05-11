<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\InventorySuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class CreateShipmentWarehouse implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface  
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;    
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @var \Magento\Framework\App\RequestInterface 
     */
    protected $request;

    /**
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $posPermission;
        

    /**
     * 
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(  
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\Webpos\Helper\Permission $posPermission
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->request = $request;
        $this->posPermission = $posPermission;
    }
    
    /**
     * Load linked Warehouse from Location of WebPOS Order
     * 
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $warehouse = $observer->getEvent()->getWarehouse();
        $shipmentItem = $observer->getEvent()->getItem();
        
        /* check warehouse_id from post data */
        $postData = $this->request->getParam('shipment');
        $shipWarehouseId = isset($postData['warehouse']) ? $postData['warehouse'] : null;        
        if($shipWarehouseId)
            return $this;
        
        /* if there is no posted warehouse_id, then get warehouse_id from location_id */
            /* get current location */
        $locationId = $this->posPermission->getCurrentLocation();
            /* if $location is null, get location_id from Order */
        if(!$locationId) {
            $order = $shipmentItem->getOrderItem()->getOrder();
            $locationId = $order->getData('location_id');
        }
        if(!$locationId) {
            return $this;
        }
        
        /* get warehouse which is linked to current location */
        $locationMapping = $this->_objectManager->get('\Magestore\InventorySuccess\Api\Warehouse\Location\MappingManagementInterface');
        $warehouseId = $locationMapping->getWarehouseIdByLocationId($locationId);
        if($warehouseId) {
            $warehouse->load($warehouseId);
        }
    }    

}