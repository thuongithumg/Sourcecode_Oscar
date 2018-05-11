<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\WebposConfigProvider;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DefaultConfigProvider implements ConfigProviderInterface
{
    /**
     * @var array
     */
    protected $_roleTitleArray = [];
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_countryData;

    /**
     * @var
     */
    protected $_webposSession;

    protected $_storeManager;

    protected $_objectManager;

    protected $_permissionHelper;

    protected $_shiftHelper;

    protected $_paymentConfigModel;

    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helperData;

    /**
     * DefaultConfigProvider constructor.
     * @param CheckoutSession $checkoutSession
     * @param LocaleFormat $localeFormat
     * @param \Magento\Directory\Helper\Data $countryData
     * @param \Magestore\Webpos\Model\WebPosSession $webPosSession
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        LocaleFormat $localeFormat,
        \Magento\Directory\Helper\Data $countryData,
        \Magestore\Webpos\Model\WebPosSession $webPosSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magestore\Webpos\Model\Staff\Acl\AclResource\Provider $aclResourceProvider,
        \Magestore\Webpos\Model\Staff\AuthorizationRuleFactory $authorizationRule,
        \Magestore\Webpos\Model\Staff\RoleFactory $role,
        \Magestore\Webpos\Model\Staff\StaffFactory $staff,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        \Magento\Payment\Model\Config $paymentConfigModel,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Webpos\Helper\Data $helperData
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->localeFormat = $localeFormat;
        $this->_countryData = $countryData;
        $this->_webposSession = $webPosSession;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_permissionHelper = $permissionHelper;
        $this->_aclResourceProvider = $aclResourceProvider;
        $this->_authorizationRule = $authorizationRule;
        $this->_roleFactory = $role;
        $this->_staffFactory = $staff;
        $this->_shiftHelper = $shiftHelper;
        $this->_paymentConfigModel = $paymentConfigModel;
        $this->_moduleManager = $moduleManager;
        $this->_helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output['priceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $this->checkoutSession->getQuote()->getQuoteCurrencyCode()
        );
        $output['basePriceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $this->checkoutSession->getQuote()->getBaseCurrencyCode()
        );
        $currentLocation = $this->_permissionHelper->getCurrentLocationObject();
        $output['regionJson'] = $this->_countryData->getRegionJson();
        $output['storeCode'] = $this->_storeManager->getStore(true)->getCode();
        $staffModel = $this->_permissionHelper->getCurrentStaffModel();
        $output['staffId'] = $staffModel->getId();
        $output['staffName'] = $staffModel->getDisplayName();
        $output['customerGroupOfStaff'] = $staffModel->getCustomerGroup();
        $output['defaultRegionCode'] = $this->_objectManager->get('Magestore\Webpos\Helper\Customer')->getDefaultRegionCode();
        $output['defaultRegionLabel'] = $this->_objectManager->get('Magestore\Webpos\Helper\Customer')->getDefaultRegionLabel();
        $output['shiftId'] = $this->_shiftHelper->getCurrentShiftId();
        $output['locationId'] = $this->_permissionHelper->getCurrentLocation();
        $output['posId'] = $this->_permissionHelper->getCurrentPosId();
        $posModel = $this->_objectManager->create('Magestore\Webpos\Model\Pos\Pos')->load($output['posId']);
        $output['posName'] =  $posModel->getData('pos_name');
        $output['allLocationIds'] = $this->_permissionHelper->getAllLocationIds();
        $output['location_name'] = $currentLocation->getDisplayName();
        $output['location_address'] = $currentLocation->getAddress();
        $output['last_offline_order_id'] = $this->_permissionHelper->getCurrentLastOfflineOrderId();
        $output['maximum_discount_percent'] = $this->_permissionHelper->getMaximumDiscountPercent();
        $output['order_sync_time'] = $this->_objectManager->get('Magestore\Webpos\Helper\Data')
            ->getTimeSyncOrder();
        $output['storeView'] = $this->_objectManager->get('Magestore\Webpos\Helper\Data')
            ->getStoreView();
        $output['order_sync_time_period'] = $this->_objectManager->get('Magestore\Webpos\Helper\Data')
            ->getStoreConfig('webpos/offline/order_limit');
        $output['timeoutSession'] = $this->_permissionHelper->getTimeoutSession();
        $output['customerGroup'] = $this->_objectManager->get('Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup')
            ->getAllCustomerByCurrentStaff();
        $output['country'] = $this->_objectManager->get('Magestore\Webpos\Model\Directory\Country\Country')
            ->getList();
        $resourceAccess = array();
        if ($this->_permissionHelper->getCurrentUser()) {
            $staffId = $this->_permissionHelper->getCurrentUser();
            $staffModel = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')
                ->load($staffId);
            $roleId = $staffModel->getRoleId();
            $authorizeRuleCollection = $this->_objectManager->create('Magestore\Webpos\Model\Staff\AuthorizationRule')
                ->getCollection()
                ->addFieldToFilter('role_id', $roleId);
            foreach ($authorizeRuleCollection as $authorizeRule) {
                $resourceAccess[] = $authorizeRule->getResourceId();
            }
        }
        $resourceAccess[] = 'Magestore_Webpos::create_orders';
        $output['staffResourceAccess'] = $resourceAccess;
        $output['currentCurrencyCode'] = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $output['baseCurrencyCode'] = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        $currencySymbol = $output['currentCurrencyCode'];
        if($this->_objectManager->get('Magento\Directory\Model\Currency')->load($output['currentCurrencyCode'])->getCurrencySymbol()) {
            $currencySymbol = $this->_objectManager->get('Magento\Directory\Model\Currency')->load($output['currentCurrencyCode'])->getCurrencySymbol();
        }
        $output['currentCurrencySymbol'] = $currencySymbol;
        $output['cc_types'] = $this->getAvailableTypes();
        $output['cc_months'] = $this->getMonths();
        $output['cc_years'] = $this->getYears();
        $output['can_adjust_stock'] = true;
        //$output['role'] = $this->getRole();

        /* S: Daniel - integration */
        $output['plugins'] = $this->getEnablePlugins();
        $output['plugins_config'] = $this->getEnablePluginsConfig();
        $output['is_allow_to_lock'] = $posModel->getIsAllowToLock();
        /* E: Daniel - integration */

        $output['pos_account_sharing'] = $this->_helperData->getStoreConfig('webpos/security/pos_account_sharing');

        $configObject = new \Magento\Framework\DataObject();
        $configObject->setData($output);
        $this->_objectManager->create('Magento\Framework\Event\ManagerInterface')->dispatch('webpos_config_load_after', ['object_config' => $configObject]);
        $output = $configObject->getData();
        return $output;
    }

    /**
     * @return array
     */
    public function getRole()
    {
        $role = $this->_roleFactory->create()->load($this->_staffFactory->create()->load($this->_permissionHelper->getCurrentUser())->getRoleId());
        $authorizeRuleCollection = $this->_authorizationRule->create()
            ->getCollection()
            ->addFieldToFilter('role_id', $role->getId());
        $text = '';
        $roleResourcesArray = $this->getRoleResourcesArray();
        foreach ($authorizeRuleCollection as $authorizeRule) {
            $text = $text . $roleResourcesArray[$authorizeRule->getResourceId()] . '</br>';
        }
        $roleArray = [];
        $roleArray['name'] = $role->getDisplayName();
        $roleArray['roleInformation'] = $text;
        return $roleArray;
    }

    /**
     * @return array
     */
    public function getRoleResourcesArray()
    {
        $resources = $this->_aclResourceProvider->getAclResources();
        $output = $this->mapResources($resources);
        return $output;
    }

    /**
     * @param array $resources
     * @return array
     */
    public function mapResources(array $resources)
    {
        foreach ($resources as $resource) {
            $this->_roleTitleArray[$resource['id']] = $resource['title'];
            if (isset($resource['children'])) {
                $this->mapResources($resource['children']);
            }
        }
        return $this->_roleTitleArray;
    }

    /**
     * Get availables credit card types
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getAvailableTypes()
    {
        $types = $this->_paymentConfigModel->getCcTypes();
        $availableTypes = array('AE', 'VI', 'MC', 'DI');
        foreach ($types as $code => $name) {
            if (!in_array($code, $availableTypes)) {
                unset($types[$code]);
            }
        }
        $types = ['' => __('')] + $types;
        return $types;
    }

    /**
     * Get credit card expire months
     *
     * @return array
     */
    public function getMonths()
    {
        $months = $this->_paymentConfigModel->getMonths();
        $months = ['0' => __('Month')] + $months;
        return $months;
    }

    /**
     * Get credit card expire years
     *
     * @return array
     */
    public function getYears()
    {
        $years = $this->_paymentConfigModel->getYears();
        $years = ['0' => __('Year')] + $years;
        return $years;
    }

    /**
     * @return array
     */
    public function getEnablePlugins(){
        $plugins = [];
        if ($this->_moduleManager->isEnabled('Magestore_Customercredit')) {
            $plugins[] = 'os_store_credit';
        }
        if ($this->_moduleManager->isEnabled('Magestore_Rewardpoints')) {
            $plugins[] = 'os_reward_points';
        }
        if ($this->_moduleManager->isEnabled('Magestore_Giftvoucher')) {
            $plugins[] = 'os_gift_card';
        }
        if ($this->_moduleManager->isEnabled('Magento_GiftCardAccount')) {
            $plugins[] = 'giftCardAccount';
        }
        if ($this->_helperData->checkMagentoEE()) {
            if ($this->_objectManager->create('\Magento\CustomerBalance\Helper\Data')->isEnabled()) {
                $plugins[] = 'os_storecredit_ee';
            }
        }
        return $plugins;
    }

    /**
     * @return array
     */
    public function getEnablePluginsConfig(){
        $config = [];
        if ($this->_moduleManager->isEnabled('Magestore_Customercredit')) {
            $config['os_store_credit'] = $this->getModuleConfig('Magestore_Customercredit', 'customercredit');
        }
        if ($this->_moduleManager->isEnabled('Magestore_Rewardpoints')) {
            $config['os_reward_points'] = $this->getModuleConfig('Magestore_Rewardpoints', 'rewardpoints');
        }
        if ($this->_moduleManager->isEnabled('Magestore_Giftvoucher')) {
            $config['os_gift_card'] = $this->getModuleConfig('Magestore_Giftvoucher', 'giftvoucher');
        }
        if ($this->_moduleManager->isEnabled('Magento_GiftCard')) {
            $config['m2ee_gift_card'] = $this->getModuleConfig('Magento_GiftCard', 'giftcard');
        }
        return $config;
    }

    /**
     * @return array
     */
    public function getModuleConfig($module, $code)
    {
        $results = [];
        $configs = [];
        $helper = $this->_objectManager->get('Magestore\Webpos\Helper\Data');
        if ($this->_moduleManager->isEnabled($module)) {
            $configs = $helper->getStoreConfig($code);
        }

        /* convert configs to flat path */
        if (count($configs) > 0) {
            foreach ($configs as $index => $subConfigs) {
                foreach ($subConfigs as $subIndex => $value) {
                    $results[$code . '/' . $index . '/' . $subIndex] = $value;
                }
            }
        }
        return $results;
    }
}
