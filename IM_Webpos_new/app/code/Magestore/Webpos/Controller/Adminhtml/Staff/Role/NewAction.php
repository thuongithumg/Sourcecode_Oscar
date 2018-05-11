<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Staff\Role;
use Magento\Framework\Controller\ResultFactory;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Role\NewAction
 *
 * new Role
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Role
 * @module      Webpos
 * @author      Magestore Developer
 */
class NewAction extends \Magestore\Webpos\Controller\Adminhtml\Staff\Role
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}
