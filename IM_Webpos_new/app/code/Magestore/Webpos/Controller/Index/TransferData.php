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

class TransferData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\Webpos\Model\Sales\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResourceModel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResourceModel;

    /**
     * TransferData constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magestore\Webpos\Model\Sales\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magestore\Webpos\Helper\Data $helper,
        \Magestore\Webpos\Model\Sales\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
    ){
        parent::__construct($context);
        $this->helper = $helper;
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderResourceModel = $orderResourceModel;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productResourceModel = $productResourceModel;
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
            $orderCollection = $this->orderCollectionFactory->create();
            \Zend_Debug::dump($orderCollection->getSize());
            foreach ($orderCollection as $order){
                if($order->getData('webpos_staff_id')){
                    \Zend_Debug::dump($order->getData('webpos_staff_id'));
                }
            }

            $productCollection = $this->productCollectionFactory->create();
            foreach ($productCollection as $product){
                $websiteIds = $product->getWebsiteIds();
                $websiteIds = (is_array($websiteIds))?$websiteIds:[];
                if(!in_array($websiteId, $websiteIds)){
                    $websiteIds[] = $websiteId;
                    $product->setWebsiteIds($websiteIds);
                    try{
                        $this->productResourceModel->save($product);
                    }catch (\Exception $e){

                    }
                }
            }
        }
    }
}
