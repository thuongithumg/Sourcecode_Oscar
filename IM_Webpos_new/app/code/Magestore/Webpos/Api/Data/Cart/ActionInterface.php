<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Cart;

interface ActionInterface
{
    const CREATE_INVOICE = 'create_invoice';
    const CREATE_SHIPMENT = 'create_shipment';
    const DELIVERY_TIME = 'delivery_time';
    const SEND_SALE_EMAIL = 'send_sale_email';
    const FULFILL_ONLINE = 'fulfill_online';

    /**
     * Sets create invoice
     *
     * @param string $createInvoice
     * @return $this
     */
    public function setCreateInvoice($createInvoice);
    
    /**
     * Gets create invoice
     *
     * @return string.
     */
    public function getCreateInvoice();

    /**
     * Sets create shipment
     *
     * @param string $createShipment
     * @return $this
     */
    public function setCreateShipment($createShipment);

    /**
     * Gets create shipment
     *
     * @return string.
     */
    public function getCreateShipment();

    /**
     * Sets fullfill online
     *
     * @param string $fulfillOnline
     * @return $this
     */
    public function setFulfillOnline($fulfillOnline);

    /**
     * Gets fulfill online
     *
     * @return string.
     */
    public function getFulfillOnline();

    /**
     * Sets delivery time
     *
     * @param string $deliveryTime
     * @return $this
     */
    public function setDeliveryTime($deliveryTime);

    /**
     * Gets create invoice
     *
     * @return string.
     */
    public function getDeliveryTime();

    /**
     *
     * @param string $sendSaleEmail
     * @return $this
     */
    public function setSendSaleEmail($sendSaleEmail);

    /**
     *
     * @return string.
     */
    public function getSendSaleEmail();
}
