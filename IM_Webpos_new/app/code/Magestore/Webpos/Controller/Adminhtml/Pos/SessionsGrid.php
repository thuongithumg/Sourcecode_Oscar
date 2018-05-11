<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * Class SessionsGrid
 * @package Magestore\Webpos\Controller\Adminhtml\Pos
 */
class SessionsGrid extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractPos
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}
