<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos\Delete
 * 
 * Delete Pos
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Delete extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractPos
{
    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = $this->getRequest()->getParam('id');
        if ($modelId > 0) {
            $model = $this->posFactory->create()->load($this->getRequest()->getParam('id'));
            try {
                $model->delete();
                $this->messageManager->addSuccessMessage(__('Pos was successfully deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }


}