<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposBambora\Controller\Poshub;
/**
 * Class SalesHttps
 * @package Magestore\Webpos\Controller\Index
 */

class SalesHttps extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultLayout = $this->_resultPageFactory->create();
        $html = $resultLayout->getLayout()->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Magestore_WebposBambora::poshub/sales_https.phtml')->toHtml();
        $this->getResponse()->setBody($html);
    }
}
