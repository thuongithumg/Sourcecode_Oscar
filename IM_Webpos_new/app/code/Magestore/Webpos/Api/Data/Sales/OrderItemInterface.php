<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Sales;

/**
 * Order item interface.
 *
 * An order is a document that a web store issues to a customer. Magento generates a sales order that lists the product
 * items, billing and shipping addresses, and shipping and payment methods. A corresponding external document, known as
 * a purchase order, is emailed to the customer.
 * @api
 */
interface OrderItemInterface extends \Magento\Sales\Api\Data\OrderItemInterface
{
    /**
     * Customer credit discount amount
     */
    const CUSTOMERCREDIT_DISCOUNT = 'customercredit_discount';

    /**
     * Customer credit base discount amount
     */
    const BASE_CUSTOMERCREDIT_DISCOUNT = 'base_customercredit_discount';

    /*
   * customer credit discount.
   */
    const CUSTOMER_BALANCE_AMOUNT = "customer_balance_amount";

    /*
     * base customer credit discount.
     */
    const BASE_CUSTOMER_BALANCE_AMOUNT = "base_customer_balance_amount";

    /**
     * Rewardpoints earn point
     */
    const REWARDPOINTS_EARN = 'rewardpoints_earn';

    /**
     * Rewardpoints spent point
     */
    const REWARDPOINTS_SPENT = 'rewardpoints_spent';

    /**
     * Rewardpoints discount amount
     */
    const REWARDPOINTS_DISCOUNT = 'rewardpoints_discount';

    /**
     * Rewardpoints base discount amount
     */
    const REWARDPOINTS_BASE_DISCOUNT = 'rewardpoints_base_discount';

    /**
     * Gift Card discount amount
     */
    const GIFT_VOUCHER_DISCOUNT = 'gift_voucher_discount';

    /**
     * Gift Card base discount amount
     */
    const BASE_GIFT_VOUCHER_DISCOUNT = 'base_gift_voucher_discount';

    /**
     * Gets customer credit discount.
     *
     * @return float|null Base cost.
     */
    public function getCustomercreditDiscount();

    /**
     * Sets customer credit discount.
     *
     * @param float $discount
     * @return $this
     */
    public function setCustomercreditDiscount($discount);

    /**
     * Gets customer credit base discount.
     *
     * @return float|null Base cost.
     */
    public function getBaseCustomercreditDiscount();

    /**
     * Sets customer credit base discount.
     *
     * @param float $baseDiscount
     * @return $this
     */
    public function setBaseCustomercreditDiscount($baseDiscount);

    /**
     * Gets rewardpoints earn point.
     *
     * @return float|null Base cost.
     */
    public function getRewardpointsEarn();

    /**
     * Sets rewardpoints earn point.
     *
     * @param float $earnPoint
     * @return $this
     */
    public function setRewardpointsEarn($earnPoint);

    /**
     * Gets rewardpoints spent point.
     *
     * @return float|null Base cost.
     */
    public function getRewardpointsSpent();

    /**
     * Sets rewardpoints spent point.
     *
     * @param float $spentPoint
     * @return $this
     */
    public function setRewardpointsSpent($spentPoint);

    /**
     * Gets rewardpoints discount.
     *
     * @return float|null Base cost.
     */
    public function getRewardpointsDiscount();

    /**
     * Sets rewardpoints discount.
     *
     * @param float $discount
     * @return $this
     */
    public function setRewardpointsDiscount($discount);

    /**
     * Gets rewardpoints base discount.
     *
     * @return float|null Base cost.
     */
    public function getRewardpointsBaseDiscount();

    /**
     * Sets rewardpoints base discount.
     *
     * @param float $baseDiscount
     * @return $this
     */
    public function setRewardpointsBaseDiscount($baseDiscount);

    /**
     * Gets giftcard discount.
     *
     * @return float|null Base cost.
     */
    public function getGiftVoucherDiscount();

    /**
     * Sets giftcard discount.
     *
     * @param float $discount
     * @return $this
     */
    public function setGiftVoucherDiscount($discount);

    /**
     * Gets giftcard base discount.
     *
     * @return float|null Base cost.
     */
    public function getBaseGiftVoucherDiscount();

    /**
     * Sets giftcard base discount.
     *
     * @param float $baseDiscount
     * @return $this
     */
    public function setBaseGiftVoucherDiscount($baseDiscount);

}
