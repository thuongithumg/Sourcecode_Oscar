<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Report;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Report\Index
 * 
 * Report page
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Report\Dashboard
 * @module      Webpos
 * @author      Magestore Developer
 */
class Dashboard extends Report
{
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::reports');
        $resultPage->addBreadcrumb(__('Reports'), __('Reports'));
        $resultPage->getConfig()->getTitle()->prepend(__('Reports'));
        return $resultPage;
    }
}
