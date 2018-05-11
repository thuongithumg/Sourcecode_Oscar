<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Rewardpoints;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class WebposApiCustomerListAfter implements ObserverInterface
{

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        return $this;
        if (!$this->_moduleManager->isEnabled('Magestore_Rewardpoints')) {
            return $this;
        }

        $searchResults = $observer->getEvent()->getSearchResults();
        $customerIds = [];
        foreach ($searchResults->getItems() as $customerData) {
            $customerIds[] = $customerData->getId();
        }
        $pointBalances = [];
        $accounts = $this->_objectManager->create('Magestore\Rewardpoints\Model\ResourceModel\Rewardcustomer\Collection')
            ->addFieldToFilter('customer_id', ['in' => $customerIds]);
        if ($accounts->getSize()) {
            foreach ($accounts as $account) {
                $pointBalances[$account->getEntityId()] = $account->getPointBalance();
            }
        }

        foreach ($searchResults->getItems() as $customerData) {
            $customerId = $customerData->getId();
            if (isset($pointBalances[$customerId])) {
                $additionalAttributes = $customerData->getAdditionalAttributes();
                $additionalAttributes['reward_point'] = ['code' => 'rewardpoint', 'value' => $pointBalances[$customerId]];
                $customerData->setAdditionalAttributes($additionalAttributes);
            }
        }
    }
}