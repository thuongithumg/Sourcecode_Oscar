<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml;

/**
 * class \Magestore\Webpos\Block\Adminhtml\AbstractBlock
 * 
 * Web POS adminhtml abstract block  
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Adminhtml
 * @module      Webpos
 * @author      Magestore Developer
 */
/**
 * Class AbstractBlock
 * @package Magestore\Webpos\Block\Adminhtml
 */
class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface 
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    
    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->_messageManager = $messageManager;
        $this->_storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     *
     * @param string $path
     * @return URL
     */
    public function getPosUrl($path)
    {
        return $this->getUrl($path, array('_forced_secure' => $this->getRequest()->isSecure()));
    }
    
    /**
    * 
    * @return Magestore\Webpos\Helper
    */
    public function getHelper($name = ""){
        $helperClass = "";
        switch($name){
           case "config": 
               $helperClass = "Magestore\Webpos\Helper\Config";
               break;
           case "customer": 
               $helperClass = "Magestore\Webpos\Helper\Customer";
               break;
           case "product": 
               $helperClass = "Magestore\Webpos\Helper\Product";
               break;
           case "receipt": 
               $helperClass = "Magestore\Webpos\Helper\Receipt";
               break;
           case "permission": 
               $helperClass = "Magestore\Webpos\Helper\Permission";
               break;
           case "webposuser": 
               $helperClass = "Magestore\Webpos\Helper\WebposUser";
               break;
           case "user": 
               $helperClass = "Magestore\Webpos\Helper\User";
               break;
           case "payment": 
               $helperClass = "Magestore\Webpos\Helper\Payment";
               break;
           case "order": 
               $helperClass = "Magestore\Webpos\Helper\Order";
               break;
           default: 
               $helperClass = "Magestore\Webpos\Helper\Data";
               break;
        }
        return $this->_objectManager->get($helperClass);
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->getHelper()->getStoreConfig($path);
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
     * @param string $path
     * @return URL
     */
    public function getUrlWithParams($path,$params)
    {
        $params['_forced_secure'] = $this->getRequest()->isSecure();
        return $this->getUrl($path, $params);
    }
    
    /**
     * 
     * @return Locale
     */
    public function getLocale() {
        return $this->_localeDate;
    }
    
    /**
     * 
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager() {
        return $this->_objectManager;
    }
    
    /**
     * 
     * @param string $class
     * @return json
     */
    public function getJsInitData($class,$dataType = 'json'){
        $params = array();
        switch ($class){
            case "webposStorage":
                $offlineConfig = $this->getHelper()->getOfflineConfig();
                $params['config'] = array();
                $params['config']['show_outofstock'] = $this->getStoreConfig('webpos/general/show_product_outofstock');
                $params['config']['defaultCustomerConfig'] = $this->getHelper('customer')->getAllDefaultCustomerInfoJson();
                $params['config']['enableOffline'] = $offlineConfig['enable'];
                $params['config']['enable_till'] = $this->getStoreConfig('webpos/general/enable_tills');
                $params['config']['dataLoadInterval'] = $offlineConfig['data_load_interval'];
                $params['config']['networkCheckInterval'] = $offlineConfig['network_check_interval'];
                $params['config']['productPerRequest'] = $offlineConfig['product_per_request'];
                $params['config']['customerPerRequest'] = $offlineConfig['customer_per_request'];
                $params['config']['useLocalSearch'] = $offlineConfig['search_offline'];
                $params['config']['check_stock_interval'] = $offlineConfig['check_stock_interval'];
                break;
            case "webposSalesReport":
                $params['s_report_url']= $this->getPosUrl('webposadmin/report/filterSales');
                break;
        }
        return ($dataType == 'json')?\Zend_Json::encode($params):$params;
    }
    
}
