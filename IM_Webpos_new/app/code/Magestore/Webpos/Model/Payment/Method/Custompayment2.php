<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Payment\Method;

/**
 * class \Magestore\Webpos\Model\Payment\Method\Custompayment2
 * 
 * Web POS Custom payment number two payment method model
 * Methods:
 *  assignData
 *  isAvailable
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Custompayment2 extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment method code
     * @var string
     */
    protected $_code = 'cp2forpos';

    /**
     * Class of info block
     * @var string
     */
    protected $_infoBlockType = 'Magestore\Webpos\Block\Payment\Method\Multipayment\Info\Multipayment';

    /**
     * Class of form block 
     * @var string
     */
    protected $_formBlockType = 'Magestore\Webpos\Block\Payment\Method\Cc\Ccforpos';
    
    /**
     * Request object
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request = '';
    
    /**
     * Helper payment object
     *
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $_helperPayment = '';

    /**
     * Helper permission object
     *
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $_helperPermission = '';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magestore\Webpos\Helper\Payment $helperPayment
     * @param \Magestore\Webpos\Helper\Permission $helperPermission
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, 
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, 
        \Magento\Payment\Helper\Data $paymentData, 
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
        \Magento\Payment\Model\Method\Logger $logger, 
        \Magento\Framework\App\Request\Http $request,
        \Magestore\Webpos\Helper\Payment $helperPayment,
        \Magestore\Webpos\Helper\Permission $helperPermission,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
        array $data = []
    ) {
        $this->_request = $request;
        $this->_helperPayment = $helperPayment;
        $this->_helperPermission = $helperPermission;
        parent::__construct(
            $context, 
            $registry, 
            $extensionFactory, 
            $customAttributeFactory, 
            $paymentData, 
            $scopeConfig, 
            $logger, 
            $resource, 
            $resourceCollection, 
            $data
        );
    }
    
    /**
     * Enable for Web POS only
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $routeName = $this->_request->getRouteName();
        $settingEnabled = $this->_helperPayment->isCp2PaymentEnabled();
        $isWebposApi = $this->_helperPermission->validateRequestSession();
        if (($routeName == "webpos" || $isWebposApi) && $settingEnabled == true){
            return true;
        }else{
            return false;
        }
    }
    /**
     * Assign data from payment object to info block
     * @param payment $data
     * @return \Magestore\Webpos\Model\Payment\Method\Custompayment2
     */
    public function assignData(\Magento\Framework\DataObject $data) {
        if (!$data instanceof \Magento\Framework\DataObject) {
            $data = new \Magento\Framework\DataObject($data);
        }
        
        $info = $this->getInfoInstance();

        if ($data->getData('cp2forpos_ref_no')) {
            $info->setData('cp2forpos_ref_no', $data->getData('cp2forpos_ref_no'));
        }

        return $this;
    }
}