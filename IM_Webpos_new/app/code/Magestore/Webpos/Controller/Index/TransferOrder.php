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

class TransferOrder extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResourceModel;

    /**
     * TransferOrder constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magestore\Webpos\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
    ){
        parent::__construct($context);
        $this->helper = $helper;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderResourceModel = $orderResourceModel;
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
            foreach ($orderCollection as $order){
                $order->setData('store_id', $storeId);
                try{
                    $this->orderResourceModel->save($order);
                }catch (\Exception $e){
//                    echo $e->getMessage();
                    return $this->getResponse()->setBody($e->getMessage());
                }
            }
            return $this->getResponse()->setBody($orderCollection->getSize());
//            echo $orderCollection->getSize();
//            echo "ok";
        }
    }
}
