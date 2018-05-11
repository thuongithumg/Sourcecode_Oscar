<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Index;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
/**
 * Class Index
 * @package Magestore\Webpos\Controller\ChangeStore
 */
class ChangeStore extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $storeRepository;

    protected $storeManager;

    protected $storeCookieManager;

    protected $httpContext;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Store\Model\Data\StoreConfigFactory $storeConfigFactory,
        StoreCookieManagerInterface $storeCookieManager,
        HttpContext $httpContext,
        StoreRepositoryInterface $storeRepository,
        StoreManagerInterface $storeManager
    ){
        $this->_storeConfigFactory = $storeConfigFactory;
        $this->storeCookieManager = $storeCookieManager;
        $this->httpContext = $httpContext;
        $this->storeRepository = $storeRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        $storeModel = $this->_objectManager->create('Magento\Store\Model\Store');
        $storeCode = $this->getRequest()->getParam('store_code');
        $storeId = $this->getRequest()->getParam('store_id');

        try {
            if($storeId) {
                $store = $this->storeRepository->getActiveStoreById($storeId);
            } else {
                $store = $this->storeRepository->getActiveStoreByCode($storeCode);
            }
            $storeModel = $storeModel->load($store->getId());

        } catch (StoreIsInactiveException $e) {
            $error = __('Requested store is inactive');
        } catch (NoSuchEntityException $e) {
            $error = __('Requested store is not found');
        }
        if (isset($error)) {
            $this->messageManager->addError($error);
            $isSecured = (int)$this->storeManager->getStore()->isCurrentlySecure();
            $urlRedirect = $storeModel->getBaseUrl(UrlInterface::URL_TYPE_WEB, $isSecured).'webpos';
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($urlRedirect);
        }
        $defaultStoreView = $this->storeManager->getDefaultStoreView();
        if ($defaultStoreView->getId() == $store->getId()) {
            $this->storeCookieManager->deleteStoreCookie($store);
        } else {
            $this->httpContext->setValue(Store::ENTITY, $store->getCode(), $defaultStoreView->getCode());
            $this->storeCookieManager->setStoreCookie($store);
        }
        $isSecured = (int)$this->storeManager->getStore()->isCurrentlySecure();
        $urlRedirect = $storeModel->getUrl('webpos');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath($urlRedirect);

    }
}
