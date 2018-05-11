<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposPaynl\Controller;

/**
 * Class AbstractAction
 * @package Magestore\WebposPaynl\Controller
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magestore\WebposPaynl\Api\PaypalServiceInterface
     */
    protected $paypalService;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magestore\WebposPaynl\Helper\Data
     */
    protected $helper;

    /**
     * AbstractAction constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magestore\WebposPaynl\Api\PaypalServiceInterface $paypalService
     * @param \Magestore\WebposPaynl\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magestore\WebposPaynl\Api\PaynlServiceInterface $paypalService,
        \Magestore\WebposPaynl\Helper\Data $helper
    ){
        parent::__construct($context);
        $this->paypalService = $paypalService;
        $this->helper = $helper;
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createJsonResult($data){
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        return $resultJson->setData($data);
    }

    /**
     * @return mixed
     */
    public function createPageResult(){
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
