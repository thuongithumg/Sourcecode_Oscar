<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

interface ConfigInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const PATH = 'path';
    const VALUE = 'value';
    /**#@-*/

    /**
     * Get path
     *
     * @api
     * @return string
     */
    public function getPath();

    /**
     * Set path
     *
     * @api
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Get value
     *
     * @api
     * @return string|null
     */
    public function getValue();

    /**
     * Set value
     *
     * @api
     * @param string $value
     * @return $this
     */
    public function setValue($value);

}
