<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\View;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class BlockToHtmlBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * BlockToHtmlBefore constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block && (
                $block instanceof \Magento\Payment\Block\Transparent\Iframe ||
                $block instanceof \Magento\Authorizenet\Block\Transparent\Iframe
            )){
            $isWebpos = $this->request->getParam('is_webpos');
            $isFromWebpos = $this->request->getParam('controller_action_name');
            if (($isWebpos && $isWebpos == 'webpos') || ($isFromWebpos && $isFromWebpos == 'webpos')) {
                $block->setTemplate('Magestore_Webpos::payment/iframe.phtml');
            }
        }
    }
}