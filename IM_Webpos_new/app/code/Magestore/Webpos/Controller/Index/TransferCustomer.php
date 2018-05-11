<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Index;

/**
 * Class Index
 * @package Magestore\Webpos\Controller\Index
 */

class TransferCustomer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $customerResourceModel;

    /**
     * TransferCustomer constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magestore\Webpos\Helper\Data $helper,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
    ){
        parent::__construct($context);
        $this->helper = $helper;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerResourceModel = $customerResourceModel;
    }

    /**
     * Transfer data to POS store
     */
    public function execute()
    {
        $store = $this->helper->getPosStore();
        if($store->getId()){
            $storeId = $store->getId();
            $websiteId = $store->getWebsiteId();
            $customerCollection = $this->customerCollectionFactory->create();
            foreach ($customerCollection as $customer){
                $customer->setData('website_id', $websiteId);
                try{
                    $this->customerResourceModel->save($customer);
                }catch (\Exception $e){
//                    echo $e->getMessage().$customer->getEmail();
                    return $this->getResponse()->setBody($e->getMessage().$customer->getEmail());
                }
            }
            return $this->getResponse()->setBody($customerCollection->getSize());
//            echo $customerCollection->getSize();
//            echo "ok";
        }
    }
}
