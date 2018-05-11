<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\InventorySuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\Webpos\Model\Checkout\Data\ExtensionData;

class GetDefaultScopeId implements ObserverInterface
{

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     *
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
    )
    {
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
        if (!$this->_coreRegistry->registry('webpos_get_product_list') &&
            !$this->_coreRegistry->registry('create_order_webpos')
        ) {
            return $this;
        }

        $scope = $observer->getEvent()->getScope();

        /* get current location */
        $locationId = $this->posPermission->getCurrentLocation();

        if (!$locationId) {
            return $this;
        }
        /* get warehouse which is linked to current location */
        $locationMapping = $this->_objectManager->get('\Magestore\InventorySuccess\Api\Warehouse\Location\MappingManagementInterface');
        $warehouseId = $locationMapping->getWarehouseIdByLocationId($locationId);
        if ($warehouseId) {
            $scope->setData('scope_id', $warehouseId);
        }
    }

}