<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff;
/**
 * Class Staff
 * @package Magestore\Webpos\Model\Staff
 */
class Staff extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\Webpos\Api\Data\Staff\StaffListDataInterface
{

    /**
     *
     */
    const PASSWORD = 'password';

    /**
     *
     */
    const USER_NAME = 'username';
    /**
     *
     */
    const MIN_PASSWORD_LENGTH = 7;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;
    /**
     * @var \Magento\User\Helper\Data
     */
    protected $_userData;
    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;
    /**
     * @var \Magento\Framework\Validator\DataObjectFactory
     */
    protected $_validatorObject;

    /**
     * @var \Magestore\Webpos\Model\Staff\RoleFactory
     */
    protected $_roleFactory;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magestore\Webpos\Model\Staff\StaffListData
     */
    protected $staff;

    /**
     * Staff constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\User\Helper\Data $userData
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Framework\Validator\DataObjectFactory $validatorObjectFactory
     * @param \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\User\Helper\Data $userData,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Framework\Validator\DataObjectFactory $validatorObjectFactory,
        \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_userData = $userData;
        $this->_config = $config;
        $this->_validatorObject = $validatorObjectFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_roleFactory = $roleFactory;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Staff\Staff');
    }


    /**
     * @param $password
     * @return string
     */
    public function getEncodedPassword($password)
    {
        return $this->_encryptor->getHash($password, true);
    }


    /**
     * @return $this
     */
    public function beforeSave() {
        $data = array(
            'username' => $this->getUsername(),
            'display_name' => $this->getDisplayName(),
            'email' => $this->getEmail(),
            'location_id' => $this->getLocationId(),
            'role_id' => $this->getRoleId(),
        );
        if ($this->getId() > 0) {
            $data['user_id'] = $this->getId();
        }
        if ($this->getUsername()) {
            $data['username'] = $this->getUsername();
        }
        if ($this->getNewPassword()) {
            $data['password'] = $this->getEncodedPassword($this->getNewPassword());
        } elseif ($this->getPassword() && !$this->getId() && !$this->getNotEncode()) {
            $data['password'] = $this->getEncodedPassword($this->getPassword());
        }
        $this->addData($data);
        return parent::beforeSave();
    }


    /**
     * @return bool
     */
    public function userExists() {
        $username = $this->getUsername();
        $check = $this->getCollection()->addFieldToFilter('username',$username);
        if ($check->getFirstItem()->getId() && $this->getId() != $check->getFirstItem()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public function validate() {
        $errors = array();
        if ($this->hasNewPassword()) {
            if (strlen($this->getNewPassword()) < self::MIN_PASSWORD_LENGTH) {
                $errors[] = __('Password must be at least of %1 characters.', self::MIN_PASSWORD_LENGTH);
            }

            if (!preg_match('/[a-z]/iu', $this->getNewPassword()) || !preg_match('/[0-9]/u', $this->getNewPassword())
            ) {
                $errors[] = __('Password must include both numeric and alphabetic characters.');
            }

            if ($this->hasPasswordConfirmation() && $this->getNewPassword() != $this->getPasswordConfirmation()) {
                $errors[] = __('Password confirmation must be same as password.');
            }
        }
        if ($this->userExists()) {
            $errors[] = __('A user with the same user name already exists.');
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }


    /**
     * @param $username
     * @return $this
     */
    public function loadByUsername($username) {
        $info = $this->getCollection()->addFieldToFilter('username', $username)
                ->addFieldToFilter('status',1);
        if ($id = $info->getFirstItem()->getId())
            $this->load($id);
        return $this;
    }


    /**
     * @param $login
     * @param $password
     * @return bool
     */
    public function authenticate($login, $password) {
        $this->loadByUsername($login);
        if (!$this->validatePassword($password)) {
            return false;
        }
        return true;
    }


    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password) {
        $hash = $this->getPassword();
        if (!$hash)
            return false;
        return $this->_encryptor->validateHash($password, $hash);
    }

    /**
     * Get user name
     *
     * @api
     * @return string|null
     */
    public function getUsername() {
        return $this->getData(self::USER_NAME);
    }

    /**
     * Set user name
     *
     * @api
     * @param string $username
     * @return $this
     */
    public function setUsername($username) {
        $this->setData(self::USER_NAME, $username);
    }
    /**
     * Get password params
     *
     * @api
     * @return string|null
     */
    public function getPassword() {
        return $this->getData(self::PASSWORD);
    }
    /**
     * Set password param
     *
     * @api
     * @param string $customerEmail
     * @return $this
     */
    public function setPassword($password) {
        $this->setData(self::PASSWORD, $password);
    }

    /**
     * Get staff
     *
     * @api
     * @return \Magestore\Webpos\Model\Staff\StaffListData
     */
    public function getStaff()
    {
        if (!$this->staff) {
            $this->staff = $this->load($this->getId());
        }
        return $this->staff;
    }

    /**
     * Get display name
     *
     * @api
     * @return string|null
     */
    public function getDisplayName()
    {
        return $this->getData(self::DISPLAY_NAME);
    }

    /**
     * Set display name
     *
     * @api
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->setData(self::DISPLAY_NAME, $displayName);
    }

    /**
     * Get staff id
     *
     * @api
     * @return string|null
     */
    public function getStaffId()
    {
        return $this->getData(self::STAFF_ID);
    }

    /**
     * Set staff id
     *
     * @api
     * @param string $email
     * @return $this
     */
    public function setStaffId($staffId)
    {
        $this->setData(self::STAFF_ID, $staffId);
    }

    /**
     * Get role
     *
     * @api
     * @return string
     */
    public function getRole()
    {
        $staff = $this->getStaff();
        $roleId = $staff->getRoleId();
        $role = $this->_roleFactory->create()->load($roleId);
        $roleName = '';
        if($role->getDisplayName()) {
            $roleName = $role->getDisplayName();
        }
        return $roleName;
    }

    /**
     * Set role
     *
     * @api
     * @param string $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->setData(self::ROLE, $role);
    }

    /**
     * Get permission
     *
     * @api
     * @return array
     */
    public function getPermission()
    {
        $staff = $this->getStaff();
        $roleId = $staff->getRoleId();
        $resourceAccess = array();
        $authorizeRuleCollection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Staff\AuthorizationRule')
            ->getCollection()
            ->addFieldToFilter('role_id', $roleId);
        foreach ($authorizeRuleCollection as $authorizeRule) {
            $resourceAccess[] = $authorizeRule->getResourceId();
        }
        return $resourceAccess;
    }

    /**
     * Set permission
     *
     * @api
     * @param array $permission\
     * @return $this
     */
    public function setPermission($permission)
    {
        $this->setData(self::PERMISSION, $permission);
    }

    /**
     * Get pin
     *
     * @api
     * @return string
     */
    public function getPin()
    {
        $pin = '';
        if($this->getData(self::PIN)) {
            $pin = $this->encryptPin($this->getData(self::PIN));
        }
        return $pin;
    }

    /**
     * Set pin
     *
     * @api
     * @param string $pin
     * @return $this
     */
    public function setPin($pin)
    {
        $this->setData(self::PIN, $pin);
    }

    public function encryptPin($pin)
    {
        $encryptPin = md5(md5($pin).'zxcpoi');
        return $encryptPin;
    }
}