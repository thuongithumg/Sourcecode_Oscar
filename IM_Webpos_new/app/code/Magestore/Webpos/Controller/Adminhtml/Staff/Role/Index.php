<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Role;
/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Role\Index
 *
 * List staff
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Role
 * @module      Webpos
 * @author      Magestore Developer
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Staff\Role
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::roles');
        $resultPage->addBreadcrumb(__('Role'), __('Role'));
        $resultPage->getConfig()->getTitle()->prepend(__('Role'));
        return $resultPage;
    }
}