<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Shipping;
use Magestore\Webpos\Api\Data\Shipping\price;
use Magestore\Webpos\Api\Data\Shipping\ShippingInterface;

/**
 * Class Magestore\Webpos\Model\Shipping\Shipping
 *
 */
class Shipping extends \Magento\Framework\Model\AbstractModel implements
    \Magestore\Webpos\Api\Data\Shipping\ShippingInterface
{
    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get title
     *
     * @api
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set description
     *
     * @api
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get description
     *
     * @api
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set error message
     *
     * @api
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * Get error message
     *
     * @api
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->getData(self::ERROR_MESSAGE);
    }

    /**
     * Set price type
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setPriceType($priceType)
    {
        return $this->setData(self::PRICE_TYPE, $priceType);
    }

    /**
     * Get price type
     *
     * @api
     * @return string
     */
    public function getPriceType()
    {
        return $this->getData(self::PRICE_TYPE);
    }

    /**
     * Set price
     *
     * @api
     * @param string $price
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get price
     *
     * @api
     * @return string
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * Get is default
     *
     * @api
     * @return string
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * Set is default
     *
     * @api
     * @param string $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }


}
