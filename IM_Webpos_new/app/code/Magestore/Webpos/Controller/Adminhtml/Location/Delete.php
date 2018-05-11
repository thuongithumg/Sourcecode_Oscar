<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Location;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\Delete
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
class Delete extends \Magestore\Webpos\Controller\Adminhtml\Location\AbstractLocation
{
    /**
     *
     * @var \Magestore\Webpos\Model\Location\LocationFactory 
     */
    protected $_modelFactory;
    
    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magestore\Webpos\Model\Location\LocationFactory $modelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magestore\Webpos\Model\Location\LocationFactory $modelFactory
    ) {

        $this->_modelFactory = $modelFactory;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelId = $this->getRequest()->getParam('id');
        if ($modelId > 0) {
            $model = $this->_modelFactory->create()->load($this->getRequest()->getParam('id'));
            try {
                $model->delete();
                $this->messageManager->addSuccess(__('Location was successfully deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }


}