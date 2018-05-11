<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Directpost\Payment;

use Magento\Payment\Block\Transparent\Iframe;

class Response extends \Magento\Authorizenet\Controller\Directpost\Payment
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * Response constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Authorizenet\Helper\DataFactory $dataFactory
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Authorizenet\Helper\DataFactory $dataFactory,
        \Magestore\Webpos\Helper\Data $helper
    ) {
        parent::__construct($context, $coreRegistry, $dataFactory);
        $this->helper = $helper;
    }

    /**
     * Response action.
     * Action for Authorize.net SIM Relay Request.
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /* @var $paymentMethod \Magento\Authorizenet\Model\DirectPost */
        $paymentMethod = $this->_objectManager->create('Magento\Authorizenet\Model\Directpost');

        $params = [];
        $result = [];
        if (!empty($data['x_invoice_num'])) {
            $result['x_invoice_num'] = $data['x_invoice_num'];
            $params['success'] = 1;
        }

        try {
            if (!empty($data['store_id'])) {
                $paymentMethod->setStore($data['store_id']);
            }
            $paymentMethod->process($data);
            $result['success'] = 1;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $result['success'] = 0;
            $result['error_msg'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $result['success'] = 0;
            $result['error_msg'] = __('We can\'t process your order right now. Please try again later.');
        }

        if (!empty($data['controller_action_name'])) {
            $result['controller_action_name'] = $data['controller_action_name'];
            $result['is_secure'] = isset($data['is_secure']) ? $data['is_secure'] : false;
            $params['redirect'] = $this->helper->getUrl('webpos/directpost_payment/redirect',$result);
        }
        $this->_coreRegistry->register(Iframe::REGISTRY_KEY, $params);
        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false)->renderLayout();
    }
}
