<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface as CustomerData;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
/**
 * class \Magestore\Webpos\Model\WebPosSession
 *
 * Web POS session model
 * Methods:
 *  getUser
 *  getUserId
 *  isLoggedIn
 *  login
 *  loginById
 *  logout
 *  regenerateId
 *  setUser
 *  setUserId
 *  setWebPosUserAsLoggedIn
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class WebPosSession extends \Magento\Framework\Session\SessionManager
{
    /**
     * Customer object
     *
     * @var CustomerData
     */
    protected $_customer;
    /**
     * @var ResourceCustomer
     */
    protected $_customerResource;
    /**
     * Customer model
     *
     * @var Customer
     */
    protected $_customerModel;
    /**
     * Flag with customer id validations result
     *
     * @var bool|null
     */
    protected $_isCustomerIdChecked = null;
    /**
     * Customer URL
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    /**
     * Core url
     *
     * @var \Magento\Framework\Url\Helper\Data|null
     */
    protected $_coreUrl = null;
    /**
     * @var Share
     */
    protected $_configShare;
    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;
    /**
     * @var  CustomerRepositoryInterface
     */
    protected $_customerRepository;
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;
    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $_urlFactory;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;
    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $_response;

    /**
     *
     * @var \Magestore\Webpos\Model\PosUserFactory
     */
    protected $_posUserFactory;

    /**
     *
     * @var \Magestore\Webpos\Model\PosUser
     */
    protected $_user;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Framework\Session\ValidatorInterface $validator
     * @param \Magento\Framework\Session\StorageInterface $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Url\Helper\Data $coreUrl
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magestore\Webpos\Model\PosUserFactory $posUserFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Http\Context $httpContext,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\App\Response\Http $response,
        \Magestore\Webpos\Model\Staff\StaffFactory $posUserFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_coreUrl = $coreUrl;
        $this->_urlFactory = $urlFactory;
        $this->_session = $session;
        $this->_eventManager = $eventManager;
        $this->_httpContext = $httpContext;
        $this->_posUserFactory = $posUserFactory;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
        $this->_groupManagement = $groupManagement;
        $this->_response = $response;
        $this->_eventManager->dispatch('webpos_session_init', ['webpos_session' => $this]);
    }
    /**
     *
     * @param \Magestore\Webpos\Model\PosUser $user
     * @return \Magestore\Webpos\Model\WebPosSession
     */
    public function setUser($user) {
        $this->_user = $user;
        $this->setId($user->getId());
        return $this;
    }

    /**
     *
     * @return \Magestore\Webpos\Model\PosUser
     */
    public function getUser() {
        if ($this->_user instanceof \Magestore\Webpos\Model\PosUserFactory) {
            return $this->_user;
        }
        $user = $this->_posUserFactory->create();
        if ($this->getId()) {
            $user->load($this->getId());
        }
        $this->setUser($user);
        return $this->_user;
    }

    /**
     *
     * @param string $id
     * @return \Magestore\Webpos\Model\WebPosSession
     */
    public function setUserId($id) {
        $this->setData('user_id', $id);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getUserId() {
        //return 1;
        if ($this->getData('user_id')) {
            return $this->getData('user_id');
        }
        return ($this->isLoggedIn()) ? $this->getId() : null;
    }

    /**
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return (bool) $this->getId();
    }
    /**
     *
     * @param string $username
     * @param string $password
     * @return string|boolean
     */
    public function login($username, $password) {
        $user = $this->_posUserFactory->create();
        $currentStoreId = $this->_storeManager->getStore(true)->getId();
        if ($user->authenticate($username, $password)) {
            $storeIds = explode(",", $user->getStoreIds());
            if (count($storeIds) > 0 && !in_array(0, $storeIds) && !in_array($currentStoreId, $storeIds)) {
                return "store_error";
            }
            $this->setWebPosUserAsLoggedIn($user);
            return true;
        }
        return false;
    }

    /**
     *
     * @return \Magestore\Webpos\Model\WebPosSession
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->_eventManager->dispatch('webpos_logout', ['webpos' => $this->getUser()]);
            $this->_logout();
        }
        return $this;
    }

    /**
     *
     * @return \Magestore\Webpos\Model\WebPosSession
     */
    protected function _logout()
    {
        $this->_user = null;
        $this->setId(null);
        $this->destroy(['clear_storage' => false]);
        return $this;
    }

    /**
     *
     * @param \Magestore\Webpos\Model\PosUser $user
     * @return \Magestore\Webpos\Model\WebPosSession
     */
    public function setWebPosUserAsLoggedIn($user)
    {
        $this->setUser($user);
        $this->_eventManager->dispatch('webpos_user_login', ['user' => $user]);
        $this->regenerateId();
        return $this;
    }
    /**
     *
     * @param int $webPosId
     * @return boolean
     */
    public function loginById($webPosId)
    {
        try {
            $user = $this->_posUserFactory->create()->load($webPosId);
            $this->setWebPosUserAsLoggedIn($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     *
     * @return \Magestore\Webpos\Model\WebPosSession
     */
    public function regenerateId()
    {
        parent::regenerateId();
        $this->_cleanHosts();
        return $this;
    }
    /**
     *
     * @return string
     */
    protected function _createUrl()
    {
        return $this->_urlFactory->create();
    }
}