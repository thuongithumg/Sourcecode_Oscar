<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use Magestore\Webpos\Model\Checkout\Data\CartItem;

/**
 * class \Magestore\Webpos\Helper\Data
 *
 * Web POS Data helper
 * Methods:
 *  formatCurrency
 *  formatDate
 *  formatPrice
 *  getCurrentDatetime
 *  getModel
 *  getObjectManager
 *  getOfflineConfig
 *  getOrderCollection
 *  getStore
 *  getStoreConfig
 *  getWebPosImages
 *  getWebPosLogoSetting
 *  htmlEscape
 *  setTillData
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MAGENTO_EE = 'Enterprise';
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     *
     * @var \Magestore\Webpos\Model\WebPosSession
     */
    protected $_webposSession;

    /**
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    protected $storeRepository;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Model\WebPosSession $webPosSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param  \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Model\WebPosSession $webPosSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    ){
        $this->productMetadata = $productMetadata;
        $this->_storeManager = $storeManager;
        $this->_webposSession = $webPosSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_localeDate = $localeDate;
        $this->_dateTime = $dateTime;
        $this->storeRepository = $storeRepository;
        parent::__construct($context);
    }

    /**
     *
     * @return string
     */
    public function getWebPosImages()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'webpos/logo/' . $this->getWebPosLogoSetting();
    }

    /**
     * @return mixed
     */
    public function getWebPosLogoSetting()
    {
        return $this->scopeConfig->getValue('webpos/general/webpos_logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @return Magento store
     */
    public function getStore(){
        return $this->_storeManager->getStore();
    }

    /**
     *
     * @param string $data
     * @return string
     */
    public function formatCurrency($data){
        $currencyHelper = $this->_objectManager->get('Magento\Framework\Pricing\Helper\Data');
        return $currencyHelper->currency($data, true, false);
    }

    /**
     *
     * @return string
     */
    public function checkMagentoEE() {
        $edition = $this->productMetadata->getEdition();
        if ($edition == self::MAGENTO_EE) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param string $data
     * @return string
     */
    public function formatPrice($data){
        $checkoutHelper = $this->_objectManager->get('Magento\Checkout\Helper\Data');
        return $checkoutHelper->formatPrice($data);
    }

    /**
     *
     * @param string $data
     * @return string
     */
    public function formatDate($data,$format = ''){
        $format = ($format == '')?'M d,Y H:i:s a':$format;
        return $this->_localeDate->date(new \DateTime($data))->format($format);
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @return Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrderCollection(){
        return $this->_objectManager->create('Magento\Sales\Model\ResourceModel\Order\Collection');
    }

    /**
     *
     * @param string $till_id
     */
    public function setTillData($till_id) {
        try {
            if (!$till_id instanceof Magestore\Webpos\Model\Till) {
                $till = $this->_objectManager->create('Magestore\Webpos\Model\Till')->load($till_id);
            } else {
                $till = $till_id;
            }
            $currentTill = $this->_webposSession->getTill();
            if ($till->getId()) {
                $this->_webposSession->setTill($till);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @return string
     */
    public function getCurrentDatetime(){
        return $this->_dateTime->gmtDate();
    }

    /**
     * string class name
     * @return Model
     */
    public function getModel($class){
        return $this->_objectManager->get($class);
    }

    /**
     *
     * @param string $str
     * @return string
     */
    public function htmlEscape($str){
        return htmlspecialchars($str);
    }


    /**
     * get config account share
     * @return string
     */
    public function getAccountShare() {
        return $this->getStoreConfig('customer/account_share/scope');
    }

    /**
     *
     * @return array
     */
    public function getOfflineConfig() {
        $configData = array();
        $configItems = array('offline/enable',
            'offline/network_check_interval',
            'offline/check_stock_interval',
            'offline/data_load_interval',
            'offline/product_per_request',
            'offline/customer_per_request',
            'product_search/search_offline'
        );
        foreach ($configItems as $configItem) {
            $config = explode('/', $configItem);
            $value = $config[1];
            $configData[$value] = $this->getStoreConfig('webpos/' . $configItem);
        }
        if (empty($configData['data_load_interval']))
            $configData['data_load_interval'] = 0;
        if (empty($configData['product_per_request']))
            $configData['product_per_request'] = 50;
        if (empty($configData['customer_per_request']))
            $configData['customer_per_request'] = 100;
        return $configData;
    }

    /**
     *
     * @return \Magento\Framework\App\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return string
     */
    public function getMaximumDiscountPercent() {
        $role = $this->_objectManager->get('Magestore\Webpos\Model\Role')
            ->load($this->getCurrentUserLoggedIn()->getRoleId());
        if ($role) {
            return $role->getMaximumDiscountPercent();
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function getCurrentUserLoggedIn() {
        return $this->_webposSession->getUser();
    }

    /**
     * @return string
     */
    public function getCustomSaleProductId(){
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $productId = $product->getIdBySku(CartItem::CUSTOM_SALE_PRODUCT_SKU);
        if(!$productId){
            $productId = $this->createCustomSale();
        }
        return $productId;
    }

    /**
     *
     * @return string
     */
    public function getCustomSaleProductSku(){
        return CartItem::CUSTOM_SALE_PRODUCT_SKU;
    }

    /**
     *
     * @return string
     */
    public function createCustomSale(){
        $product = $this->getModel('\Magento\Catalog\Model\Product');
        $entityAttributeSetCollectionFactory = $this->getModel('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory');
        $websiteCollectionFactory = $this->getModel('\Magento\Store\Model\ResourceModel\Website\CollectionFactory');

        $entityType = $product->getResource()->getEntityType();
        $attributeSet = $entityAttributeSetCollectionFactory->create()
            ->setEntityTypeFilter($entityType->getId())
            ->getFirstItem();
        $websiteIds = $websiteCollectionFactory->create()
            ->addFieldToFilter('website_id', array('neq' => 0))
            ->getAllIds();
        $product->setAttributeSetId($attributeSet->getId())
            ->setTypeId('customsale')
            ->setStoreId(0)
            ->setSku(CartItem::CUSTOM_SALE_PRODUCT_SKU)
            ->setWebsiteIds($websiteIds)
            ->setStockData(array(
                'manage_stock' => 0,
                'use_config_manage_stock' => 0,
            ));

        $product->addData(array(
            'name' => 'Custom Sale',
            'weight' => 1,
            'status' => 1,
            'visibility' => 1,
            'price' => 0,
            'description' => 'Custom Sale for POS system',
            'short_description' => 'Custom Sale for POS system',
        ));


        if (!is_array($errors = $product->validate())) {
            try {
                $product->save();
                if (!$product->getId()) {
                    $lastProduct = $this->getProductModel()->getCollection()->setOrder('entity_id', 'DESC')->getFirstItem();
                    $lastProductId = $lastProduct->getId();
                    $product->setName('Custom Sale')->setId($lastProductId + 1)->save();
                    $this->getProductModel()->load(0)->delete();
                }
                return $product->getId();
            } catch (\Exception $e) {
                return $this;
            }
        }
    }

    /**
     * @return string
     */
    public function getTimeSyncOrder()
    {
        $config = $this->getStoreConfig('webpos/offline/order_limit');
        $sourceOption = $this->_objectManager->get('Magestore\Webpos\Model\Source\Adminhtml\Limit')
            ->toOptionArray();
        $limitTitle = '';
        foreach ($sourceOption as $value) {
            if (isset($value['value'])  && $value['value'] == $config) {
                $limitTitle = $value['label'];
            }
        }
        return $limitTitle;
    }

    public function getWebposLogo()
    {
        return $this->getStoreConfig('webpos/general/webpos_logo');
    }

    /**
     * @param $message
     * @param string $type
     */
    public function addLog($message, $type = ''){
        switch ($type){
            case 'info':
                $this->_logger->info($message);
                break;
            case 'debug':
                $this->_logger->debug($message);
                break;
            case 'info':
                $this->_logger->info($message);
                break;
            case 'notice':
                $this->_logger->notice($message);
                break;
            case 'warning':
                $this->_logger->warning($message);
                break;
            case 'error':
                $this->_logger->error($message);
                break;
            case 'emergency':
                $this->_logger->emergency($message);
                break;
            case 'critical':
                $this->_logger->critical($message);
                break;
            case 'alert':
                $this->_logger->alert($message);
                break;
            default:
                $this->_logger->error($message);
                break;
        }
    }

    /**
     *
     * @return Magento store
     */
    public function getStoreManager(){
        return $this->_storeManager;
    }

    public function getStoreView() {
        $stores = [];
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getCode() != 'admin') {
                $stores[] = array(
                    'id' => $store->getId(),
                    'value' => $store->getCode(),
                    'text' => $store->getName()
                );
            }
        }
        return $stores;
    }

    /**
     * Set secure url checkout is secure for current store.
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _getUrl($route, $params = [])
    {
        $params['_type'] = \Magento\Framework\UrlInterface::URL_TYPE_LINK;
        if (isset($params['is_secure'])) {
            $params['_secure'] = (bool)$params['is_secure'];
        } elseif ($this->_storeManager->getStore()->isCurrentlySecure()) {
            $params['_secure'] = true;
        }
        return parent::_getUrl($route, $params);
    }

    /**
     * Retrieve url
     *
     * @param array $params
     * @return string
     */
    public function getUrl($route, $params)
    {
        return $this->_getUrl($route, $params);
    }

    /**
     * @return mixed
     */
    public function getPosStore(){
        return false;
        return $this->_storeManager->getStore('pos');
    }

    /**
     * @return bool
     */
    public function isGiftcardRebuild(){
        $isGiftcardRebuild = false;
        $moduleEnabled = $this->isModuleOutputEnabled('Magestore_Giftvoucher');
        if($moduleEnabled){
            $helperData = $this->getModel('\Magestore\Giftvoucher\Helper\Data');
            if(method_exists($helperData, 'isRebuildVersion')){
                if($helperData->isRebuildVersion()){
                    $isGiftcardRebuild = true;
                }
            }
        }
        return $isGiftcardRebuild;
    }

    /**
     * Dispatch event
     * @param string $eventName
     * @param array $params
     * @return $this
     */
    public function dispatchEvent($eventName, $params = []){
        $this->_eventManager->dispatch($eventName, $params);
        return $this;
    }
}