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
/**
 * Class AbstractBlock
 * @package Magestore\Webpos\Block
 */
class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface 
     */
    protected $_objectManager;
    
    /**
     *
     * @var \Magento\Checkout\Model\CompositeConfigProvider 
     */
    protected $_configProvider;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var array|\Magento\Checkout\Block\Checkout\LayoutProcessorInterface[]
     */
    protected $_layoutProcessors;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magestore\Webpos\Model\Staff\WebPosSessionFactory
     */
    protected $_webposSessionFactory;
    
    /**
     *
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $_permissionHelper;
    
    /**
     *
     * @var \Magestore\Webpos\Model\Config\Config
     */
    protected $_configModel;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Model\WebposConfigProvider\CompositeConfigProvider $configProvider
     * @param \Magestore\Webpos\Model\Config\Config $configModel
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magestore\Webpos\Helper\Permission $permissionHelper
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Model\WebposConfigProvider\CompositeConfigProvider $configProvider,
        \Magestore\Webpos\Model\Config\Config $configModel,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        array $layoutProcessors = [],
        array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->_configProvider = $configProvider;
        $this->_storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_layoutProcessors = $layoutProcessors;
        $this->_cookieManager = $cookieManager;
        $this->_permissionHelper = $permissionHelper;
        $this->_configModel = $configModel;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->_layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return parent::getJsLayout();
    }

    /**
     * Retrieve webpos configuration
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getWebposConfig()
    {
        return $this->_configModel->getConfig();
    }
    
    /**
     * Retrieve webpos color
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getWebposColor()
    {
        $color = $this->_configModel->getConfigByPath('webpos/general/webpos_color');
        if($color)
            return $color;
        return '00A679';
    }
    
    /**
     * 
     * @return tring
     */
    public function getLogoUrl($imageUrl)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            .'webpos/logo/'.$imageUrl;
    }
    
}
