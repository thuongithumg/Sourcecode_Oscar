<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Denomination;

/**
 * Class Denomination
 * @package Magestore\Webpos\Model\Denomination
 */
class Denomination extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Denomination\DenominationInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_denomination';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Denomination\Denomination');
    }
    /**
     *  Get Denomination Id
     * @return string|null
     */
    public function getDenominationId(){
        return $this->getData(self::DENOMINATION_ID);
    }

    /**
     * Set Denomination Id
     *
     * @param string $denominationId
     * @return $this
     */
    public function setDenominationId($denominationId){
        return $this->setData(self::DENOMINATION_ID, $denominationId);
    }

    /**
     *  Get Denomination Id
     * @return string|null
     */
    public function getDenominationName(){
        return $this->getData(self::DENOMINATION_NAME);
    }

    /**
     * Set Denomination Name
     *
     * @param string $denominationName
     * @return $this
     */
    public function setDenominationName($denominationName){
        return $this->setData(self::DENOMINATION_NAME, $denominationName);
    }

    /**
     *  Get Denomination Id
     * @return float
     */
    public function getDenominationValue(){
        return $this->getData(self::DENOMINATION_VALUE);
    }

    /**
     * Set Denomination Name
     *
     * @param string $denominationName
     * @return $this
     */
    public function setDenominationValue($denominationValue){
        return $this->setData(self::DENOMINATION_VALUE, $denominationValue);
    }

    /**
     *  Get $sortOrder
     * @return float
     */
    public function getSortOrder(){
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Set $sortOrder
     *
     * @param string $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder){
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }
}