<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain;
/**
 * class \Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain\Collection
 *
 * Web POS Customer Complain Collection resource model
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
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Customer\CustomerComplain',
            'Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain');
    }
}