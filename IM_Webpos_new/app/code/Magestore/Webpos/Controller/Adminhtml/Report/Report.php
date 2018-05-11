<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Report;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Report
 * 
 * Abstract report action class
 * Methods:
 *  _isAllowed
 *  createBlock
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml
 * @module      Webpos
 * @author      Magestore Developer
 */
abstract class Report extends \Magento\Backend\App\Action
{
    /**
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory 
     */
    protected $_resultJsonFactory;
    
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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::reports');
    }
    
    /**
     * 
     * @param string $class
     * @param string $name
     * @param string $template
     * @return block type
     */
    public function createBlock($class,$name = '',$template = ""){
        $block = "";
        try{
            $block = $this->_view->getLayout()->createBlock($class,$name);
            if($block && $template != ""){
                $block->setTemplate($template);
            }
        }catch(\Exception $e){
            return $e->getMessage();
        }
        return $block;
    }
}