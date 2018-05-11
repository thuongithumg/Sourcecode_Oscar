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

/**
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 10:19 SA
 */
class Magestore_Webpos_Adminhtml_RoleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {
        $roleId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('webpos/role')->load($roleId);

        if ($model->getRoleId() || $roleId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('role_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('webpos/manage/webpos_role');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Role Manager'),
                Mage::helper('adminhtml')->__('Role Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Role News'),
                Mage::helper('adminhtml')->__('Role News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('webpos/adminhtml_role_edit'))
                ->_addLeft($this->getLayout()->createBlock('webpos/adminhtml_role_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('webpos')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_redirect('*/*/edit');
    }

    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {


            $model = Mage::getModel('webpos/role');
            if (isset($data['permission_ids'])) {
                $data['permission_ids'] = implode(',',$data['permission_ids']);
            }
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                $roleId=$model->getRoleId();
                if (isset($data['role_user'])) {
                    $userArray = array();
                    parse_str(($data['role_user']), $userArray);
                    $userArray = array_keys($userArray);
                    $userCollection=Mage::getModel('webpos/user')->getCollection()
                        ->addFieldToFilter('role_id',$this->getRequest()->getParam('id'));
                    foreach ($userCollection as $user) {
                        $userId=$user->getUserId();
                        if ($userId && !in_array($userId,$userArray)) {
                            $user->setRoleId(0);
                            $user->save();
                        }
                    }
                    foreach ($userArray as $user) {
                        if(is_numeric($user)){
                            $userModel = Mage::getModel('webpos/user')->load($user);
                            $userModel->setRoleId($roleId);
                            $userModel->save();
                        }
                    }
                }
				if($model->getData('maximum_discount_percent') > 100){
					$model->setData('maximum_discount_percent',100);
					$model->save();
					Mage::getSingleton('adminhtml/session')->addError( Mage::helper('webpos')->__('Maximum discount percent cannot be higher than 100'));
				}
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('webpos')->__('Role was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('webpos')->__('Unable to find role to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('webpos/role');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Role was successfully deleted')
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
        $roleIds = $this->getRequest()->getParam('webpos');
        if (!is_array($roleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Role(s)'));
        } else {
            try {
                foreach ($roleIds as $roleId) {
                    $role = Mage::getModel('webpos/role')->load($roleId);
                    $role->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($roleIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $roleIds = $this->getRequest()->getParam('webpos');
        $statusId =  $this->getRequest()->getParam('status_id');

        if (!is_array($roleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Role(s)'));
        } else {
            try {
                foreach ($roleIds as $roleId) {
                    $role = Mage::getModel('webpos/role')->load($roleId);
                    $role->setActive($statusId);
                    $role->save();

                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($roleIds))
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


    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'role.csv';
        $content    = $this->getLayout()
            ->createBlock('webpos/adminhtml_role_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'role.xml';
        $content    = $this->getLayout()
            ->createBlock('webpos/adminhtml_role_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function userAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('role.edit.tab.user')
            ->setUsers($this->getRequest()->getPost('ouser', null));
        $this->renderLayout();
    }
    public function usergridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('role.edit.tab.user')
            ->setUsers($this->getRequest()->getPost('ouser', null));
        $this->renderLayout();
    }
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/manage_webpos_permission/manage_webpos_role');
    }
}