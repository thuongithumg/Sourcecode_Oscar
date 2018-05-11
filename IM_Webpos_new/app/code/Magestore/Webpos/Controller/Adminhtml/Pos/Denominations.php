<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * Class Denominations
 * @package Magestore\Webpos\Controller\Adminhtml\Pos
 */
class Denominations extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractPos
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('webpos.block.pos.denomination.grid')
            ->setDenominationIds($this->getRequest()->getPost('denomination_ids', null));
        return $resultLayout;
    }
}
