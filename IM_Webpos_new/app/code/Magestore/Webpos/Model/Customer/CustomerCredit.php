<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Customer;
use Magestore\Webpos\Api\Data\Customer\CustomerComplainInterface;
/**
 * class \Magestore\Webpos\Model\Customer\CustomerComplain
 *
 * Web POS Customer Complain model
 * Use to work with Web POS complain table
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class CustomerComplain extends \Magento\Framework\Model\AbstractModel implements CustomerComplainInterface
{
    /**
     *
     */
    const BALANCE_AMOUNT = 'balance_amount';
    /**
     *
     */
    const BALANCE_DELTA = 'balance_delta';
    /**
     *
     */
    const ADDITIONAL_DELTA = 'additional_delta';
    /**
     *
     */
    const BALACE_ID = 'balance_id';
    /**
     *
     */
    const INDB = 'indexeddb_id';
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
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Customer\CustomerComplain');
    }

    /**
     * Get content params
     *
     * @api
     * @return string|null
     */
    public function getContent() {
        return $this->getData(self::CONTENT);
    }

    /**
     * Set content param
     *
     * @api
     * @param string $content
     * @return $this
     */
    public function setContent($content) {
        $this->setData(self::CONTENT, $content);
    }
    /**
     * Get customer email params
     *
     * @api
     * @return string|null
     */
    public function getCustomerEmail() {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * Set customer email param
     *
     * @api
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail) {
        $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }
    /**
     * Gets the created-at timestamp for the complain.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt()
    {
        $this->getData(self::CREATED_AT);
    }

    /**
     * Sets the created-at timestamp for the complain.
     *
     * @param string $createAt timestamp
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }
    /**
     * Get complain id params
     *
     * @api
     * @return string|null
     */
    public function getComplainId() {
        $this->getData(self::COMPLAIN_ID);
    }

    /**
     * Set complain param
     *
     * @api
     * @param string $complainId
     * @return $this
     */
    public function setComplainId($complainId) {
        $this->setData(self::COMPLAIN_ID, $complainId);
    }
    /**
     * Get complain id params
     *
     * @api
     * @return string|null
     */
    public function getIndexeddbId() {
        $this->getData(self::INDB);
    }

    /**
     * Set complain param
     *
     * @api
     * @param string $complainId
     * @return $this
     */
    public function setIndexeddbId($indexDb) {
        $this->setData(self::INDB, $indexDb);
    }
}