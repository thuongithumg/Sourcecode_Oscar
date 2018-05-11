<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface ProductInterface extends ProductOriginalInterface
{
    /**
     * Get list of product options
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]|null
     */
    public function getCustomOptions();

    /**
     * Get list of product config options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\ConfigOptionsInterface[]|null
     */
    public function getConfigOptions();

    /**
     * Get list of product bundle options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\BundleOptionsInterface[]|null
     */
    public function getBundleOptions();

    /**
     * Get list of product grouped options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\GroupedOptionsInterface[]|null
     */
    public function getGroupedOptions();

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig();

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getPriceConfig();

    /**
     * Get barcode options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\BarcodeOptionsInterface[]|null
     */
    public function getBarcodeOptions();

    /**
     * Get stocks data by product
     *
     * @return \Magestore\Webpos\Api\Data\Inventory\StockItemInterface[]
     */
    public function getStocks();

    /**
     * Get data of children product
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductInterface[]|null
     */
    public function getChildrenProducts();

    /**
     * Get qty in online mode
     *
     * @return float|null
     */
    public function getQtyOnline();
}
