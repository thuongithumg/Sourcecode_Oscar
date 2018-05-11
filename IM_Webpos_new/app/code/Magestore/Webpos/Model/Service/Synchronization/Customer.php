<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Service\Synchronization;

/**
 * Class Customer
 * @package Magestore\Webpos\Model\Service\Synchronization
 */
class Customer extends \Magestore\Webpos\Model\Service\Synchronization
    implements \Magestore\Webpos\Api\Synchronization\CustomerInterface
{
    const SYNCHRONIZATION_TYPE = 'customer';

    const SYNCHRONIZATION_CONFIG_LINK = 'ms_webpos/sync_time/customer';

    const SYNCHRONIZATION_CONFIG_UPDATE = 'ms_webpos/process_update/customer';

    const SYNCHRONIZATION_CONFIG_USE = 'webpos/offline/customer_sync_index';

    const SYNCHRONIZATION_TABLE = 'ms_webpos_customer_flat';

    /**
     * @var array
     */
    protected $arrayColumn = [
        'addresses',
        'additional_attributes'
    ];

    /**
     * Prepare synchronization data to update in synchronization table
     *
     * @param $updatedTime
     * @param null $storeId
     * @return array|object|void
     */
    public function prepareSynchronizationData($updatedTime, $storeId = null, $curentPage = 1)
    {
        $customers = [];
        $collection = $this->getUpdatedCollection($updatedTime, $storeId);
        $collection->setPageSize(static::PAGESIZE);
        $collection->setCurPage($curentPage);
        /** @var \Magento\Customer\Model\Customer $customerModel */
        foreach ($collection as $customerModel) {
            $customers[] = $customerModel->getDataModel();
        }
        $result = new \Magento\Framework\DataObject();
        $result->setItems($customers);
        $this->eventManager->dispatch(
            'webpos_api_customer_list_after', ['search_results' => $result]
        );
        $data = $this->convertValue($result->getItems(), 'Magestore\Webpos\Api\Data\Customer\CustomerInterface[]');
        return $data;
    }

    public function getTotalUpdatedData($updatedTime, $storeId = null)
    {
        $collection = $this->getUpdatedCollection($updatedTime, $storeId);
        return $collection->getSize();
    }

    /**
     * @param $updatedTime
     * @param null $storeId
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    public function getUpdatedCollection($updatedTime, $storeId = null)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->customerCollectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection, 'Magestore\Webpos\Api\Data\Customer\CustomerInterface');
        // This is needed to make sure all the attributes are properly loaded
        foreach ($this->customerMetadata->getAllAttributesMetadata() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        // Needed to enable filtering on name as a whole
        $collection->addNameToSelect();
        $collection->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->getSelect()
            ->joinLeft(
                ['ns' => $collection->getTable('newsletter_subscriber')],
                'e.entity_id = ns.customer_id',
                ['ns.subscriber_status']
            )
            ->columns('IFNULL(at_billing_telephone.telephone,"N/A") AS telephone')
            ->columns('CONCAT(e.firstname, " ", e.lastname) AS full_name')
            ->columns('IFNULL(ns.subscriber_status,"0") AS subscriber_status');
        if ($this->isMagentoEnterprise()) {
            $collection->getSelect()->joinLeft(
                ['credit' => $collection->getTable('magento_customerbalance')],
                'e.entity_id = credit.customer_id',
                ['credit.amount']
            )->columns('IFNULL(credit.amount,"0") AS amount');
        }
        $collection->addFieldToFilter('updated_at', ['gteq' => $updatedTime]);
        return $collection;
    }
}