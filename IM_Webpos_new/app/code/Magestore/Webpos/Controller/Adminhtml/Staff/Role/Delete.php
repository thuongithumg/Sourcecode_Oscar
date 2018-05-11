<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Role;

    /**
     * class \Magestore\Webpos\Controller\Adminhtml\Staff\Role\Delete
     *
     * Delete user
     * Methods:
     *  execute
     *
     * @category    Magestore
     * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Role
     * @module      Webpos
     * @author      Magestore Developer
     */
/**
 * Class Delete
 * @package Magestore\Webpos\Controller\Adminhtml\Staff\Role
 */
class Delete extends \Magestore\Webpos\Controller\Adminhtml\Staff\Role
{
    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $_roleFactory;


    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory
    ) {

        $this->_roleFactory = $roleFactory;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $userId = $this->getRequest()->getParam('id');
        if ($userId > 0) {
            $userModel = $this->_roleFactory->create()->load($this->getRequest()->getParam('id'));
            try {
                $userModel->delete();
                $this->messageManager->addSuccess(__('Role was successfully deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
