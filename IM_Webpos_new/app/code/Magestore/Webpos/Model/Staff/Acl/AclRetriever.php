<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff\Acl;

class AclRetriever
{
    protected $_roleCollectionFactory;
    protected $_ruleCollectionFactory;
    protected $_logger;
    protected $_aclBuilder;

    public function __construct(
        \Magestore\Webpos\Model\Staff\Acl\Builder $aclBuilder,
        \Magestore\Webpos\Model\ResourceModel\Staff\Role\CollectionFactory $roleCollectionFactory,
        \Magestore\Webpos\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $authorizationRuleCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
        $this->_ruleCollectionFactory = $authorizationRuleCollectionFactory;
        $this->_roleCollectionFactory = $roleCollectionFactory;
        $this->_aclBuilder = $aclBuilder;
    }

    public function getAllowedResourcesByRole($roleId)
    {
        $allowedResources = [];
        $rulesCollection = $this->_ruleCollectionFactory->create();
        /* @var \Magestore\Webpos\Model\ResourceModel\Staff\AuthorizationRule\Collection $rulesCollection*/
        $rulesCollection->getByRoles($roleId)->load();
        /** @var \Magestore\Webpos\Model\Staff\AuthorizationRule $ruleItem */
        foreach ($rulesCollection->getItems() as $ruleItem) {
            $resourceId = $ruleItem->getResourceId();
            $allowedResources[] = $resourceId;
        }

        return $allowedResources;
    } 
}
