<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos\Save
 * 
 * Save location
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Save extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractPos
{

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = (int)$this->getRequest()->getParam('id');
        $lock = (int)$this->getRequest()->getParam('lock');
        $unlock = (int)$this->getRequest()->getParam('unlock');

        $data = $this->getRequest()->getPostValue();
        if ($lock == \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED) {
            $data['status'] = \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED;
        }
        if ($unlock == \Magestore\Webpos\Model\Pos\Status::STATUS_ENABLED) {
            $data['status'] = \Magestore\Webpos\Model\Pos\Status::STATUS_ENABLED;
        }
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        if ($modelId) {
            $model = $this->posFactory->create()
                ->load($modelId);
        } else {
            $model = $this->posFactory->create();
        }
        if(isset($data['denomination_ids'])){
            $denominationIds = $this->backendJsHelper->decodeGridSerializedInput($data['denomination_ids']);
            $data['denomination_ids'] = implode(',', $denominationIds);
        }
        $autoJoin = isset($data['auto_join']) ? $data['auto_join'] : 0;
        $model->setData($data);
        $model->setLocationId((int)$data['location_id']);
        try {
            if (isset($data['pin']) && (strlen($data['pin']) > 4 || strlen($data['pin']) < 4) ) {
                $this->messageManager->addErrorMessage(__('Security PIN only contains 4 numeric characters in length. Please try again!'));
                return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            } else {
                $this->posRepository->save($model);
                if ($this->getRequest()->getParam('lock')) {
                    $this->messageManager->addSuccessMessage(__('%1 was locked successfully.', $data['pos_name']));
                } else if ($this->getRequest()->getParam('unlock')) {
                    $this->messageManager->addSuccessMessage(__('%1 was unlocked successfully.', $data['pos_name']));
                } else {
                    $this->messageManager->addSuccessMessage(__('Pos was successfully saved'));
                }
            }
        }catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        if($autoJoin) {
            $this->posRepository->autoJoinAllStaffs($model->getId());
        }

        if ($this->getRequest()->getParam('back') == 'edit' || $this->getRequest()->getParam('lock')) {
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }


}