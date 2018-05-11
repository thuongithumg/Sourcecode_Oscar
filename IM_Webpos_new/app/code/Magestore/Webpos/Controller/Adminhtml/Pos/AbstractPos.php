<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos
 * 
 * Abstract pos action class
 * Methods:
 *  _isAllowed
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
 */
abstract class AbstractPos extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosRepository
     */
    protected $posRepository;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosFactory
     */
    protected $posFactory;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $backendJsHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * AbstractPos constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magestore\Webpos\Model\Pos\PosRepository $posRepository
     * @param \Magestore\Webpos\Model\Pos\PosFactory $posFactory
     * @param \Magento\Backend\Helper\Js $backendJsHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magestore\Webpos\Model\Pos\PosRepository $posRepository,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magento\Backend\Helper\Js $backendJsHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->posRepository = $posRepository;
        $this->posFactory = $posFactory;
        $this->backendJsHelper = $backendJsHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }

    /**
     * @param string $key
     * @return string
     */
    public function getBodyParams($key = '')
    {
        $request = $this->getRequest();
        $content = $request->getContent();
        $content = \Zend_Json::decode($content);
        return ($key)?(isset($content[$key])?$content[$key]:''):$content;
    }

    /**
     * @param array $response
     */
    protected function _processResponseMessages($response)
    {
        if (!empty($response['notices'])) {
            foreach ($response['notices'] as $message) {
                $this->messageManager->addNotice($message);
            }
        }
        if (!empty($response['errors'])) {
            foreach ($response['errors'] as $message) {
                $this->messageManager->addError($message);
            }
        }
        if (!empty($response['success'])) {
            foreach ($response['success'] as $message) {
                $this->messageManager->addSuccess($message);
            }
        }
    }
}