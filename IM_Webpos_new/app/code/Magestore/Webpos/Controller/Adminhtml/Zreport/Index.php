<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Zreport;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos\Index
 *
 * Delete pos
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Zreport
 * @module      Webpos
 * @author      Magestore Developer
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Zreport\AbstractZreport
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::zreport');
        $resultPage->addBreadcrumb(__('Z-Report'), __('Z-Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Z-Report'));
        return $resultPage;
    }
}
