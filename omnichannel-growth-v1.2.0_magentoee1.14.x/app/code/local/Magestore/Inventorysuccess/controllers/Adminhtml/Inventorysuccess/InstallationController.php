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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_InstallationController 
    extends Mage_Adminhtml_Controller_Action
    implements Magestore_Coresuccess_Controller_ProcessControllerInterface 
{
    
    /**
     * 
     */
    public function doProcessAction()
    {
        $processService = $this->getProcessService();
        try {
            $step = $processService->processByStep();
        } catch (Exception $e) {
            $return = array(
                'error' => 1,
                'msgs' => array(
                    'error' => $e->getMessage(),
                )
            );             
        }
        if($step) {
            $return = array(
                'step' => $step->getStep(),
                'current_index' => $step->getCurrentIndex(),
                'total' => $step->getTotal(),
                'status' => $step->getStatus(),
                'finished' => 0,
                'msgs' => array(
                    'start' => Mage::helper('inventorysuccess')->__('Start %s', $processService->getProcessTitle($step->getStep())),
                    'finish' => Mage::helper('inventorysuccess')->__('Finish %s', $processService->getProcessTitle($step->getStep())),
                )                  
            );
        } else {
            $return = array(
                'finished' => 1,              
            );            
        }
        $this->getResponse()->setBody(Zend_Json::encode($return));
    }
    
    /**
     * 
     */
    public function runAction()
    {
        $this->getProcessService()->reapplySetupScript();
        $this->loadLayout();
        $this->renderLayout();        
    }

    /**
     * @return Magestore_Coresuccess_Model_Service_Process_ProcessServiceInterface
     */
    public function getProcessService()
    {
        return Magestore_Coresuccess_Model_Service::installService();
    }
    
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $resource = 'admin/inventorysuccess';
        return Mage::getSingleton('admin/session')->isAllowed($resource);
    }    

}