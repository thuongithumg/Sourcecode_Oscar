<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Staff\Acl\AclResource;

use Magento\Framework\App\ObjectManager;

/**
 * Class Provider
 * @package Magestore\Webpos\Model\Staff\Acl\AclResource
 */
class Provider implements ProviderInterface
{
    /**
     * Cache key for ACL roles cache
     */
    const ACL_RESOURCES_CACHE_KEY = 'magestore_webpos_provider_acl_resources_cache';

    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @var TreeBuilder
     */
    protected $_resourceTreeBuilder;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * Provider constructor.
     * @param \Magento\Framework\Config\ReaderInterface $configReader
     * @param \Magento\Framework\Acl\AclResource\TreeBuilder $resourceTreeBuilder
     * @param string $cacheKey
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $configReader,
        \Magento\Framework\Acl\AclResource\TreeBuilder $resourceTreeBuilder,
        $cacheKey = self::ACL_RESOURCES_CACHE_KEY
    ) {
        $this->_configReader = $configReader;
        $this->_resourceTreeBuilder = $resourceTreeBuilder;
        $this->cacheKey = $cacheKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclResources()
    {
        $aclResourceConfig = $this->_configReader->read();
        if (!empty($aclResourceConfig['config']['acl']['resources'])) {
            $tree = $this->_resourceTreeBuilder->build($aclResourceConfig['config']['acl']['resources']);
            return $tree;
        }
        return [];
    }
}
