<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Cart;

interface TotalInterface
{
    const TITLE = 'title';
    const VALUE = 'value';
    const CODE = 'code';

    /**
     * Sets title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);
    
    /**
     * Gets title
     *
     * @return string.
     */
    public function getTitle();

    /**
     * Sets value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Gets value
     *
     * @return string.
     */
    public function getValue();

    /**
     * Sets code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Gets code
     *
     * @return string.
     */
    public function getCode();
}
