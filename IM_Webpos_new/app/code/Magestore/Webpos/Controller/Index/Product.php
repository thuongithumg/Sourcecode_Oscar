<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Index;
/**
 * Class Index
 * @package Magestore\Webpos\Controller\Index
 */

class Product extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $_storeManager;
    
    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_storeManager = $storeManager;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_storeManager->getStore()->setCurrentCurrencyCode('VND');
        $resultLayout = $this->_resultPageFactory->create();
      //  $resultLayout->getLayout()->getUpdate()->removeHandle('default');
        return $resultLayout;
    }
}
