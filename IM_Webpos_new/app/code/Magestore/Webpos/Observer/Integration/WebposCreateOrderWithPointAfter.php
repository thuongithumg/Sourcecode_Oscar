<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Integration;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;



class WebposCreateOrderWithPointAfter implements ObserverInterface
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

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * WebposUseCustomerCreditAfter constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Webpos\Helper\Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        try{
            if (!$this->_moduleManager->isEnabled('Magestore_Rewardpoints')) {
                return $this;
            }
            $order = $observer->getEvent()->getOrder();
            if(isset($order) && $order->getId() && $order->getCustomerId() && $order->getRewardpointsSpent() > 0){
                $customer = $this->_objectManager->create('\Magento\Customer\Model\Customer');
                $resource = $this->_objectManager->create('\Magento\Customer\Model\ResourceModel\Customer');
                $resource->load($customer, $order->getCustomerId());
                $action = $this->_objectManager->create('\Magestore\Rewardpoints\Helper\Action');
                $action->addTransaction('spending_order',
                    $customer,
                    $order
                );
            }
        }catch(\Exception $e){
            $this->helper->addLog($e->getMessage());
        }
    }
}