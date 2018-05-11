<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Role;
/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Role\Save
 *
 * Save Role
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Role
 * @module      Webpos
 * @author      Magestore Developer
 */
class Save extends \Magestore\Webpos\Controller\Adminhtml\Staff\Role
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory);
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if (isset($data['role_id'])) {
            $roleId = $data['role_id'];
        } else {
            $roleId = null;
        }

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        if ($roleId) {
            $model = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Role')
                ->load($roleId);
        } else {
            $model = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Role');
        }
        $model->setData($data);
        try {
            $model->save();
            $roleId=$model->getRoleId();
            $resources = array();
            if (isset($data['all']) && $data['all']) {
                $resources = array('Magestore_Webpos::all');
            } else {
                if (isset($data['resource'])) {
                    $resources = $data['resource'];
                }
            }


            $authorizeRuleCollection = $this->_objectManager->create('Magestore\Webpos\Model\Staff\AuthorizationRule')
                ->getCollection()
                ->addFieldToFilter('role_id', $roleId);
            foreach ($authorizeRuleCollection as $authorizeRule) {
                $authorizeRule->delete();
            }

            foreach ($resources as $resource) {
                $authorizeRuleCollection = $this->_objectManager->create('Magestore\Webpos\Model\Staff\AuthorizationRule');
                $authorizeRuleCollection->setRoleId($roleId);
                $authorizeRuleCollection->setResourceId($resource);
                $authorizeRuleCollection->save();
            }

            if (isset($data['role_staff'])) {
                $staffArray = array();
                parse_str($data['role_staff'], $staffArray);
                $staffArray = array_keys($staffArray);

                $staffCollection = $this->_objectManager->create('Magestore\Webpos\Model\ResourceModel\Staff\Staff\Collection')
                    ->addFieldToFilter('role_id', $roleId);
                foreach ($staffCollection as $staff) {
                    $staffId = $staff->getId();
                    if ($staffId && !in_array($staffId,$staffArray)) {

                        $staff->setRoleId(0);
                        $staff->save();
                    }

                }

                foreach ($staffArray as $staff) {
                    if(is_numeric($staff)){
                        $staffModel = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')->load($staff);
                        $staffModel->setRoleId($roleId);
                        $staffModel->save();
                    }
                }
            }
            if($model->getData('maximum_discount_percent') > 100){
                $model->setData('maximum_discount_percent',100);
                $this->messageManager->addError(__('Maximum discount percent cannot be higher than 100'));
            }
            $model->save();
            $this->messageManager->addSuccess(__('Role was successfully saved'));
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