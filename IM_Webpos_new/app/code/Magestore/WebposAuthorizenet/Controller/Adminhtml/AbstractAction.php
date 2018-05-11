<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposAuthorizenet\Controller\Adminhtml;

/**
 * Class AbstractAction
 * @package Magestore\WebposAuthorizenet\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface
     */
    protected $authorizenetService;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magestore\WebposAuthorizenet\Helper\Data
     */
    protected $helper;

    /**
     * AbstractAction constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface $authorizenetService
     * @param \Magestore\WebposAuthorizenet\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface $authorizenetService,
        \Magestore\WebposAuthorizenet\Helper\Data $helper
    ){
        parent::__construct($context);
        $this->authorizenetService = $authorizenetService;
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
}