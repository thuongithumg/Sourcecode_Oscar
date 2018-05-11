<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Role\UserGrid
 *
 * Staff Grid
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Role
 * @module      Webpos
 * @author      Magestore Developer
 */
class StaffGrid extends \Magestore\Webpos\Controller\Adminhtml\Staff\Role
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
