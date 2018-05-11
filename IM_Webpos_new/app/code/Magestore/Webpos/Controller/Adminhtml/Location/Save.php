<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Location;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\Save
 * 
 * Save location
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Location
 * @module      Webpos
 * @author      Magestore Developer
 */
class Save extends \Magestore\Webpos\Controller\Adminhtml\Location\AbstractLocation
{

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = (int)$this->getRequest()->getParam('location_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        if ($modelId) {
            $model = $this->_objectManager->create('Magestore\Webpos\Model\Location\Location')
                ->load($modelId);
        } else {
            $model = $this->_objectManager->create('Magestore\Webpos\Model\Location\Location');
        }

        $model->setData($data);

        try {
            $model->save();
            $this->messageManager->addSuccess(__('Location was successfully saved'));
        }catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        if ($this->getRequest()->getParam('back') == 'edit') {
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }


}