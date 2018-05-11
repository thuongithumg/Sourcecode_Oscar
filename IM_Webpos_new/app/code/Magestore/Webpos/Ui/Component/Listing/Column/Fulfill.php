<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Price
 */
class Fulfill extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        OrderRepositoryInterface $orderRepository,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = []
    ) {
        $this->_orderRepository = $orderRepository;
        $this->priceFormatter = $priceFormatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $order  = $this->_orderRepository->get($item["entity_id"]);
                $fulfill = $order->getData('fulfill_online');
                switch ($fulfill) {
                    case "0":
                        $fulfillOnline = "No";
                        break;
                    case "1";
                        $fulfillOnline = "Yes";
                        break;
                    default:
                        $fulfillOnline = "No";
                        break;
                }
                $item[$this->getData('name')] = $fulfillOnline;
            }
        }
        return $dataSource;
    }
}
