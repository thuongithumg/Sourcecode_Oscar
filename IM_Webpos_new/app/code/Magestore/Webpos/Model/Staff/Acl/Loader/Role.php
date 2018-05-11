<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff\Acl\Loader;

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\Acl\Role\User as RoleUser;
use Magento\Framework\App\ResourceConnection;

class Role extends \Magento\Authorization\Model\Acl\Loader\Role
{

    public function populateAcl(\Magento\Framework\Acl $acl)
    {
        $webposRoleTableName = $this->_resource->getTableName('webpos_authorization_role');
        $connection = $this->_resource->getConnection();

        $select = $connection->select()->from($webposRoleTableName)->order('tree_level');

        foreach ($connection->fetchAll($select) as $webposRole) {
            $parent = $webposRole['parent_id'] > 0 ? $webposRole['parent_id'] : null;
            switch ($webposRole['role_type']) {
                case RoleGroup::ROLE_TYPE:
                    $acl->addRole($this->_groupFactory->create(['roleId' => $webposRole['role_id']]), $parent);
                    break;

                case RoleUser::ROLE_TYPE:
                    if (!$acl->hasRole($webposRole['role_id'])) {
                        $acl->addRole($this->_roleFactory->create(['roleId' => $webposRole['role_id']]), $parent);
                    } else {
                        $acl->addRoleParent($webposRole['role_id'], $parent);
                    }
                    break;
            }
        }
    }
}
