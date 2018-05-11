<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Cart\Data;

/**
 * Class Action
 * @package Magestore\Webpos\Model\Cart\Data
 */
class Action extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Cart\ActionInterface
{
    /**
     * Sets create invoice
     *
     * @param string $createInvoice
     * @return $this
     */
    public function setCreateInvoice($createInvoice)
    {
        return $this->setData(self::CREATE_INVOICE, $createInvoice);
    }

    /**
     * Gets create invoice
     *
     * @return string.
     */
    public function getCreateInvoice()
    {
        return $this->getData(self::CREATE_INVOICE);
    }

    /**
     * Sets create shipment
     *
     * @param string $createShipment
     * @return $this
     */
    public function setCreateShipment($createShipment)
    {
        return $this->setData(self::CREATE_SHIPMENT, $createShipment);
    }

    /**
     * Gets create shipment
     *
     * @return string.
     */
    public function getCreateShipment()
    {
        return $this->getData(self::CREATE_SHIPMENT);
    }

    /**
     * Sets fulfill Online
     *
     * @param string $fulfillOnline
     * @return $this
     */
    public function setFulfillOnline($fulfillOnline)
    {
        return $this->setData(self::FULFILL_ONLINE, $fulfillOnline);
    }

    /**
     * Gets fulfill online
     *
     * @return string.
     */
    public function getFulfillOnline()
    {
        return $this->getData(self::FULFILL_ONLINE);
    }

    /**
     * Sets delivery time
     *
     * @param string $deliveryTime
     * @return $this
     */
    public function setDeliveryTime($deliveryTime)
    {
        return $this->setData(self::DELIVERY_TIME, $deliveryTime);
    }

    /**
     * Gets create invoice
     *
     * @return string.
     */
    public function getDeliveryTime()
    {
        return $this->getData(self::DELIVERY_TIME);
    }

    /**
     * Sets send email
     *
     * @param string $sendSaleEmail
     * @return $this
     */
    public function setSendSaleEmail($sendSaleEmail)
    {
        return $this->setData(self::SEND_SALE_EMAIL, $sendSaleEmail);
    }

    /**
     * Gets send email
     *
     * @return $this
     */
    public function getSendSaleEmail()
    {
        return $this->getData(self::SEND_SALE_EMAIL);
    }
}