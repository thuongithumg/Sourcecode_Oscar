<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Integration\Giftcard;

interface TemplateInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const TEMPLATE_ID = 'template_id';
    const TYPE = 'type';
    const TEMPLATE_NAME = 'template_name';
    const PATTERN = 'pattern';
    const BALANCE = 'balance';
    const CURRENCY = 'currency';
    const EXPIRED_AT = 'expired_at';
    const AMOUNT = 'amount';
    const DAY_TO_SEND = 'day_to_send';
    const STORE_ID = 'store_id';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const IS_GENERATED = 'is_generated';
    const GIFTCARD_TEMPLATE_IMAGE = 'giftcard_template_image';
    const GIFTCARD_TEMPLATE_ID = 'giftcard_template_id';

    /**#@-*/

    /**
     * Get template id
     *
     * @api
     * @return string
     */
    public function getTemplateId();

    /**
     * Get type
     *
     * @api
     * @return string|null
     */
    public function getType();

    /**
     * Get template name
     *
     * @api
     * @return string|null
     */
    public function getTemplateName();

    /**
     * Get pattern
     *
     * @api
     * @return string|null
     */
    public function getPattern();

    /**
     * Get balance
     *
     * @api
     * @return string|null
     */
    public function getBalance();
    /**
     * Get balance
     *
     * @api
     * @return string|null
     */
    public function getCurrency();
    /**
     * Get expired at
     *
     * @api
     * @return string|null
     */
    public function getExpiredAt();
    /**
     * Get amount
     *
     * @api
     * @return string|null
     */
    public function getAmount();
    /**
     * Get day to send
     *
     * @api
     * @return string|null
     */
    public function getDayToSend();
    /**
     * Get store id
     *
     * @api
     * @return string|null
     */
    public function getStoreId();
    /**
     * Get conditions serialized
     *
     * @api
     * @return string|null
     */
    public function getConditionsSerialized();
    /**
     * Get is generated
     *
     * @api
     * @return string|null
     */
    public function getIsGenerated();
    /**
     * Get gift card template id
     *
     * @api
     * @return string|null
     */
    public function getGiftcardTemplateId();
    /**
     * Get gift card template image
     *
     * @api
     * @return string|null
     */
    public function getGiftcardTemplateImage();
}
