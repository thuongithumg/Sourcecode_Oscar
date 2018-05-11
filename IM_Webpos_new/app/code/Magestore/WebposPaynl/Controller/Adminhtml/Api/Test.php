<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposPaynl\Controller\Adminhtml\Api;

/**
 * Class Test
 * @package Magestore\WebposPaynl\Controller\Adminhtml\Api
 */
class Test extends \Magestore\WebposPaynl\Controller\Adminhtml\AbstractAction
{
    /**
     * @return \Magento\Framework\Controller\Result\Json $resultJson
     */
    public function execute()
    {
        $response = [
            'url' => '',
            'message' => '',
            'success' => true
        ];
        $isEnable = $this->paypalService->isEnable();
        if ($isEnable) {
            $connected = $this->paypalService->canConnectToApi();
            $response['success'] = ($connected)?true:false;
            $response['message'] = ($connected) ? '' : __('Cannot connect to the Paypal API');
        } else {
            $message = $this->paypalService->getConfigurationError();
            $response['success'] = false;
            $response['message'] = __($message);
        }
        return $this->createJsonResult($response);
    }
}
