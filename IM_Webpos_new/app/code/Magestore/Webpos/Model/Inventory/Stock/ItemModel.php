<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Inventory\Stock;

use Magento\CatalogInventory\Api\StockConfigurationInterface as StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface as StockItemRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;

/**
 * Class ItemModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ItemModel extends \Magento\CatalogInventory\Model\Stock\Item
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_quoteSession;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param StockConfigurationInterface $stockConfiguration
     * @param StockRegistryInterface $stockRegistry
     * @param StockItemRepositoryInterface $stockItemRepository
     * @param \Magestore\Webpos\Helper\Data $posHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StockConfigurationInterface $stockConfiguration,
        StockRegistryInterface $stockRegistry,
        StockItemRepositoryInterface $stockItemRepository,
        \Magestore\Webpos\Helper\Data $posHelper,
        \Magento\Framework\App\Request\Http $request, 
        \Magento\Backend\Model\Session\Quote $quoteSession,    
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $customerSession,
            $storeManager,
            $stockConfiguration,
            $stockRegistry,
            $stockItemRepository,
            $resource,
            $resourceCollection,
            $data 
        );
        $this->_helper = $posHelper;
        $this->_request = $request;
        $this->_quoteSession = $quoteSession;
    }
    
    /**
     * Retrieve backorders status
     *
     * @return int
     */
    public function getBackorders()
    {
        if($this->_quoteSession->getQuote()->getInventoryProcessed()) {
            /* after registerProductsSale */
            return parent::getBackorders();
        }
        $isWebpos = $this->_request->getParam('session');
        $backorder = $this->_helper->getStoreConfig('webpos/general/ignore_checkout');
        
        if ($isWebpos && $backorder) {
            $stock = $this->_helper->getModel('Magento\CatalogInventory\Model\Stock');
            return $stock::BACKORDERS_YES_NOTIFY;
        }
        return parent::getBackorders();
    }
    
    /**
     * Retrieve Stock Availability
     *
     * @return bool|int
     */
    public function getIsInStock()
    {
        if($this->_quoteSession->getQuote()->getInventoryProcessed()) {
            /* after registerProductsSale */
            return parent::getIsInStock();
        }        
        $isWebpos = $this->_request->getParam('session');
        $backorder = $this->_helper->getStoreConfig('webpos/general/ignore_checkout');
        if ($isWebpos && $backorder) {
            return true;
        }
        return parent::getIsInStock();
    }
}
