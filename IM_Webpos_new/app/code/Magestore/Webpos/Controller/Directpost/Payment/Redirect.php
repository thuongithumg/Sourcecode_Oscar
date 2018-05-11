<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Directpost\Payment;

use Magento\Payment\Block\Transparent\Iframe;

/**
 * Class Redirect
 */
class Redirect extends \Magento\Authorizenet\Controller\Directpost\Payment
{
    /**
     * Retrieve params and put javascript into iframe
     *
     * @return void
     */
    public function execute()
    {
        $redirectParams = $this->getRequest()->getParams();
        $params = [];
        if (!empty($redirectParams['success'])
            && isset($redirectParams['x_invoice_num'])
            && isset($redirectParams['controller_action_name'])
        ) {
            $this->_getDirectPostSession()->unsetData('quote_id');
            $params['success'] = 1;
        }

        if (!empty($redirectParams['error_msg'])) {
            $errorMessage = $redirectParams['error_msg'];
            $incrementId = $redirectParams['x_invoice_num'];
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                try {
                    $quoteRepository = $this->_objectManager->create('Magento\Quote\Api\CartRepositoryInterface');
                    $quote = $quoteRepository->get($order->getQuoteId());
                    $quote->setIsActive(1)->setReservedOrderId(null);
                    $quoteRepository->save($quote);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                }
                $this->_getDirectPostSession()->removeCheckoutOrderIncrementId($incrementId);
                $this->_getDirectPostSession()->unsetData('quote_id');
                $order->registerCancellation($errorMessage)->save();
            }
            $params['error_msg'] = $errorMessage;
        }

        $this->_coreRegistry->register(Iframe::REGISTRY_KEY, $params);
        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false)->renderLayout();
    }
}
