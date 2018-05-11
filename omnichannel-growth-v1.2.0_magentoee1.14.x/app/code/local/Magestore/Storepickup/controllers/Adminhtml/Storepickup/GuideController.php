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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Adminhtml_Storepickup_GuideController
 */
class Magestore_Storepickup_Adminhtml_Storepickup_GuideController extends Mage_Adminhtml_Controller_action
{

    public function indexAction() {            
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Store Pickup Guide'));
        $this->renderLayout();
    }

    /**
     * @return mixed
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }
}