<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Product;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ConfigOptionsInterface
 */
interface BarcodeOptionsInterface extends ExtensibleDataInterface
{
    /**#@+
     * Barcode Options object data keys
     */

    const KEY_CONFIG_OPTIONS = 'barcode_options';

    /**
     * Gets barcode options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\BarcodeOptionsInterface
     */
    public function getBarcodeOptions();

    /**
     * Sets barcode options
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Product\BarcodeOptionsInterface $barcodeOptions
     * @return $this
     */
    public function setBarcodeOptions(\Magestore\Webpos\Api\Data\Catalog\Product\BarcodeOptionsInterface $barcodeOptions);
}
