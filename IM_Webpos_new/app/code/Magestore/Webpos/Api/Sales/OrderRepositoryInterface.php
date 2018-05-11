<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Sales;

/**
 * Order repository interface.
 *
 * An order is a document that a web store issues to a customer. Magento generates a sales order that lists the product
 * items, billing and shipping addresses, and shipping and payment methods. A corresponding external document, known as
 * a purchase order, is emailed to the customer.
 * @api
 */
interface OrderRepositoryInterface
{
    /**
     * Lists orders that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria The search criteria.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderSearchResultInterface Order search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Loads a specified order.
     *
     * @param int $id The order ID.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface WebposOrder interface.
     */
    public function get($id);

    /**
     * Loads a specified order.
     *
     * @param string $id The order Increment ID.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface WebposOrder interface.
     */
    public function getByIncrementId($id);

    /**
     * Deletes a specified order.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $entity The order ID.
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\OrderInterface $entity);

    /**
     * Performs persist operations for a specified order.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $entity The order.
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     */
    public function save(\Magento\Sales\Api\Data\OrderInterface $entity);

    /**
     * Cancels a specified order.
     *
     * @param int $id The order ID.
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryInterface|null $comment Status history comment.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface WebposOrder interface.
     */
    public function cancel($id,\Magento\Sales\Api\Data\OrderStatusHistoryInterface $comment = null);
    
    /**
     * Adds a comment to a specified order.
     *
     * @param int $id The order ID.
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryInterface $statusHistory Status history comment.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface WebposOrder interface.
     */
    public function addComment($id, \Magento\Sales\Api\Data\OrderStatusHistoryInterface $statusHistory);
    
    /**
     * Emails a user a specified order.
     *
     * @param int $id The order ID.
     * @param string|null $email the customer email
     * @return bool
     */
    public function notify($id, $email);

    /**
     * Unhold holded order
     *
     * @param int $id The order ID.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface WebposOrder interface.
     */
    public function unhold($id);
}
