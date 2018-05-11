<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripe\Controller\Adminhtml;

/**
 * Class AbstractAction
 * @package Magestore\WebposStripe\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magestore\WebposStripe\Api\StripeServiceInterface
     */
    protected $stripeService;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magestore\WebposStripe\Helper\Data
     */
    protected $helper;

    /**
     * AbstractAction constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magestore\WebposStripe\Api\StripeServiceInterface $stripeService
     * @param \Magestore\WebposStripe\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magestore\WebposStripe\Api\StripeServiceInterface $stripeService,
        \Magestore\WebposStripe\Helper\Data $helper
    ){
        parent::__construct($context);
        $this->stripeService = $stripeService;
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