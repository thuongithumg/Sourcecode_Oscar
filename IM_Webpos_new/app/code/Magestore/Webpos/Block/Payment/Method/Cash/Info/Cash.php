<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Payment\Method\Cash\Info;

/**
 * class \Magestore\Webpos\Block\Payment\Method\Cash\Info\Cash
 * 
 * Cash for POS info block
 * Methods:
 *  _construct
 *  _prepareSpecificInformation
 *  getMethodTitle
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Block\Payment\Method\Cash\Info
 * @module      Webpos
 * @author      Magestore Developer
 */
class Cash extends \Magento\Payment\Block\Info
{
    
    /**
     * Helper payment object
     *
     * @var \Magestore\Webpos\Helper\Payment
     */
    protected $_helperPayment = '';
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magestore\Webpos\Helper\Payment $helperPayment
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Webpos\Helper\Payment $helperPayment,
        array $data = []
    ) {
        $this->_helperPayment = $helperPayment;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * 
     * @param \Magento\Framework\DataObject $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new \Magento\Framework\DataObject();
        $transport = parent::_prepareSpecificInformation($transport);
        return $transport;
    }
    
    /**
     * Construct function
     */
    protected function _construct()
    {
        parent::_construct();
        /*
         * $this->setTemplate('Magento_Payment::info/pdf/default.phtml');
         * $this->setTemplate('webpos/admin/webpos/payment/method/info/cash.phtml');
         */
    }
    
    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        return $this->_helperPayment->getCashMethodTitle();
    }

}
