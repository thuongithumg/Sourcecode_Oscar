<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface CategoryInterface
{
    /**
     * Category id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Retrieve Name data wrapper
     *
     * @return string
     */
    public function getName();


    /**
     * Retrieve children ids
     *
     * @return string[]
     */
    public function getChildren();

    /**
     * Get category image
     *
     * @return string/null
     */
    public function getImage();

    /**
     * Get category image
     *
     * @return string/null
     */
    public function getPosition();

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel();

    /**
     * is first category
     * @return int
     */
    public function isFirstCategory();

    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId();

    /**
     * @codeCoverageIgnoreStart
     * @return string|null
     */
    public function getPath();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Catalog\Api\Data\ProductExtensionInterface|null
     */
//    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes
     * @return $this
     */
//    public function setExtensionAttributes(\Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes);
}
