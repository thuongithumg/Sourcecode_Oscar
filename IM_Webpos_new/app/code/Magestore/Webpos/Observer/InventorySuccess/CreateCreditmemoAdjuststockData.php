<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\InventorySuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class CreateCreditmemoAdjuststockData implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface  
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;    
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @var \Magento\Framework\App\RequestInterface 
     */
    protected $request;    
        
    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory 
     */
    protected $staffFactory;
    
    /**
     *
     * @var type @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    
    /**
     * 
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(  
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->request = $request;
        $this->staffFactory = $staffFactory;
        $this->authSession = $authSession;
    }
    
    /**
     * Load linked Warehouse from Location of WebPOS Order
     * 
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $adjustDataObject = $observer->getEvent()->getAdjuststockData();
        $order = $observer->getEvent()->getOrder();
        if($this->authSession->isLoggedIn()) {
            return $this; 
        }
        if(!$staffId = $order->getData('webpos_staff_id')) 
            return $this;
        
        /* load staff from WebPOS */
        $staff = $this->staffFactory->create()->load($staffId);
        if($staff->getId()) {
            $createdBy = $staff->getUsername();
            $confirmedBy = $staff->getUsername();
        } else {
            $createdBy = 'webpos_staff';
            $confirmedBy = 'webpos_staff';
        }
        
        $adjustDataObject->setData('created_by', $createdBy);
        $adjustDataObject->setData('confirmed_by', $confirmedBy);
        
        return $this;
    }    

}