<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Role\Edit
 *
 * Edit role
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Role
 * @module      Webpos
 * @author      Magestore Developer
 */
class Edit extends \Magestore\Webpos\Controller\Adminhtml\Staff\Role
{

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Role');
        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');
        if ($id) {
            $model = $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This role no longer exists.'));
                return $resultRedirect->setPath('webposadmin/*/', ['_current' => true]);
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('current_role', $model);
        $resultPage = $this->_resultPageFactory->create();
        if (!$model->getId()) {
            $pageTitle = __('New Role');
        } else {
            $pageTitle =  __('Edit Role %1', $model->getDisplayName());
        }
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }

}
