<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Zreport;


class Detail extends \Magestore\Webpos\Controller\Adminhtml\Zreport\AbstractZreport
{

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Magestore\Webpos\Model\Shift\Shift');
        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');
        if ($id) {
            $model = $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This session no longer exists.'));
                return $resultRedirect->setPath('webposadmin/*/', ['_current' => true]);
            }
        }
        $registryObject->register('current_zreport', $model);
        $resultPage = $this->_resultPageFactory->create();
        $pageTitle =  __('Session %1', $model->getShiftId());
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }

}