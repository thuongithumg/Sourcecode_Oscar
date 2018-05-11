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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_ActivityController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return true;
        //Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/transferstock/activity');
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        $this->loadLayout();
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function viewAction()
    {
        $activityId = $this->getRequest()->getParam('activity_id');
        $model      = Mage::getModel('inventorysuccess/transferstock_activity')->load($activityId);
        if ($model->getId()) {
            Mage::register('transfer_activity', $model);
        }
        return $this->loadLayout()->renderLayout();
    }

    /**
     * render for ajax
     * @return Mage_Core_Controller_Varien_Action
     */
    public function gridAction()
    {
        return $this->loadLayout()->renderLayout();
    }
}
