<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Customer;

interface CustomerComplainInterface
{
    /**
     * Get content params
     *
     * @api
     * @return string|null
     */
    public function getContent();

    /**
     * Set content param
     *
     * @api
     * @param string $content
     * @return $this
     */
    public function setContent($content);
    /**
     * Get customer email params
     *
     * @api
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer email param
     *
     * @api
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);
    /**
     * Gets the created-at timestamp for the complain.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Sets the created-at timestamp for the complain.
     *
     * @param string $createdAt timestamp
     * @return $this
     */
    public function setCreatedAt($createdAt);
    /**
     * Get complain id params
     *
     * @api
     * @return string|null
     */
    public function getComplainId();

    /**
     * Set complain param
     *
     * @api
     * @param string $complainId
     * @return $this
     */
    public function setComplainId($complainId);
    /**
     * Get indexed id params
     *
     * @api
     * @return string|null
     */
    public function getIndexeddbId();

    /**
     * Set indexed db param
     *
     * @api
     * @param string $indexDb
     * @return $this
     */
    public function setIndexeddbId($indexDb);

}
