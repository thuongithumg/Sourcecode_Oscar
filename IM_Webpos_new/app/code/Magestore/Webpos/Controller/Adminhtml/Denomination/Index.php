<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Denomination;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Denomination\Index
 * 
 * Delete Denomination
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Denomination
 * @module      Webpos
 * @author      Magestore Developer
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Denomination\AbstractDenomination
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::denomination');
        $resultPage->addBreadcrumb(__('Denomination'), __('Denomination'));
        $resultPage->getConfig()->getTitle()->prepend(__('Denomination'));
        return $resultPage;
    }
}
