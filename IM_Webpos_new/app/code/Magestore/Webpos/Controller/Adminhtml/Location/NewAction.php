<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Location;

use Magento\Framework\Controller\ResultFactory;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\NewAction
 * 
 * New location
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Location
 * @module      Webpos
 * @author      Magestore Developer
 */
class NewAction extends \Magestore\Webpos\Controller\Adminhtml\Location\AbstractLocation
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