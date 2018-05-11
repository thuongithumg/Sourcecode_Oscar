<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos\Index
 * 
 * Delete pos
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractPos
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::pos');
        $resultPage->addBreadcrumb(__('POS'), __('POS'));
        $resultPage->getConfig()->getTitle()->prepend(__('POS'));
        return $resultPage;
    }
}
