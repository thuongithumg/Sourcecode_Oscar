<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Role\Staff
 *
 * Staff tab
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Role
 * @module      Webpos
 * @author      Magestore Developer
 */
class Staff extends \Magestore\Webpos\Controller\Adminhtml\Staff\Role
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('role.edit.tab.staff')
            ->setStaffs($this->getRequest()->getPost('ostaff', null));
        return $resultLayout;
    }
}
