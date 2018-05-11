<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block;

/**
 * class \Magestore\Webpos\Block\AbstractBlock
 *
 * Web POS abstract block
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Block
 * @module      Webpos
 * @author      Magestore Developer
 */


/**
 * Class Login
 * @package Magestore\Webpos\Block
 */
class Login extends \Magento\Framework\View\Element\Template
{
    /**
     *
     */
    const XML_PATH_DESIGN_EMAIL_LOGO = 'design/email/logo';

    const POS_ENABLE = 1;
    const POS_DISABLE = 2;
    const POS_LOCKED = 3;

    /**
     * @var \Magestore\Webpos\Model\WebPosSession
     */
    protected $_webPosSession;

    /**
     * @var \Magestore\Webpos\Model\Staff\WebPosSessionFactory
     */
    protected $_webposSessionFactory;

    /**
     * @var \Magestore\Webpos\Model\WebposConfigProvider\CompositeConfigProvider
     */
    protected $_configProvider;

    /**
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $_permissionHelper;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_webposHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $webposFileStorageHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magestore\Webpos\Model\LocationFactory
     */
    protected $locationFactory;
    /**
     * @var \Magestore\Webpos\Model\Pos\PosFactory
     */
    protected $posFactory;

    /**
     * @var $shiftFactory  \Magestore\Webpos\Model\Shift\ShiftFactory */

    protected $_shiftFactory;

    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $_staffFactory;

    /**
     * Login constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Webpos\Model\WebPosSession $webPosSession
     * @param \Magestore\Webpos\Model\Staff\WebPosSessionFactory $sessionFactory
     * @param \Magestore\Webpos\Helper\Permission $permissionHelper
     * @param \Magestore\Webpos\Helper\Data $webposHelper
     * @param \Magestore\Webpos\Model\WebposConfigProvider\CompositeConfigProvider $configProvider
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper
     * @param \Magestore\Webpos\Model\Location\LocationFactory $locationFactory
     * @param \Magestore\Webpos\Model\Pos\PosFactory $posFactory
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Webpos\Model\WebPosSession $webPosSession,
        \Magestore\Webpos\Model\Staff\WebPosSessionFactory $sessionFactory,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magestore\Webpos\Helper\Data $webposHelper,
        \Magestore\Webpos\Model\WebposConfigProvider\CompositeConfigProvider $configProvider,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper,
        \Magestore\Webpos\Model\Location\LocationFactory $locationFactory,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        array $layoutProcessors = [],
        array $data = []
    )
    {
        $this->_webPosSession = $webPosSession;
        $this->_webposSessionFactory = $sessionFactory;
        $this->_configProvider = $configProvider;
        $this->_permissionHelper = $permissionHelper;
        $this->_webposHelper = $webposHelper;
        $this->_storeManager = $context->getStoreManager();
        $this->webposFileStorageHelper = $fileStorageHelper;
        $this->checkoutSession = $checkoutSession;
        $this->locationFactory = $locationFactory;
        $this->posFactory = $posFactory;
        $this->_shiftFactory = $shiftFactory;
        $this->_staffFactory = $staffFactory;
        $this->_layoutProcessors = $layoutProcessors;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $isLogin = $this->_permissionHelper->getCurrentUser();
        if (!$isLogin) {
            $this->setTemplate('Magestore_Webpos::login.phtml');
        } else {
            if($this->isShowWarningLogin()) {
                $this->setTemplate('Magestore_Webpos::login_warning.phtml');
            } else {
                $this->setTemplate('Magestore_Webpos::choose_pos_location.phtml');
            }
        }
        return parent::_prepareLayout(); // TODO: Change the autogenerated stub
    }

    protected function isShowWarningLogin() {
        $config = $this->_webposHelper->getStoreConfig('webpos/security/pos_account_sharing');
        $curSessionId = $this->_permissionHelper->getCurrentSession();
        $curSessionModel = $this->_webposSessionFactory->create()->load($curSessionId, 'session_id');
        $curStaffId = $this->_permissionHelper->getCurrentUser();
        if($config || $curSessionModel->getData('is_allow_multi_pos')) {
            return false;
        } else {
            $sessionCollection = $this->getListSessionByStaff($curStaffId);
            if(count($sessionCollection) != 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    protected function getListSessionByStaff($staffId) {
        $sessionCollection = $this->_webposSessionFactory->create()->getCollection()
            ->addFieldToFilter('staff_id', ['eq' => $staffId]);
        return $sessionCollection;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $isLogin = $this->_permissionHelper->getCurrentUser();
        if (!$isLogin || $this->_permissionHelper->isShowChoosePosLocation()) {
            return parent::toHtml();
        } else {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getWebposConfig()
    {
        return $this->_configProvider->getConfig();
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        $imageUrl = $this->_webposHelper->getWebposLogo();
        if ($imageUrl) {
            return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'webpos/logo/' . $imageUrl;
        } else {
            return $this->getStoreLogoUrl();
        }
    }

    /**
     * @return string
     */
    public function getWarningLogoUrl()
    {
        $imageUrl = 'login-warning.png';
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . 'webpos/logo/' . $imageUrl;
    }

    /**
     * @return string
     */
    protected function getStoreLogoUrl()
    {
        $uploadFolderName = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $webposLogoPath = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $uploadFolderName . '/' . $webposLogoPath;
        $logoUrl = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($webposLogoPath !== null && $this->_isFile($path)) {
            $url = $logoUrl;
        } elseif ($this->getLogoFile()) {
            $url = $this->getViewFileUrl($this->getLogoFile());
        } else {
            $url = $this->getViewFileUrl('images/logo.svg');
        }
        return $url;
    }

    /**
     * @param $filename
     * @return bool
     */
    protected function _isFile($filename)
    {
        if ($this->webposFileStorageHelper->checkDbUsage() && !$this->getMediaDirectory()->isFile($filename)) {
            $this->webposFileStorageHelper->saveFileToFilesystem($filename);
        }

        return $this->getMediaDirectory()->isFile($filename);
    }

    /**
     * @return array
     */
    public function getLocationList()
    {
        $staff = $this->_permissionHelper->getCurrentStaffModel();
        $locationIds = $staff->getLocationId();
        $locationIdsArray = explode(',', $locationIds);
        $result = array();
        foreach ($locationIdsArray as $locationId) {
            $locationModel = $this->locationFactory->create()->load($locationId);
            $result[] = array(
                'location_id' => $locationId,
                'location_name' => $locationModel->getDisplayName()
            );
        }
        return $result;
    }

    /**
     * get Pos List after login
     * @return array
     */
    public function getPosList()
    {
        $staff = $this->_permissionHelper->getCurrentStaffModel();
        $posIds = $staff->getPosIds();
        $posIdsArray = explode(',', $posIds);
        foreach ($posIdsArray as $posId) {
            $posModel = $this->posFactory->create()->load($posId);
            if ($posModel->getStatus() == self::POS_DISABLE ) {
                continue;
            }
            $nameAndStatus = $this->getNameWithStatusPos($posModel);
            $posStatusSelectBoolean = $this->getStatusPosBoolean($posModel, $staff);
            $result[] = array(
                'pos_id' => $posId,
                'pos_name' => $posModel->getPosName(),
                'location_id' => $posModel->getLocationId(),
                'disable' => $posStatusSelectBoolean,
                'name_status' => $nameAndStatus
            );
        }
        /* Sort pos by status */
        $resultAfterSort = $this->subvalSort($result,'disable');
        return $resultAfterSort;
    }

    /* get name and status of POS in choose pos screen after login */
    function getNameWithStatusPos($posModel) {
        $nameAndStatus = '';
        $staffName = '';
        $staffId = $posModel->getStaffId();
        if ($staffId) {
            $staffModel = $this->_staffFactory->create()->load($staffId);
            $staffName = $staffModel->getUsername();
        }
        if ($posModel->getStatus() == self::POS_ENABLE) {
            $nameAndStatus = '';
        } elseif ($posModel->getStatus() == self::POS_LOCKED) {
            $nameAndStatus = ' ' . __('(Locked)');
        }

        if ($staffName && $posModel->getStatus() != self::POS_LOCKED) {
            $nameAndStatus = " ($staffName)";
        }
        return $nameAndStatus;
    }

    function getStatusPosBoolean($posModel, $staff) {
        $posStatusSelect = false;
        $currentStaffId = $staff->getStaffId();
        $staffId = $posModel->getStaffId();
        $staffLock =  $posModel->getStaffLocked();
        if ($posModel->getStatus() != self::POS_ENABLE && $staffId != $currentStaffId) {
            $posStatusSelect = true;
        }

        if ($posModel->getStatus() == self::POS_LOCKED && $staffLock == $currentStaffId) {
            $posStatusSelect = false;
        }

        if ($staffId && $staffId != $currentStaffId) {
            $posStatusSelect = true;
        }
        return $posStatusSelect;
    }

    /*
     * Sort array by key
     */
    function subvalSort($a, $subkey) {
        foreach($a as $k=>$v) {
            $b[$k] = strtolower($v[$subkey]);
        }
        asort($b);
        foreach($b as $key=>$val) {
            $c[] = $a[$key];
        }
        return $c;
    }

    /**
     * @return mixed
     */
    public function isShowPosSelect()
    {
        return $this->_permissionHelper->isNeedSessionBeforeWorking();
    }

}
