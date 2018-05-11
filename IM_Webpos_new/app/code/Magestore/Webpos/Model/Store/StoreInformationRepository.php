<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Store;

class StoreInformationRepository implements \Magestore\Webpos\Api\Store\StoreRepositoryInterface
{
    /**
     * webpos shipping source model
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeRepository;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get shippings list
     *
     * @api
     * @return array|null
     */
    public function getStoreInformation() {
        $storeViewList = $this->storeManager->getStores();
        $stores = [];
        foreach ($storeViewList as $storeView) {
            $stores[] = $storeView->getData();
        }

        $data['stores'] = $stores;
        return \Zend_Json::encode($data);
    }
}