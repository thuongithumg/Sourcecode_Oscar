<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Staff\Staff;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Staff\Save
 *
 * Save user
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Staff
 * @module      Webpos
 * @author      Magestore Developer
 */
/**
 * Class Save
 * @package Magestore\Webpos\Controller\Adminhtml\Staff\Staff
 */
class Save extends \Magestore\Webpos\Controller\Adminhtml\Staff\Staff
{

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = (int)$this->getRequest()->getParam('staff_id');
        $signout = (int)$this->getRequest()->getParam('signout');
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        $email = $data['email'];
        if ($email) {
            $findEmailExist = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')
                ->load($email, 'email');
            if ($findEmailExist->getId() && $findEmailExist->getId() != $modelId) {
                //var_dump($modelId);
                $this->messageManager->addErrorMessage(__('Email %1 is existed.', $email));
                return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('staff_id')]);
            }
        }
        if ($modelId) {
            $model = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')
                ->load($modelId);
        } else {
            $model = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff');
        }

        if ($signout){
            $sessionCollection = $this->_objectManager->create('Magestore\Webpos\Model\Staff\WebPosSession')->getCollection()
                ->addFieldToFilter('staff_id', $modelId);
            foreach ($sessionCollection as $session){
                $session->delete();
            }
            // remove current staff from other pos
            $posCollection = $this->_objectManager->create('Magestore\Webpos\Model\Pos\Pos')->getCollection()
                ->addFieldToFilter('staff_id', ['eq' => $modelId]);
            foreach ($posCollection as $pos) {
                $pos->setStaffId('')->save();
            }
            $this->messageManager->addSuccessMessage(__('Staff account was logged out of the current POS.'));
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getId()]);
        }

        if (isset($data['pos_ids'])) {
            $data['pos_ids'] = implode(',', $data['pos_ids']);
        } else {
            $data['pos_ids'] = '';
        }

        if (isset($data['location_id'])) {
            $data['location_id'] = implode(',', $data['location_id']);
        }

        if (isset($data['pin'])) {
            if(strlen($data['pin']) != 4) {
                $this->messageManager->addError(__('PIN code must be 4 numbers'));
                return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('staff_id')]);
            }
        }
        $model->setData($data);

        if ($model->hasNewPassword() && $model->getNewPassword() === '') {
            $model->unsNewPassword();
        }
        if ($model->hasPasswordConfirmation() && $model->getPasswordConfirmation() === '') {
            $model->unsPasswordConfirmation();
        }
        $result = $model->validate(); /* validate data */
        if (is_array($result)) {
            foreach ($result as $message) {
                $this->messageManager->addError($message);
            }
            $this->_redirect('*/*/edit', array('_current' => true));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $model->setData('customer_group', implode(',', $model->getData('customer_group')));
            $model->save();
            $this->messageManager->addSuccess(__('Staff was successfully saved'));
        }catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('staff_id')]);
        }
        if ($this->getRequest()->getParam('back') == 'edit') {
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}