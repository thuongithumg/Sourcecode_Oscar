<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Service\Sales;

use Magento\Framework\Cache\Frontend\Adapter\Zend;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Model\Order;

/**
 * Class InvoiceService
 */
class InvoiceService extends \Magento\Sales\Model\Service\InvoiceService implements InvoiceManagementInterface
{
    /**
     * @param Order $order
     * @param array $qtys|null
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareInvoice(Order $order, array $qtys = null)
    {
        $invoice = $this->orderConverter->toInvoice($order);
        $totalQty = 0;
        foreach ($order->getAllItems() as $orderItem) {
            if (!$this->_canInvoiceItem($orderItem)) {
                continue;
            }
            $item = $this->orderConverter->itemToInvoiceItem($orderItem);
            if (!$qtys){
                $qty = 0;
            } else if ($orderItem->isDummy()) {
                $qty = $orderItem->getQtyOrdered() ? $orderItem->getQtyOrdered() : 1;
            } elseif (isset($qtys[$orderItem->getId()])) {
                $qty = (double) $qtys[$orderItem->getId()];
            } else {
                $qty = $orderItem->getQtyToInvoice();
            }
            $totalQty += $qty;
            $this->setInvoiceItemQuantity($item, $qty);
            $invoice->addItem($item);
        }
        $invoice->setTotalQty($totalQty);
        $invoice->collectTotals();
        $order->getInvoiceCollection()->addItem($invoice);
        return $invoice;
    }
}
