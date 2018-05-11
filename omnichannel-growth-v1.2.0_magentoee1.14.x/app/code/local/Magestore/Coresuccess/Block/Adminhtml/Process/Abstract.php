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
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
abstract class Magestore_Coresuccess_Block_Adminhtml_Process_Abstract extends Mage_Adminhtml_Block_Template
{

    /**
     * 
     * @return Magestore_Coresuccess_Block_Adminhtml_Process_Process
     */
    protected function _construct()
    {
        parent::_prepareLayout();
        $this->setTemplate('coresuccess/process/run.phtml');
        return $this;
    }
    
    /**
     * 
     * @return Magestore_Coresuccess_Model_Service_Process_ProcessServiceInterface
     */
    abstract public function getProcessService();
    
    /**
     * get process steps
     * 
     * @return array()
     */
    public function getSteps()
    {
        return $this->getProcessService()->getSteps();
    }
    
    /**
     * 
     * @return string
     */
    public function getConfigJson()
    {
        return Zend_Json::encode(array(
            'steps' => $this->getSteps(),
            'runProcessUrl' => $this->getRunProcessUrl(),
            'redirectUrl' => $this->getRedirectUrl(),
            'formKey' => $this->getFormKey(),
        ));
    }

    /**
     * 
     * @return array()
     */
    public function getExceptions()
    {
        return array();
    }

    /**
     * 
     * @return boolean
     */
    public function getShowFinished()
    {
        return false;
    }

    /**
     * 
     * @return string
     */
    public function getLoadDataTypeUrl()
    {
        return $this->getUrl('*/*/processDataList');
    }

    /**
     * 
     * @return string
     */
    public function getTotalUrl()
    {
        return $this->getUrl('*/*/countData');
    }

    /**
     * 
     * @return string
     */
    public function getRunProcessUrl()
    {
        return $this->getUrl('*/*/doProcess');
    }

    /**
     * 
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Processing');
    }

    /**
     * 
     * @return string
     */
    public function getRedirectMessage()
    {
        return $this->__('Redirecting to review page...');
    }

    /**
     * 
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->__('There was error while processing! Try again.');
    }

    /**
     * 
     * @return string
     */
    public function getFinishMessage()
    {
        return $this->__('Finished all processes.');
    }

}
