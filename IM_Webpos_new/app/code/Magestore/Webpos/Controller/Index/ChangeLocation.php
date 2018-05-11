<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Index;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Index
 * @package Magestore\Webpos\Controller\ChangeStore
 */
class ChangeLocation extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @var \Magestore\Webpos\Model\Shift\ShiftFactory
     */
    protected $shiftFactory;
    /**
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $permissionHelper;

    /**
     * @var  \Magestore\Webpos\Model\ResourceModel\Pos\Pos
     */
    protected $shiftResourceModel;
    /**
     * Cookie manager
     *
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;
    /**
     * @var WebPosSession
     */
    protected $_webPosSessionFactory;

    protected $jsonFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift $shiftResourceModel,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magestore\Webpos\Model\Staff\WebPosSessionFactory $webPosSessionFactory
    ){
        $this->checkoutSession = $checkoutSession;
        $this->jsonFactory = $jsonFactory;
        $this->shiftFactory = $shiftFactory;
        $this->permissionHelper = $permissionHelper;
        $this->shiftResourceModel = $shiftResourceModel;
        $this->_cookieManager = $cookieManager;
        $this->_webPosSessionFactory = $webPosSessionFactory;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $locationId = $this->getRequest()->getParam('location_id');
        $posId = $this->getRequest()->getParam('pos_id');

        $currentSessionId = $this->_cookieManager->getCookie('WEBPOSSESSION');
        $sessionModel = $this->_webPosSessionFactory->create()->load($currentSessionId, 'session_id');
        if ($sessionModel->getId()) {
            $sessionModel->setData('location_id', $locationId);
            $sessionModel->setData('pos_id', $posId);
            $sessionModel->save();
        }
        $currentUserId = $this->permissionHelper->getCurrentUser();
        $posModel = $this->_objectManager->create('Magestore\Webpos\Model\Pos\Pos')->load($posId);
        if ($posModel->getId()) {
            $posModel->setData('staff_id', $currentUserId);
            $posModel->save();
        }
        $openPos = $this->findOpenPos($posId);
        if ($openPos) {
            $shiftModel = $this->shiftFactory->create()->load($openPos);
            $shiftModel->setStaffId($currentUserId);
            try {
                $shiftModel->save();
            } catch (\Exception $e) {

            }
        }
        return $this->jsonFactory->create()->setData(['storeViewId' => $this->getStoreViewId($locationId)]);
    }

    protected function getStoreViewId($locationId) {
        /** @var \Magestore\Webpos\Model\Location\Location $model */
        $model = $this->_objectManager->get('Magestore\Webpos\Model\Location\LocationFactory')
            ->create()->load($locationId);
        if(!$model->getId()) {
            throw new \Exception(__('Location does not exist!'));
        }
        $listStoreView = $this->_objectManager->get('Magestore\Webpos\Helper\Data')
            ->getStoreView();
        $storeViewId = $listStoreView[0]['id']; // default store view id
        if($model->getData('store_id')) {
            $storeViewId = $model->getData('store_id');
        }
        return $storeViewId;
    }

    /**
     */
    public function findOpenPos($posId)
    {
        $openPos = $this->shiftFactory->create()->getCollection()
            ->addFieldToFilter('pos_id', $posId)
            ->addFieldToFilter('status', 0)
            ->getFirstItem();
        return $openPos->getEntityId();
    }
}
