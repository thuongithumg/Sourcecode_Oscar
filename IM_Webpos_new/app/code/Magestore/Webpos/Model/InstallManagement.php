<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model;

use Magestore\Webpos\Api\InstallManagementInterface;

class InstallManagement implements InstallManagementInterface
{
    /**
     *
     * @var \Magestore\Webpos\Model\ResourceModel\InstallManagement
     */
    protected $_resource;

    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\InstallManagement $installManagementResource
    )
    {
        $this->_resource = $installManagementResource;
    }

    /**
     * @inheritdoc
     */
    public function convertSaleItemsData(){
        return $this->_resource->convertSaleItemsData();
    }

    /**
     * @inheritdoc
     */
    public function createIndexTable($type) {
        return $this->_resource->createIndexTable($type);
    }

    /**
     * @inheritdoc
     */
    public function addIndexTableData($type) {
        return $this->_resource->addIndexTableData($type);
    }
}