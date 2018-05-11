<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * Class Sessions
 * @package Magestore\Webpos\Controller\Adminhtml\Pos
 */
class Sessions extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractPos
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('webpos.block.pos.sessions.grid')
            ->setDenominationIds($this->getRequest()->getPost('sessions_ids', null));
        return $resultLayout;
    }
}
