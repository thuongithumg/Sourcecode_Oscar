<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposAuthorizenet\Controller\Adminhtml\Api;

/**
 * Class Test
 * @package Magestore\WebposAuthorizenet\Controller\Adminhtml\Api
 */
class Test extends \Magestore\WebposAuthorizenet\Controller\Adminhtml\AbstractAction
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
        $isEnable = $this->authorizenetService->isEnable();
        if ($isEnable) {
            $connected = $this->authorizenetService->canConnectToApi();
            $response['success'] = ($connected) ? true : false;
            $response['message'] = ($connected) ? '' : __('Cannot connect to the Authorizenet API');
        } else {
            $message = $this->authorizenetService->getConfigurationError();
            $response['success'] = false;
            $response['message'] = __($message);
        }
        return $this->createJsonResult($response);
    }
}
