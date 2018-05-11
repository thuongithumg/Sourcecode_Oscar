<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Payment\Method\Multipayment;

/**
 * class \Magestore\Webpos\Block\Payment\Method\Multipayment\Multipaymentforpos
 * 
 * Multipayment for POS form block
 * Methods:
 *  _construct
 *  getActiveMethods
 *  htmlEscape
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Block\Payment\Method\Multipayment
 * @module      Webpos
 * @author      Magestore Developer
 */
class Multipaymentforpos extends \Magento\Payment\Block\Form
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager = '';
    
    /**
     * Store config manager
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig = '';
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = '';
    
    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('Magestore_Webpos::webpos/payment/method/form/multipaymentforpos.phtml');
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $context->getStoreManager();
    }
    
    /**
     * Get methods will be used for multipayment from setting
     * @return array
     */
    public function getActiveMethods() {
        $methods = $this->_objectManager->create('Magestore\Webpos\Model\Source\Adminhtml\Multipaymentforpos')->getAllowPaymentMethodsWithLabel();
        $storeId = $this->_storeManager->getStore()->getId();
        $paymentsForSplit = $this->_scopeConfig->getValue('payment/multipaymentforpos/payments',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
        if (count(explode(',', $paymentsForSplit)) > 0) {
            foreach ($methods as $methodCode => $methodTitle) {
                if (!in_array($methodCode, explode(',', $paymentsForSplit))) {
                    unset($methods[$methodCode]);
                }
            }
        }
        return $methods;
    }
    
    /**
     * 
     * @param string $str
     * @return string
     */
    public function htmlEscape($str){
        return $this->_objectManager->create('Magestore\Webpos\Helper\Data')->htmlEscape($str);
    }
}