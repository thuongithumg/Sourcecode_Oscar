<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magestore\Webpos\Controller\Adminhtml\Pos;

use Magento\Backend\App\Action;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos\InlineEdit
 * 
 * Inline edit pos
 * Methods:
 *  execute
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
 */
class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var \Magento\Framework\Controller\Result\JsonFactory  */
    protected $resultJsonFactory;

    /** @var \Magento\Framework\Api\DataObjectHelper  */
    protected $dataObjectHelper;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $model = $this->_objectManager->create('Magestore\Webpos\Model\Pos\Pos');
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $posId) {
            $model->setData($postItems[$posId]);
            $model->setId($posId);
            $model->save();
        }

        return $resultJson->setData([
            'messages' => $this->getErrorMessages(),
            'error' => $this->isErrorExists(),

        ]);
    }
    
    /**
     * 
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }


    /**
     * Get array with errors
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    /**
     * Check if errors exists
     *
     * @return bool
     */
    protected function isErrorExists()
    {
        return (bool)$this->getMessageManager()->getMessages(true)->getCount();
    }

}
