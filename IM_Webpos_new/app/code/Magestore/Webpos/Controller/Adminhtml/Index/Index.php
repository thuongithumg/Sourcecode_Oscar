<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Index;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Index\Index
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
class Index extends \Magento\Sales\Controller\Download\DownloadCustomOption
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl = $storeManager->getStore()->getBaseUrl();
        $url = $baseUrl.'webpos';
        header('Location: '.$url);
        $this->endExecute();
    }
}
