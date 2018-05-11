<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\QuoteRepository\Plugin;
class AccessChangeQuoteControl extends \Magestore\Webpos\Model\QuoteRepository\AccessChangeQuoteControl
{
    public function beforeSave(\Magento\Quote\Api\CartRepositoryInterface $subject, \Magento\Quote\Api\Data\CartInterface $quote)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sessionId = $objectManager->get('\Magento\Framework\App\RequestInterface')->getParam('session');
        $currentStaff = $objectManager->get('Magestore\Webpos\Helper\Permission')
            ->authorizeSession($sessionId);
        if (!$currentStaff) {
//            parent::beforeSave($subject, $quote);
        }
    }
}



