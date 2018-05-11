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
class Magestore_Coresuccess_Block_Adminhtml_Page_Title extends Mage_Adminhtml_Block_Template {
    
    /**
     * Initialize template and cache settings
     *
     */
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('coresuccess/page/title.phtml');
    }
    
    /**
     * 
     * @return string
     */
    public function getTitle() {
        $moduleName = $this->helper('coresuccess')->getCurrentModuleName();
        if($moduleName == 'Mage'){
            $moduleKey = $this->helper('coresuccess')->getCurrentSectionConfig();
            $moduleName = uc_words($moduleKey);
        }
        if(strtolower($moduleName) == 'coresuccess'){
            return 'Omnichannel';
        }
        return 'Omnichannel | '.$moduleName;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isInDashboard() {
        if( $this->getRequest()->getActionName() == 'dashboard'
            && $this->getRequest()->getControllerName() == 'coresuccess')
            return true;
        return false;
    }
}