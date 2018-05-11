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
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsloyaltylevel Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Adminhtml_Reward_RewardpointsloyaltylevelController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_RewardPointsLoyaltyLevel_Adminhtml_RewardpointsloyaltylevelController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('rewardpoints/rewardpointsloyaltylevel')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Loyalty Level Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        $group_id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($group_id, 'customer_group_id');
        if ($model->getId() || $group_id == 0) {
            if (!$model->getId())
                Mage::register('rewardpointsloyaltylevel_new', true);
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('rewardpointsloyaltylevel_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('rewardpoints/rewardpointsloyaltylevel');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Loyalty Level Manager'), Mage::helper('adminhtml')->__('Loyalty Level Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true)
                    ->setCanLoadRulesJs(true);
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true)
                    ->addItem('js', 'tiny_mce/tiny_mce.js')
                    ->addItem('js', 'mage/adminhtml/wysiwyg/tiny_mce/setup.js')
                    ->addJs('mage/adminhtml/browser.js')
                    ->addJs('prototype/window.js')
                    ->addJs('lib/flex.js')
                    ->addJs('mage/adminhtml/flexuploader.js');

            $this->_addContent($this->getLayout()->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_edit'))
                    ->_addLeft($this->getLayout()->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('rewardpointsloyaltylevel')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        //$this->_redirect('adminhtml/customer_group/new');
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $levelFrom = $this->getRequest()->getParam('level_new');
            if ($levelFrom) {
                $taxClass = (int) $this->getRequest()->getParam('tax_class');
                $customerGroupCode = (string) $this->getRequest()->getParam('code');
                $customer_group_id = (int) $this->getRequest()->getParam('customer_group_id');
                if ($taxClass && !empty($customerGroupCode)) {
                    try {
                        $customerGroup = Mage::getModel('customer/group');
                        $customerGroup->setCode($customerGroupCode)
                                ->setTaxClassId($taxClass)
                                ->save();
                        $data['customer_group_id'] = $customerGroup->getId();
                        $data['level_name'] = $customerGroup->getCustomerGroupCode();
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        Mage::getSingleton('adminhtml/session')->setRewardPointsLoyaltyLevelData($data);
                        $this->_redirect('*/*/new');
                        return;
                    }
                } else if ($customer_group_id) {
                    $data['level_name'] = Mage::getModel('customer/group')->load($customer_group_id)->getCustomerGroupCode();
                }
            }
            $group_id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($group_id, 'customer_group_id');
            $model->addData($data);
            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('rewardpointsloyaltylevel')->__('Level was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getCustomerGroupId(), 'tab' => $this->getRequest()->getParam('back')));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setRewardPointsLoyaltyLevelData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('rewardpointsloyaltylevel')->__('Unable to find level to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
//    public function deleteAction() {
//        if ($this->getRequest()->getParam('id') > 0) {
//            try {
//                $model = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel');
//                $model->setId($this->getRequest()->getParam('id'))
//                        ->delete();
//                Mage::getSingleton('adminhtml/session')->addSuccess(
//                        Mage::helper('adminhtml')->__('Item was successfully deleted')
//                );
//                $this->_redirect('*/*/');
//            } catch (Exception $e) {
//                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
//                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
//            }
//        }
//        $this->_redirect('*/*/');
//    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $rewardpointsloyaltylevelIds = $this->getRequest()->getParam('rewardpointsloyaltylevel');
        if (!is_array($rewardpointsloyaltylevelIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($rewardpointsloyaltylevelIds as $rewardpointsloyaltylevelId) {
                    $rewardpointsloyaltylevel = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($rewardpointsloyaltylevelId);
                    $rewardpointsloyaltylevel->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($rewardpointsloyaltylevelIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
        $rewardpointsloyaltylevelIds = $this->getRequest()->getParam('rewardpointsloyaltylevel');
        if (!is_array($rewardpointsloyaltylevelIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($rewardpointsloyaltylevelIds as $rewardpointsloyaltylevelId) {
                    Mage::getSingleton('rewardpointsloyaltylevel/loyaltylevel')
                            ->load($rewardpointsloyaltylevelId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($rewardpointsloyaltylevelIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'rewardpointsloyaltylevel.csv';
        $content = $this->getLayout()
                ->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'rewardpointsloyaltylevel.xml';
        $content = $this->getLayout()
                ->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvLoyaltyAction() {
        $fileName = 'customer_loyaltylevel.csv';
        $content = $this->getLayout()
                ->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_edit_tab_customer')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlLoyaltyAction() {
        $fileName = 'customer_loyaltylevel.xml';
        $content = $this->getLayout()
                ->createBlock('rewardpointsloyaltylevel/adminhtml_rewardpointsloyaltylevel_edit_tab_customer')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * 
     * @return type
     */
    public function ignoreEarnRuleAction() {
        $id = $this->getRequest()->getParam('rule_id');
        $type = $this->getRequest()->getParam('type');
        $customerGroup = $this->getRequest()->getParam('group');
        $customerGroupId = Mage::getModel('customer/group')->load($customerGroup, 'customer_group_code')->getId();
        if ($type == 'rate') {
            $model = Mage::getModel('rewardpoints/rate')->load($id);
        } else if ($type == 'catalog')
            $model = Mage::getModel('rewardpointsrule/earning_catalog')->load($id);
        else if ($type = 'sales')
            $model = Mage::getModel('rewardpointsrule/earning_sales')->load($id);

        try {
            $this->removeGroup($model, $customerGroupId);
            echo 'success';
            return;
        } catch (Exception $exc) {
            echo '';
            return;
        }
    }

    /**
     * 
     * @return type
     */
    public function ignoreSpendRuleAction() {
        $id = $this->getRequest()->getParam('rule_id');
        $type = $this->getRequest()->getParam('type');
        $customerGroup = $this->getRequest()->getParam('group');
        $customerGroupId = Mage::getModel('customer/group')->load($customerGroup, 'customer_group_code')->getId();
        if ($type == 'rate') {
            $model = Mage::getModel('rewardpoints/rate')->load($id);
        } else if ($type == 'catalog')
            $model = Mage::getModel('rewardpointsrule/spending_catalog')->load($id);
        else if ($type = 'sales')
            $model = Mage::getModel('rewardpointsrule/spending_sales')->load($id);
        try {
            $this->removeGroup($model, $customerGroupId);
            echo 'success';
            return;
        } catch (Exception $exc) {
            echo '';
            return;
        }
    }

    public function ignorePromoRuleAction() {
        $id = $this->getRequest()->getParam('rule_id');
        $type = $this->getRequest()->getParam('type');
        $customerGroup = $this->getRequest()->getParam('group');
        $customerGroupId = Mage::getModel('customer/group')->load($customerGroup, 'customer_group_code')->getId();
        if ($type == 'shopping_discount') {
            $model = Mage::getModel('salesrule/rule')->load($id);
        } else if ($type = 'catalog_discount')
            $model = Mage::getModel('catalogrule/rule')->load($id);
        try {
            $this->removeGroup($model, $customerGroupId);
            echo 'success';
            return;
        } catch (Exception $exc) {
            echo '';
            return;
        }
    }

    public function addRuleAction() {
        if (!is_null($this->getRequest()->getParam('id'))) {
            echo 'success';
            return;
        }
        echo 'fail';
        return;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('rewardpoints/rewardpointsloyaltylevel');
    }
    
    public function removeGroup($model,$customerGroupId){
        $old = $model->getData('customer_group_ids');
        $oldArr = explode(',', $old);
        $index = array_search($customerGroupId, $oldArr);
        if($index){
            unset($oldArr[$index]);
        }
        $model->setData('customer_group_ids',  implode(',', $oldArr))->save();
    }

}
