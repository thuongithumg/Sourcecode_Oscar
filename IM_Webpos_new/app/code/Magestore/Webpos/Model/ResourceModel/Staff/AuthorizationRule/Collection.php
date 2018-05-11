<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Staff\AuthorizationRule;

/**
 * class \Magestore\Webpos\Model\ResourceModel\Staff\AuthorizationRule\Collection
 *
 * Web POS Staff Collection resource model
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Staff\AuthorizationRule',
            'Magestore\Webpos\Model\ResourceModel\Staff\AuthorizationRule');
    }

    public function getByRoles($roleId)
    {
        $this->addFieldToFilter('role_id', (int)$roleId);
        return $this;
    }

}