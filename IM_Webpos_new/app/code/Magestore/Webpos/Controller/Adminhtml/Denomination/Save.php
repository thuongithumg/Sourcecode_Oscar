<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Denomination;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Denomination\Save
 * 
 * Save Denomination
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Denomination
 * @module      Webpos
 * @author      Magestore Developer
 */
class Save extends \Magestore\Webpos\Controller\Adminhtml\Denomination\AbstractDenomination
{

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = (int)$this->getRequest()->getParam('denomination_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        if ($modelId) {
            $model = $this->denominationFactory->create()
                ->load($modelId);
        } else {
            $model = $this->denominationFactory->create();
        }
        $model->setData($data);
        try {
            $this->denominationRepository->save($model);
            $this->messageManager->addSuccessMessage(__('Denomination was successfully saved'));
        }catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        if ($this->getRequest()->getParam('back') == 'edit') {
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }


}