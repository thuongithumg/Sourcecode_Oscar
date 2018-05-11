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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('webpos_head')->setTitle('Web POS');
        $this->renderLayout();
    }

    public function restGuideAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('webpos_head')->setTitle('Fix Rest API Problem');
        $this->renderLayout();
    }

    public function reinstallDbAction() {
        $installer = new Mage_Core_Model_Resource_Setup();
        $installer->startSetup();
        $webposHelper = Mage::helper("webpos");
        $installer->run("
		  DROP TABLE IF EXISTS {$installer->getTable('webpos_admin')};
		  DROP TABLE IF EXISTS {$installer->getTable('webpos_survey')};
		  DROP TABLE IF EXISTS {$installer->getTable('webpos_order')};
		  DROP TABLE IF EXISTS {$installer->getTable('webpos_products')};
		  DROP TABLE IF EXISTS {$installer->getTable('webpos_xreport')};
		");
        if (!$webposHelper->columnExist($installer->getTable('webpos_user'), 'till_ids')) {
            $installer->run(" ALTER TABLE {$installer->getTable('webpos_user')} ADD `till_ids` VARCHAR( 255 ) default 'all'; ");
        }
        $webposHelper->addNewTables();
        $webposHelper->addAdditionalFields();
        $webposHelper->addWebposVisibilityAttribute();

        $installer->endSetup();
        $this->getResponse()->setBody('ok');
    }

    public function platformAction()
    {
        $data = Zend_Json::encode(
            array(
                'platform' => 'Magento',
                'website_id' => Mage::app()->getWebsite()->getId()
                 )
        );
        $this->getResponse()->setBody($data);
    }

    public function authorizeTestAction()
    {
        $authorize = Mage::getModel('webposauthorizenet/webposauthorizenet');
        if($authorize->canConnectToApi()) {
            $this->getResponse()->setBody('Success');
        } else {
            $this->getResponse()->setBody('Error');
        }
    }

    /**
     *
     */
    public function changeLocationAction()
    {
        $locationId = $this->getRequest()->getParam('location_id');
        $posId = $this->getRequest()->getParam('pos_id');
        $currentSessionId = Mage::app()->getCookie()->get('WEBPOSSESSION');
        $sessionModel = Mage::getModel('webpos/user_webpossession')->load($currentSessionId, 'session_id');


        $openShift = Mage::getModel('webpos/shift')->getCollection()
            ->addFieldToFilter('pos_id', $posId)
            ->addFieldToFilter('status', 0);
        $canNotOpenShift = true;
        if (Mage::helper('webpos/permission')->getCurrentUser()) {
            $staffId = Mage::helper('webpos/permission')->getCurrentUser();
            $staffModel = Mage::getModel('webpos/user')->load($staffId);
            $roleId = $staffModel->getRoleId();

            $roleModel =  Mage::getModel('webpos/role')->load($roleId);

            $authorizeRuleCollection = explode(',',$roleModel->getPermissionIds());

            if (in_array(Magestore_Webpos_Model_Source_Adminhtml_Permission::MANAGE_SHIFT_OPEN, $authorizeRuleCollection)
                || in_array(Magestore_Webpos_Model_Source_Adminhtml_Permission::ALL_PERMISSION, $authorizeRuleCollection)) {
                $canNotOpenShift = false;
            }

        }
        if (!($openShift->getSize()) && $canNotOpenShift && Mage::getStoreConfig('webpos/general/enable_session')) {
            $data = Zend_Json::encode(
                array('message'=> Mage::helper('webpos')->__('You can not access this POS because you do not have the permission!!'))
            );
            $this->getResponse()->setHeader('Content-type', 'application/json; charset=UTF-8');
            $this->getResponse()->setBody($data);
        } else {
            if ($sessionModel->getId()) {
                // return store data
                $storeId = Mage::getModel('webpos/userlocation')->load($locationId)->getData('location_store_id');
                if(!$storeId) {
                    $storeId = Mage::app()
                        ->getWebsite(true)
                        ->getDefaultGroup()
                        ->getDefaultStoreId();
                }
                $data = Zend_Json::encode(
                    array(
                        'location_store_id' => $storeId,
                        'store_url' => Mage::getModel('core/store')->load($storeId)->getUrl('webpos/index/index', array('_secure' => true)),
                        'store_name' => Mage::getModel('core/store')->load($storeId)->getName()
                    )
                );

                $sessionModel->setData('location_id', $locationId);
                if ($posId != 'undefined' && $posId > 0) {
                    $sessionModel->setData('pos_id', $posId);
                    if($staffId = $sessionModel->getStaffId() && $posId) {
                        Mage::getModel('webpos/pos')->assignStaff($posId, $staffId);
                    }
                }
                $sessionModel->setData('current_store_id', $storeId);
                $sessionModel->save();
                $this->getResponse()->setHeader('Content-type', 'application/json; charset=UTF-8');
                $this->getResponse()->setBody($data);
            }
        }
    }

    /**
     */
    public function findOpenPos($posId)
    {
        $openPos = Mage::getModel('webpos/shift')->getCollection()
            ->addFieldToFilter('pos_id', $posId)
            ->addFieldToFilter('status', 0)
            ->getFirstItem();
        return $openPos->getEntityId();
    }
}