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
 * Adjuststock Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Lowstocknotification_RuleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorysuccess_Adminhtml_InventorysuccessController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/prediction/lowstock_rule')
            ->_addBreadcrumb(
                $this->__('Manage Rule'),
                $this->__('Manage Rule')
            )->_title($this->__('Manage Rule'));
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $notAppliedRules = Mage::getResourceModel('inventorysuccess/lowStockNotification_rule_collection')
            ->getNotAppliedRules()
            ->getSize();
        if ($notAppliedRules > 0) {
            Mage::register('inventorysuccess_not_applied_rule', $notAppliedRules);
            Mage::getSingleton('adminhtml/session')->addNotice(
                $this->__('We found updated rules that are not applied. Please click "Apply Rules" to update your rule.')
            );
        }
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $ruleId = $this->getRequest()->getParam('id');
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Rule $model */
        $model = Mage::getModel('inventorysuccess/lowStockNotification_rule')->load($ruleId);
        if ($model->getId() || $ruleId == 0) {
            $this->_initAction();

            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
            Mage::register('lowstocknotification_rule_data', $model);

            if ($model->getId()) {
                $this->_addBreadcrumb(
                    $this->__('Edit Rule'),
                    $this->__('Edit Rule')
                )->_title($this->__('Edit Rule'));
            } else {
                $this->_addBreadcrumb(
                    $this->__('New Rule'),
                    $this->__('New Rule')
                )->_title($this->__('New Rule'));
            }

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_lowStockNotification_rule_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_lowStockNotification_rule_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Rule does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            //filter date for date field
            $data = $this->_filterDates($data, array('from_date', 'to_date'));
            if (isset($data['from_date']) && $data['from_date'] == '')
                $data['from_date'] = null;
            if (isset($data['to_date']) && $data['to_date'] == '')
                $data['to_date'] = null;
            /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Rule $model */
            $model = Mage::getModel('inventorysuccess/lowStockNotification_rule');

            /** event prepare save rule */
            Mage::dispatchEvent(
                'adminhtml_controller_inventorysuccess_lowstocknotification_rule_prepare_save',
                array('data' => $data)
            );

            if (isset($data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }
            if (isset($data['specific_time'])) {
                $data['specific_time'] = implode(',', $data['specific_time']);
            }
            if (isset($data['specific_day'])) {
                $data['specific_day'] = implode(',', $data['specific_day']);
            }
            if (isset($data['specific_month'])) {
                $data['specific_month'] = implode(',', $data['specific_month']);
            }
            if (isset($data['warehouse_ids']) && count($data['warehouse_ids']) && is_array($data['warehouse_ids'])) {
                $data['warehouse_ids'] = implode(',', $data['warehouse_ids']);
            }
            //set apply default is not apply
            $data['apply'] = Magestore_Inventorysuccess_Model_LowStockNotification_Rule::NOT_APPLY;

            // add data to model
            $model->addData($data)
                ->setId($this->getRequest()->getParam('id'));
            try {
                $model->loadPost($data);
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Low stock rule has been successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                //auto apply rule
                if ($this->getRequest()->getParam('auto_apply')) {
                    if ($model->getStatus() == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::STATUS_ACTIVE) {
                        $ruleProductService = Magestore_Coresuccess_Model_Service::ruleProductService();
                        $ruleProductService->applyRule($model);
                    }
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId(),
                    ));
                    return;
                }

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId(),
                    ));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id'),
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find low stock rule to save'));
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('inventorysuccess/lowStockNotification_rule');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Low stock rule has been successfully deleted.')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $ruleIds = $this->getRequest()->getParam('rule_id');
        if (!is_array($ruleIds) || empty($ruleIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                $success = 0;
                foreach ($ruleIds as $ruleId) {
                    $rule = Mage::getModel('inventorysuccess/lowStockNotification_rule')->load($ruleId);
                    $rule->delete();
                    $success++;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) have been successfully deleted.',
                        $success)
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
    public function massStatusAction()
    {
        $ruleIds = $this->getRequest()->getParam('rule_id');
        if (!is_array($ruleIds) || empty($ruleIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                $success = 0;
                $status = $this->getRequest()->getParam('status');
                foreach ($ruleIds as $ruleId) {
                    $rule = Mage::getModel('inventorysuccess/lowStockNotification_rule')
                        ->load($ruleId);
                    $rule->setStatus($status)
                        ->setIsMassupdate(true)
                        ->setId($rule->getId())
                        ->save();
                    $success++;
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been successfully updated.', $success)
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
    public function exportCsvAction()
    {
        $fileName = 'low_stock_notification_rule.csv';
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_lowStockNotification_rule_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'low_stock_notification_rule.xml';
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_lowStockNotification_rule_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * duplicate rule
     */
    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        if ($id) {
            try {
                /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Rule $model */
                $model = Mage::getModel('inventorysuccess/lowStockNotification_rule');
                $model->load($id);
                $model->setId(null);
                $model->setStatus(Magestore_Inventorysuccess_Model_LowStockNotification_Rule::STATUS_INACTIVE);
                $model->setApply(Magestore_Inventorysuccess_Model_LowStockNotification_Rule::NOT_APPLY);
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('You duplicated the rule.'));
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (\Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('We can\'t duplicate this rule right now. Please review the log and try again.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find low stock rule to duplicate'));
        $this->_redirect('*/*/');
    }

    /**
     * run rule now
     */
    public function runAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Rule $model */
                $model = Mage::getModel('inventorysuccess/lowStockNotification_rule');

                $model->load($id);
                if (!$model->getApply()) {
                    $ruleProductService = Magestore_Coresuccess_Model_Service::ruleProductService();
                    $ruleProductService->applyRule($model);
                }

                $ruleService = Magestore_Coresuccess_Model_Service::ruleService();
                $ruleService->startNotification($model);

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('This rule has been applied. Please go to Low Stock Notifications to see the list of low-stock items.')
                );
            } catch (\Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            } catch (\Exception $e) {
                Mage::getSingleton('core/session')->addError(
                    $this->__('We can\'t run this rule right now. Please review the log and try again.')
                );
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('We can\'t find a rule to tun.'));
        }
        return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
    }

    public function applyRuleAction()
    {
        $notAppliedRules = Mage::getResourceModel('inventorysuccess/lowStockNotification_rule_collection')
            ->getNotAppliedRules();
        try {
            $count = 0;
            $ruleProductService = Magestore_Coresuccess_Model_Service::ruleProductService();
            foreach ($notAppliedRules as $rule) {
                $ruleProductService->applyRule($rule);
                $count++;
            }
        } catch (\Exception $e) {
            Mage::getSingleton('core/session')->addError(
                $this->__('Something went wrong while saving the rule data. Please review the error log.')
            );
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/prediction/lowstock_rule');
    }

}