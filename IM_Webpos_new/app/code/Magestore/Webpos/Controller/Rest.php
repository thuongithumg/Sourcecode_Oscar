<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\Framework\Webapi\Rest\Response as RestResponse;
use Magento\Framework\Webapi\Rest\Response\FieldsFilter;
use Magento\Webapi\Controller\Rest\Router;

/**
 * Class Rest
 * @package Magestore\Webpos\Controller
 */
class Rest extends \Magento\Webapi\Controller\Rest
{
    const POS_LOCKED = 'pos_locked';

    /**
     * Rewrite to check WebPos Api
     * @throws \Magento\Framework\Exception\AuthorizationException
     */
    protected function checkPermissions()
    {
        $route = $this->getCurrentRoute();
        $aclResource = $route->getAclResources();

        if (!in_array('Magestore_Webpos::webpos', $aclResource)) {
            parent::checkPermissions();
        } else {
            $sessionId = $this->_request->getParam('session');
            /* remove session id from query params */
            $queries = $this->_request->getQuery()->toArray();
            unset($queries['session']);
            $this->_request->getQuery()->fromArray($queries);
            /* authorize by session id */
            $currentStaff = $this->_objectManager->get('Magestore\Webpos\Helper\Permission')
                ->authorizeSession($sessionId);
            $this->logoutCustomer();
            if (!$currentStaff && (!in_array('Magestore_Webpos::login', $aclResource))) {

                parent::checkPermissions();
            } else {
                if (!$this->isAllowWebPosPermission($aclResource)) {
                    $params = ['resources' => implode(', ', $route->getAclResources())];
                    throw new AuthorizationException(
                        __(AuthorizationException::NOT_AUTHORIZED, $params)
                    );
                }
            }
        }
    }

    /**
     * Prepare input data of API request
     *
     * @param array $inputData
     * @return array
     */
    protected function _prepareInputData($inputData)
    {
        /* remove session param */
        if (isset($inputData['session'])) {
            unset($inputData['session']);
        }
        try {
            foreach ($inputData as &$item) {
                if (is_array($item)) {
                    unset($item['session']);
                }

            }
            return $inputData;
        } catch (\Exception $e) {
            return $inputData;
        }
    }


    /**
     * Execute API request
     *
     * @return void
     * @throws AuthorizationException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Webapi\Exception
     */
    protected function processApiRequest()
    {
        $outputData = '';
        $this->validateRequest();
        /** @var array $inputData */
        $inputData = $this->_request->getRequestData();
        /* prepare input data */
        $inputData = $this->_prepareInputData($inputData);
        $route = $this->getCurrentRoute();
        $serviceMethodName = $route->getServiceMethod();
        $serviceClassName = $route->getServiceClass();
        $inputData = $this->paramsOverrider->override($inputData, $route->getParameters());
        $inputParams = $this->serviceInputProcessor->process($serviceClassName, $serviceMethodName, $inputData);
        $service = $this->_objectManager->get($serviceClassName);

        if ($this->_request->getMethod() == 'POST') {
            $urlExeptionLock = ['lockPos', 'unlockPos', 'getCartData', 'checkPos'];
            $continueApi = $this->checkContinueApiRequest();

            if (!$continueApi && !in_array($serviceMethodName, $urlExeptionLock)) {
                $outputData = self::POS_LOCKED;
            }
        }

        if (!$outputData) {
            /** @var \Magento\Framework\Api\AbstractExtensibleObject $outputData */
            $outputData = call_user_func_array([$service, $serviceMethodName], $inputParams);
            $outputData = $this->serviceOutputProcessor->process(
                $outputData,
                $serviceClassName,
                $serviceMethodName
            );
            if ($this->_request->getParam(FieldsFilter::FILTER_PARAMETER) && is_array($outputData)) {
                $outputData = $this->fieldsFilter->filter($outputData);
            }
        }

        $this->_response->prepareResponse($outputData);
    }

    public function checkContinueApiRequest()
    {
        return $this->checkStatusPos();
    }

    public function checkStatusPos()
    {
        $helperPermission = $this->_objectManager->get('Magestore\Webpos\Helper\Permission');
        $currentPosId = $helperPermission->getCurrentPosId();
        if ($currentPosId) {
            $currentPos = $this->_objectManager->create('\Magestore\Webpos\Model\Pos\PosFactory')
                ->create()->load($currentPosId);
            $posStatus = $currentPos->getStatus();
            if ($posStatus == \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @param $resource
     * @return bool
     */
    public function isAllowWebPosPermission($aclResources)
    {
        $allWebPosPermission = $this->_objectManager->get('Magestore\Webpos\Helper\Permission')
            ->getAllCurrentPermission();

        if (in_array('Magestore_Webpos::all', $allWebPosPermission)) {
            return true;
        }

        foreach ($aclResources as $resource) {
            if (!in_array($resource, $allWebPosPermission) && $resource != 'Magestore_Webpos::webpos') {
                return true;
            }
        }
        return true;
    }

    /**
     *
     */
    public function logoutCustomer()
    {
        $isLoggedInCustomer = $this->_objectManager->get('Magento\Customer\Model\Session')
            ->isLoggedIn();
        if ($isLoggedInCustomer) {
            $this->_objectManager->get('Magento\Customer\Model\Session')->logout();
        }
    }
}
