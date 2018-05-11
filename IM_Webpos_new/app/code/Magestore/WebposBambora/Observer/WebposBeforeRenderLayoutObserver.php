<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposBambora\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class WebposBeforeRenderLayoutObserver
 * @package Magestore\WebposBambora\Observer
 */
class WebposBeforeRenderLayoutObserver implements ObserverInterface
{
    /**
     * @var \Magestore\WebposBambora\Helper\Data
     */
    protected $helper;

    /**
     * WebposBeforeRenderLayoutObserver constructor.
     * @param \Magestore\WebposBambora\Helper\Data $data
     */
    public function __construct(
        \Magestore\WebposBambora\Helper\Data $data
    ){
        $this->helper = $data;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $resultLayout = $observer->getData('layout');
        if ($this->helper->isEnableBambora()) {
            $resultLayout->addHandle('webpos_bambora');
        }
        return $this;
    }

}