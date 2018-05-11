<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
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

    protected $session;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Model\Session\Quote $quoteSession
    ) {
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
        $this->session = $quoteSession;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $isWebposOrder = $this->session->getData('webpos_order', true);
        if ($isWebposOrder) {
            $order->setCanSendNewEmailFlag(false);
        }
    }
}