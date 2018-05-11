<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog;

interface SwatchInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const ATTRIBUTE_ID = 'attribute_id';
    const ATTRIBUTE_CODE = 'attribute_code';
    const ATTRIBUTE_LABEL = 'attribute_label';
    const SWATCHES = 'swatches';
    /**#@-*/

    /**
     * Get attribute id
     *
     * @api
     * @return string
     */
    public function getAttributeId();

    /**
     * Set path
     *
     * @api
     * @param string $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId);

    /**
     * Get attribute code
     *
     * @api
     * @return string|null
     */
    public function getAttributeCode();

    /**
     * Set attribute code
     *
     * @api
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode);

    /**
     * Get attribute label
     *
     * @api
     * @return string|null
     */
    public function getAttributeLabel();

    /**
     * Set attribute label
     *
     * @api
     * @param string $attributeLabel
     * @return $this
     */
    public function setAttributeLabel($attributeLabel);

    /**
     * Get swatches
     *
     * @api
     * @return array
     */
    public function getSwatches();

    /**
     * Set swatches
     *
     * @api
     * @param array $swatches
     * @return $this
     */
    public function setSwatches($swatches);

}
