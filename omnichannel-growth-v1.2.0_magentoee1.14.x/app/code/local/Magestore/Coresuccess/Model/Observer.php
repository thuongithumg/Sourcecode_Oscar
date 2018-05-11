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

class Magestore_Coresuccess_Model_Observer {

    /**
     * Apply Magestore layout to all modules
     *
     * @param type $observer
     */
    public function controllerActionLayoutLoadBefore($observer) {
        $controller = $observer->getEvent()->getAction();
        $layout = $observer->getEvent()->getLayout();
        $class = get_class($controller);
        $realModuleName = substr(
            $class, 0, strpos(strtolower($class), '_adminhtml_' . strtolower($controller->getRequest()->getControllerName() . 'Controller'))
        );
        /* Replace by module that current module depends to */
        if($parentModule = Mage::helper('coresuccess')->getDependModule($realModuleName)){
            $realModuleName = $parentModule;
        }

        if(!Mage::registry('current_real_module_name')){
            Mage::register('current_real_module_name', $realModuleName);
        }
        /* apply coresuccess layout to all modules*/
        if(Mage::helper('coresuccess')->isApplyERPlayout()){
            $layout->getUpdate()->addHandle('adminhtml_coresuccess_module_layout');
        }

        /* apply coresuccess layout to configuration pages */
        Mage::helper('coresuccess')->updateConfigLayout($controller, $layout);
    }

}
