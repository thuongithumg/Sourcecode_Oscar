<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Staff;
/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Staff\Index
 *
 * List staff
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff
 * @module      Webpos
 * @author      Magestore Developer
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Staff\Staff
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::staffs');
        $resultPage->addBreadcrumb(__('Staff'), __('Staff'));
        $resultPage->getConfig()->getTitle()->prepend(__('Staff'));
        return $resultPage;
    }
}