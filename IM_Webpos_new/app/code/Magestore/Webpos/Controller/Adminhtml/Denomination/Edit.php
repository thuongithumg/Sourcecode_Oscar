<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Denomination;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Denomination\Edit
 *
 * Delete Denomination
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Denomination
 * @module      Webpos
 * @author      Magestore Developer
 */
class Edit extends \Magestore\Webpos\Controller\Adminhtml\Denomination\AbstractDenomination
{

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Magestore\Webpos\Model\Denomination\Denomination');
        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');
        if ($id) {
            $model = $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This denomination no longer exists.'));
                return $resultRedirect->setPath('webposadmin/*/', ['_current' => true]);
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('current_denomination', $model);
        $resultPage = $this->_resultPageFactory->create();
        if (!$model->getId()) {
            $pageTitle = __('New Denomination');
        } else {
            $pageTitle =  __('Edit Denomination %1', $model->getDenominationName());
        }
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }

}