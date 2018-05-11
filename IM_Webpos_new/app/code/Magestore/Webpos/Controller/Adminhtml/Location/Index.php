<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Location;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\Index
 * 
 * Delete location
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Location
 * @module      Webpos
 * @author      Magestore Developer
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Location\AbstractLocation
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::locations');
        $resultPage->addBreadcrumb(__('Location'), __('Location'));
        $resultPage->getConfig()->getTitle()->prepend(__('Location'));
        return $resultPage;
    }
}
