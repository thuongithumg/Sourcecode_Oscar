<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\WebposConfigProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class OnlineConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $_webposSession;

    protected $_storeManager;

    protected $_objectManager;

    protected $_permissionHelper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory
     */
    protected $posCollectionFactory;

    /**
     * OnlineConfigProvider constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\Webpos\Model\WebPosSession $webPosSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Helper\Permission $permissionHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $posCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\Webpos\Model\WebPosSession $webPosSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $posCollectionFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_webposSession = $webPosSession;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_permissionHelper = $permissionHelper;
        $this->quoteRepository = $quoteRepository;
        $this->posCollectionFactory = $posCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $quote = false;
        $session = $this->_permissionHelper->getCurrentSession();
        if($session){
            $storeId = $this->_permissionHelper->getCurrentStoreId();
            $quoteId = $this->_permissionHelper->getCurrentQuoteId();
            $tillId = $this->_permissionHelper->getCurrentShiftId();
            if($quoteId){
                $quote = $this->quoteRepository->get($quoteId);
            }
        }
        $data = array(
            \Magestore\Webpos\Api\Data\Cart\QuoteInterface::STORE_ID => ($session && $storeId)?$storeId:$this->_storeManager->getStore(true)->getId(),
            \Magestore\Webpos\Api\Data\Cart\QuoteInterface::TILL_ID => ($session)?(($tillId)?$tillId:''):0,
            \Magestore\Webpos\Api\Data\Cart\QuoteInterface::QUOTE_ID => ($quote && $quote->getId())?$quote->getId():'',
            \Magestore\Webpos\Api\Data\Cart\QuoteInterface::CURRENCY_ID => ($quote && $quote->getId())?$quote->getQuoteCurrencyCode():$this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
            \Magestore\Webpos\Api\Data\Cart\QuoteInterface::CUSTOMER_ID => ($quote && $quote->getId() && $quote->getCustomerId())?$quote->getCustomerId():0
        );
        $sections = $this->_scopeConfig->getValue('webpos/online/sections', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // verify online section
        $this->verifyOnlineSection($sections);
        $useOnlineDefault = $this->_scopeConfig->getValue('webpos/online/use_online_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $useCustomOrderId = $this->_scopeConfig->getValue('webpos/general/use_custom_order_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $defaultShipping = $this->_scopeConfig->getValue('webpos/shipping/defaultshipping', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $defaultPayment = $this->_scopeConfig->getValue('webpos/payment/defaultpayment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $requireSessions = $this->_scopeConfig->getValue('webpos/general/enable_session', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $fulFillOnline = $this->_scopeConfig->getValue('webpos/omnichannel/fulfill_online', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data['sections'] = $sections;
        $data['use_online_default'] = $useOnlineDefault;
        $data['use_custom_order_id'] = $useCustomOrderId;
        $data['is_session_required'] = $requireSessions;
        $data['fulfill_online'] = $fulFillOnline;
        $data['available_pos'] = $this->getCurrentAvailablePos();
        $data['cash_values'] = $this->getCurrentPosDenominations();
        $output = [
            'online_data' => $data,
            'default_shipping' => $defaultShipping,
            'default_payment' => $defaultPayment
        ];
        $configObject = new \Magento\Framework\DataObject();
        $configObject->setData($output);
        $output = $configObject->getData();
        return $output;
    }

    protected function verifyOnlineSection(&$sections) {
        if(strpos($sections, 'all') !== false && strlen($sections) != 3) {
            // if section have "all" => only get all on data
            $sections = 'all';
        } elseif (strpos($sections, 'none') !== false && strlen($sections) != 4) {
            // if section have "none" and another => remove "none"
            $sections = str_replace(',none', '', $sections);
        }
    }

    /**
     * @return array
     */
    public function getCurrentAvailablePos(){
        $posCollection = $this->posCollectionFactory->create();
        $availablePosData = [];
        $session = $this->_permissionHelper->getCurrentSession();
        if($session){
            $staffId = $this->_permissionHelper->getCurrentUser();
            if($staffId){
                $availablePos = $posCollection->getAvailablePos($staffId);
                foreach ($availablePos as $pos){
                    $availablePosData[] = $pos->getData();
                }
            }
        }
        return $availablePosData;
    }

    /**
     * @return array
     */
    public function getCurrentPosDenominations(){
        $posCollection = $this->posCollectionFactory->create();
        $denominations = [];
        $session = $this->_permissionHelper->getCurrentSession();
        if($session){
            $staffId = $this->_permissionHelper->getCurrentUser();
            if($staffId){
                $posCollection->addFieldToFilter('staff_id', array('eq' => $staffId));
                $pos = $posCollection->getFirstItem();
                if($pos->getId()){
                    $denominations = $pos->getDenominations();
                }
            }
        }
        return $denominations;
    }
}
