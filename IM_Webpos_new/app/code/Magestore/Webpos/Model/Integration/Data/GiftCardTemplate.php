<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Data;

/**
 * Class Magestore\Webpos\Model\Integration\Data
 *
 */
class GiftCardTemplate extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magestore\Webpos\Api\Data\Integration\Giftcard\TemplateInterface
{
    /**
     * Get template id
     *
     * @api
     * @return string
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * Get type
     *
     * @api
     * @return string|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Get template name
     *
     * @api
     * @return string|null
     */
    public function getTemplateName()
    {
        return $this->getData(self::TEMPLATE_NAME);
    }

    /**
     * Get pattern
     *
     * @api
     * @return string|null
     */
    public function getPattern()
    {
        return $this->getData(self::PATTERN);
    }

    /**
     * Get balance
     *
     * @api
     * @return string|null
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }
    /**
     * Get balance
     *
     * @api
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }
    /**
     * Get expired at
     *
     * @api
     * @return string|null
     */
    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }
    /**
     * Get amount
     *
     * @api
     * @return string|null
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }
    /**
     * Get day to send
     *
     * @api
     * @return string|null
     */
    public function getDayToSend()
    {
        return $this->getData(self::DAY_TO_SEND);
    }
    /**
     * Get store id
     *
     * @api
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }
    /**
     * Get conditions serialized
     *
     * @api
     * @return string|null
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }
    /**
     * Get is generated
     *
     * @api
     * @return string|null
     */
    public function getIsGenerated()
    {
        return $this->getData(self::IS_GENERATED);
    }
    /**
     * Get gift card template id
     *
     * @api
     * @return string|null
     */
    public function getGiftcardTemplateId()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_ID);
    }
    /**
     * Get gift card template image
     *
     * @api
     * @return string|null
     */
    public function getGiftcardTemplateImage()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_IMAGE);
    }
}
