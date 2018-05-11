<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Order\View;


use Zend\Stdlib\DateTime;

class DeliveryDate extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Warehouse constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\InventorySuccess\Model\OrderProcess\DataProvider\ShipmentView $shipmentViewDataProvider
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getDeliveryDateDisplay($order)
    {
        $html = '';
        if ($order->getId() && $order->getData('webpos_delivery_date')) {
            $date = new \DateTime($order->getData('webpos_delivery_date'));
            $html = $date->format('Y-m-d H:i:s');
        }
        return $html;
    }
}