<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Response;

/**
 * Class Giftcard
 * @package Magestore\Webpos\Model\Integration\Data
 */
class Giftcard extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magestore\Webpos\Api\Integration\Response\GiftcardInterface
{
    /**
     * Get existed codes
     *
     * @api
     * @return array
     */
    public function getExistedCodes()
    {
        return $this->getData(self::EXISTED_CODES);
    }

    /**
     * Set existed codes
     *
     * @api
     * @param array $existedCodes
     * @return $this
     */
    public function setExistedCodes($existedCodes)
    {
        return $this->setData(self::EXISTED_CODES, $existedCodes);
    }

    /**
     * Get used codes
     *
     * @api
     * @return array
     */
    public function getUsedCodes()
    {
        return $this->getData(self::USED_CODES);
    }

    /**
     * Set used codes
     *
     * @api
     * @param array $usedCodes
     * @return $this
     */
    public function setUsedCodes($usedCodes)
    {
        return $this->setData(self::USED_CODES, $usedCodes);
    }
}
