<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *  
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_User extends Mage_Core_Model_Abstract
{

    const HASH_SALT_LENGTH = 32;
    const MIN_PASSWORD_LENGTH = 7;

    public function _construct()
    {
        parent::_construct();
        $this->_init('webpos/user');
    }

    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    protected function _getEncodedPassword($password)
    {
        return $this->_getHelper('core')->getHash($password, self::HASH_SALT_LENGTH);
    }

    protected function _beforeSave()
    {
        $data = array(
            'username' => $this->getUsername(),
            'display_name' => $this->getDisplayName(),
            'email' => $this->getEmail(),
            'location_id' => $this->getLocationId(),
            'role_id' => $this->getRoleId(),
            'till_ids' => Magestore_Webpos_Model_Till::VALUE_ALL_TILL, //dont't use till anymore
        );
        if ($this->getId() > 0) {
            $data['user_id'] = $this->getId();
        }
        if ($this->getUsername()) {
            $data['username'] = $this->getUsername();
        }
        if ($this->getNewPassword()) {
            $data['password'] = $this->_getEncodedPassword($this->getNewPassword());
        } elseif ($this->getPassword() && !$this->getId()) {
            $data['password'] = $this->_getEncodedPassword($this->getPassword());
        }
        $this->addData($data);
        return parent::_beforeSave();
    }

    /**
     * Processing object after load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        $tillIds = explode(',', $this->getTillIds());
        $this->setTillIds($tillIds);
        return parent::_afterLoad();
    }

    public function userExists()
    {
        $email = $this->getEmail();
        $username = $this->getUsername();
        $check = $this->getCollection()->addFieldToFilter(
            array('email', 'username'),
            array(
                array('like' => "%$email%"),
                array('like' => "%$username%")
            )
        );
        if ($check->getFirstItem()->getId() && $this->getId() != $check->getFirstItem()->getId())
            return true;
        return false;
    }

    public function validate()
    {
        $errors = array();
        if ($this->hasNewPassword()) {
            if (Mage::helper('core/string')->strlen($this->getNewPassword()) < self::MIN_PASSWORD_LENGTH) {
                $errors[] = Mage::helper('adminhtml')->__('Password must be at least of %d characters.', self::MIN_PASSWORD_LENGTH);
            }

            if (!preg_match('/[a-z]/iu', $this->getNewPassword())
                || !preg_match('/[0-9]/u', $this->getNewPassword())
            ) {
                $errors[] = $this->_getHelper('webpos')->__('Password must include both numeric and alphabetic characters.');
            }

            if ($this->hasPasswordConfirmation() && $this->getNewPassword() != $this->getPasswordConfirmation()) {
                $errors[] = $this->_getHelper('webpos')->__('Password confirmation must be same as password.');
            }
        }
        if ($this->userExists()) {
            $errors[] = Mage::helper('adminhtml')->__('A user with the same user name or email aleady exists.');
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    public function loadByUsername($username)
    {
        $info = $this->getCollection()->addFieldToFilter('username', $username);
        if ($id = $info->getFirstItem()->getId())
            $this->load($id);
        return $this;
    }

    public function authenticate($login, $password)
    {
        $this->loadByUsername($login);
        if (($this->getStatus() == '2') || !$this->validatePassword($password)) {
            return false;
        }
        return true;
    }

    public function validatePassword($password)
    {
        $hash = $this->getPassword();
        if (!$hash)
            return false;
        return Mage::helper('core')->validateHash($password, $hash);
    }

    public function getCurrentUserLoggedIn()
    {
        return Mage::getModel('webpos/session')->getUser();
    }

    public function getMaximumDiscountPercent()
    {
        $role = Mage::getModel('webpos/role')->load($this->getCurrentUserLoggedIn()->getRoleId());
        if ($role) {
            return $role->getMaximumDiscountPercent();
        }
        return '';
    }

    public function toOptionArray() {
        $options = array();
        $userCollection = $this->getCollection();
        foreach ($userCollection as $user) {
            $key = $user->getId();
            $value = $user->getDisplayName();
            $options [$key] = $value;
        }
        return $options;
    }

    public function getOptionArray() {
        $options = array();
        $userCollection = $this->getCollection();
        foreach ($userCollection as $user) {
            $key = $user->getId();
            $value = $user->getDisplayName();
            $options [$key] = $value;
        }
        return $options;
    }
}
