<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

use Magento\Backend\App\Action\Context;
use Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos\MassDelete
 * 
 * Mass delete pos
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $collectionFactory);
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(Collection $collection)
    {
        $modelDeleted = 0;
        foreach ($collection as $model) {
            $model->delete();
            $modelDeleted++;
        }

        if ($modelDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $modelDeleted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

}