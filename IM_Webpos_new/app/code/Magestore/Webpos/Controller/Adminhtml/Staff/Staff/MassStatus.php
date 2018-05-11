<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Staff\Staff;

use Magento\Backend\App\Action\Context;
use Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Webpos\Model\ResourceModel\Staff\Staff\Collection;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Staff\MassDelete
 *
 * Mass status
 * Methods:
 *  massAction
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Staff
 * @module      Webpos
 * @author      Magestore Developer
 */
class MassStatus extends AbstractMassAction
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
        $staffChangeStatus = 0;
        foreach ($collection as $staff) {
            $staff->setStatus($this->getRequest()->getParam('status'))->save();
            $staffChangeStatus++;
        }

        if ($staffChangeStatus) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $staffChangeStatus));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

}
