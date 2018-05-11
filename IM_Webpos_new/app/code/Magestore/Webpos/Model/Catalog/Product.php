<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog;
/**
 * Catalog product model
 *
 * @method Product setHasError(bool $value)
 * @method \Magento\Catalog\Model\ResourceModel\Product getResource()
 * @method null|bool getHasError()
 * @method Product setAssociatedProductIds(array $productIds)
 * @method array getAssociatedProductIds()
 * @method Product setNewVariationsAttributeSetId(int $value)
 * @method int getNewVariationsAttributeSetId()
 * @method int getPriceType()
 * @method \Magento\Catalog\Model\ResourceModel\Product\Collection getCollection()
 * @method string getUrlKey()
 * @method Product setUrlKey(string $urlKey)
 * @method Product setRequestPath(string $requestPath)
 * @method Product setWebsiteIds(array $ids)
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends \Magento\Catalog\Model\Product
    implements \Magestore\Webpos\Api\Data\Catalog\ProductInterface
{

    /** @var \Magento\Catalog\Model\Product */
    protected $_product;

    protected $_chidrenCollection;

    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->load($this->getId());
        }
        return $this->_product;
    }

    /**
     * get product type instance
     * @param type $product
     * @return type
     */
    protected function _getProductTypeInstance($product = null)
    {
        if (is_null($product))
            $product = $this;
        $type = '';
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            $type = 'Magento\ConfigurableProduct\Model\Product\Type\Configurable';
        return \Magento\Framework\App\ObjectManager::getInstance()->get($type);
    }

    /**
     * Product short description
     *
     * @return string|null
     */
    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    /**
     * Product description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Get stock data by product
     *
     * @return array/null
     */
    public function getStock()
    {
        return "";
        /** @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry */
        $stockRegistry = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\CatalogInventory\Api\StockRegistryInterface'
        );
        try {
            $stockData = $stockRegistry->getStockItem($this->getId())->getData();
            return [$stockData];
        } catch (\Exception $w) {
            return;
        }
    }

    /**
     * Retrieve images
     *
     * @return array/null
     */
    public function getImages()
    {
        $product = $this->getProduct();
        $images = [];
        if (!empty($product->getMediaGallery('images'))) {
            foreach ($product->getMediaGallery('images') as $image) {
                if ((isset($image['disabled']) && $image['disabled']) || empty($image['value_id'])) {
                    continue;
                }
                $images[] = $this->getMediaConfig()->getMediaUrl($image['file']);
            }
        }
        return $images;
    }

    /**
     * Sets product image from it's child if possible
     *
     * @return string
     */
    public function getImage()
    {
        $imageHelper = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Helper\Image'
        );

        $appEmulation = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Store\Model\App\Emulation'
        );
        $storeId = $this->_storeManager->getStore()->getId();
        $appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $imgUrl = $imageHelper
            ->init($this, 'category_page_grid')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize(310, 350)
            ->getUrl();
        if (!$imgUrl) {
            $block = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Block\Webpos'
            );
            $imgUrl = $block->getViewFileUrl('Magestore_Webpos::images/category/image.jpg');
        }
        $appEmulation->stopEnvironmentEmulation();
        return $imgUrl;
    }

    /**
     * Get list of product options
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]|null
     */
    public function getCustomOptions()
    {
        return $this->getProduct()->getOptions();
    }

    /**
     * Get list of product config options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\ConfigOptionsInterface[]|null
     */
    public function getConfigOptions()
    {
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $product = $this;
            $productTypeInstance = $this->_getProductTypeInstance($product);
            $productAttributeOptions = $productTypeInstance->getConfigurableAttributesAsArray($product);
            $options = array();
            foreach ($productAttributeOptions as $productAttributeOption) {
                $values = $productAttributeOption['values'];
                $optionId = $productAttributeOption['attribute_id'];
                $code = $productAttributeOption['attribute_code'];
                $optionLabel = $productAttributeOption['label'];
                $options[$code]['optionId'] = $optionId;
                $options[$code]['optionLabel'] = $optionLabel;
                foreach ($values as $value) {
                    $optionValueId = $value['value_index'];
                    $val = $value['label'];
                    $options[$code][$optionValueId] = $val;
                }
            }
            return $options;

        }
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        /** if product is configurable */
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            /** @var \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurable */
            $configurable = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable'
            );
            $currentProduct = $this;
            $configurable->setProduct($currentProduct);
            return $configurable->getJsonConfig();
        }
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getPriceConfig()
    {
        if ($this->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            return;
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this;//->getProduct();
        /** @var \Magento\Framework\Locale\FormatInterface $localeFormat */
        $localeFormat = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magento\Framework\Locale\FormatInterface'
        );
        /** @var  \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency */
        $priceCurrency = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magento\Framework\Pricing\PriceCurrencyInterface'
        );

        /** @var \Magento\Framework\Json\EncoderInterface $jsonEncoder */
        $jsonEncoder = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magento\Framework\Json\EncoderInterface'
        );
        if (!$this->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $localeFormat->getPriceFormat()
            ];
            return $jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        if (!empty($tierPricesList)) {
            foreach ($tierPricesList as $tierPrice) {
                $value = ($tierPrice['price']) ? $tierPrice['price']->getValue() : 0;
                $tierPrices[] = $priceCurrency->convert($value);
            }
        }
        $oldPrice = ($product->getPriceInfo()->getPrice('regular_price')->getAmount()) ? $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue() : 0;
        $basePrice = ($product->getPriceInfo()->getPrice('final_price')->getAmount()) ? $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount() : 0;
        $finalPrice = ($product->getPriceInfo()->getPrice('final_price')->getAmount()) ? $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue() : 0;

        $config = [
            'productId' => $product->getId(),
            'priceFormat' => $localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $priceCurrency->convert(
                        $oldPrice
                    ),
                    'adjustments' => []
                ],
                'basePrice' => [
                    'amount' => $priceCurrency->convert(
                        $finalPrice
                    ),
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount' => $priceCurrency->convert(
                        $finalPrice
                    ),
                    'adjustments' => []
                ]
            ],
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        ];

        return $jsonEncoder->encode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getTypeInstance()->hasOptions($this)) {
            return true;
        }

        if ($this->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return true;
        }

        return false;
    }

    /**
     * Get list of product bundle options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\BundleOptionsInterface[]|null
     */
    public function getBundleOptions()
    {
        if ($this->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $bundleChilds = [];
            $product = $this;
            $store_id = $this->_storeManager->getStore()->getId();
            $options = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Bundle\Model\Option'
            )->getResourceCollection()
                ->setProductIdFilter($product->getId())
                ->setPositionOrder();
            $options->joinValues($store_id);
            $typeInstance = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Bundle\Model\Product\Type'
            );
            $selections = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
            foreach ($options->getItems() as $option) {
                $optionChilds = [];
                $optionChilds['title'] = $option->getTitle();
                $optionChilds['required'] = $option->getRequired();
                $optionChilds['type'] = $option->getType();
                $optionChilds['id'] = $option->getId();
                $optionChilds['product_id'] = $product->getId();
                $optionChilds['shipment_type'] = $product->getData('shipment_type');
                $itemChilds = [];
                foreach ($selections as $selection) {
                    if ($option->getId() == $selection->getOptionId()) {
                        if (!$this->checkStock($selection->getEntityId())) {
                            continue;
                        }
                        /** get tier price of child options in bundle product */
                        $selection->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
                        $itemChilds[] = $selection->getData();
                    }
                }
                $optionChilds['items'] = $itemChilds;
                $bundleChilds[] = $optionChilds;
            };
            return $bundleChilds;
        }
    }

    /**
     * check product in stock
     *
     * @param $productId
     * @return bool
     */
    public function checkStock($productId)
    {
        $inStock = true;
        $product = \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magestore\Webpos\Model\Catalog\ProductRepository'
        )->getProductById($productId);

        if (!$product->getId()) {
            $inStock = false;
        } else {
            $stockRegistry = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\CatalogInventory\Api\StockRegistryInterface'
            );
            $stockData = $stockRegistry->getStockItem($product->getId());
            if ($stockData->getData('manage_stock') && !$stockData->getData('backorders')
                && ($stockData->getData('qty') <= 0 || !$stockData->getData('is_in_stock'))
            ) {
                $inStock = false;
            }
        }
        return $inStock;
    }

    /**
     * Get list of product grouped options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\GroupedOptionsInterface[]|null
     */
    public function getGroupedOptions()
    {
        if ($this->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $childProducts = [];
            $product = $this;
            $typeInstance = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\GroupedProduct\Model\Product\Type\Grouped'
            );
            $childs = $typeInstance->getAssociatedProducts($product);
            if (!empty($childs)) {
                $childItem = [];
                foreach ($childs as $child) {
                    $child->load($child->getId());
                    $childItem['id'] = $child->getId();
                    $childItem['type_id'] = $child->getTypeId();
                    $childItem['sku'] = $child->getSku();
                    $childItem['name'] = $child->getName();
                    $childItem['price'] = $child->getFinalPrice();
                    $childItem['default_qty'] = $child->getQty();
                    $childProductModel = \Magento\Framework\App\ObjectManager::getInstance()->create(
                        '\Magento\Catalog\Model\Product'
                    )->load($child->getId());
                    $imageString = $childProductModel->getImage();
                    if ($imageString && $imageString != 'no_selection') {
                        $imgSrc = $this->getMediaConfig()->getMediaUrl($imageString);
                    } else {
                        $block = \Magento\Framework\App\ObjectManager::getInstance()->get(
                            '\Magestore\Webpos\Block\Webpos'
                        );
                        $imgSrc = $block->getViewFileUrl('Magestore_Webpos::images/category/image.jpg');
                    }
                    $childItem['image'] = $imgSrc;
                    $childItem['tax_class_id'] = $child->getData(self::TAX_CLASS_ID);
                    /** @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry */
                    $stockRegistry = \Magento\Framework\App\ObjectManager::getInstance()->create(
                        '\Magento\CatalogInventory\Api\StockRegistryInterface'
                    );

                    try {
                        $stockData = $stockRegistry->getStockItem($child->getId())->getData();
                        $childItem['stock'] = [$stockData];
                    } catch (\Exception $w) {
                        $childItem['stock'] = [];
                    }
                    $childItem['tier_price'] = $this->getPriceModel()->getTierPrices(
                        \Magento\Framework\App\ObjectManager::getInstance()->create(
                            '\Magento\Catalog\Model\Product'
                        )->load($child->getId())
                    );

                    $stockItem = $stockRegistry->getStockItem($child->getId());
                    if ($stockItem) {
                        $qtyIncrement = 1;
                        $minimumQty = $child->getData(self::MIN_SALE_QTY);
                        $maximumQty = $child->getData(self::MAX_SALE_QTY);
                        $isInStock = $child->getData(self::IS_IN_STOCK);
                        $stockData = $stockItem->getData();
                        if (!$isInStock) {
                            if (array_key_exists(self::IS_IN_STOCK, $stockData)) {
                                $isInStock = $stockData[self::IS_IN_STOCK];
                            }
                        }
                        if (!$minimumQty) {
                            if (array_key_exists(self::MIN_SALE_QTY, $stockData)) {
                                $minimumQty = $stockData[self::MIN_SALE_QTY];
                            }
                        }
                        if (!$maximumQty) {
                            if (array_key_exists(self::MAX_SALE_QTY, $stockData)) {
                                $maximumQty = $stockData[self::MAX_SALE_QTY];
                            }
                        }
                        if (is_array($stockData) && array_key_exists('enable_qty_increments', $stockData) && $stockData['enable_qty_increments'] == 1) {
                            if (array_key_exists('qty_increments', $stockData) && $stockData['qty_increments'] > 0) {
                                $qtyIncrement = $stockData['qty_increments'];
                            }
                        }
                        $childItem[self::IS_IN_STOCK] = $isInStock;
                        $childItem['minimum_qty'] = $minimumQty;
                        $childItem['is_salable'] = $child->getIsSalable();
                        $childItem['maximum_qty'] = $maximumQty;
                        $childItem['qty_increment'] = $qtyIncrement;
                    }
                    $childProducts[] = $childItem;
                }
            }
            return $childProducts;
        }
    }

    /**
     *
     * @param string $price
     * @return string
     */
    public function formatPrice($price)
    {
        $pricingHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Framework\Pricing\Helper\Data'
        );
        return $pricingHelper->currency($price, true, false);
    }

    /**
     * Retrieve assigned category Ids
     *
     * @return array
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $wasLocked = false;
            if ($this->isLockedAttribute('category_ids')) {
                $wasLocked = true;
                $this->unlockAttribute('category_ids');
            }
            //$ids = $this->_getResource()->getCategoryIds($this);
            $ids = $this->getShowedCategoryIds();
            $this->setData('category_ids', $ids);
            if ($wasLocked) {
                $this->lockAttribute('category_ids');
            }
        }

        if (is_array($this->_getData('category_ids')) && count($this->_getData('category_ids'))) {
            $catStrings = '';
            foreach ($this->_getData('category_ids') as $catId) {
                $catStrings .= '\'' . $catId . '\'';
            }
            return $catStrings;
        }

        if (!is_array($this->_getData('category_ids')) && count($this->_getData('category_ids'))) {
            return $this->_getData('category_ids');
        }
    }

    /**
     * Retrieve product tax class id
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->getData(self::TAX_CLASS_ID);
    }

    /**
     * @return array
     */
    public function getAttributesToSearch()
    {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magestore\Webpos\Helper\Data'
        );
        $attributeSearch = $helper->getStoreConfig('webpos/product_search/product_attribute');
        return explode(',', $attributeSearch);
    }

    /**
     * @return string
     */
    public function getBarcodeAttribute()
    {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magestore\Webpos\Helper\Data'
        );
        return $helper->getStoreConfig('webpos/product_search/barcode');
    }

    /**
     * get search string to search product
     *
     * @return string
     */
    public function getSearchString()
    {
        $searchString = '';
        $attributesToSearch = $this->getAttributesToSearch();
        if (!empty($attributesToSearch)) {
            foreach ($attributesToSearch as $attribute) {
                if ($this->getData($attribute) && !is_array($this->getData($attribute)))
                    $searchString .= ' ' . $this->getData($attribute);
            }
        }
        if ($this->getBarcodeAttribute()) {
            $searchString .= ' ' . $this->getBarCodeByProduct();
        }
        return $searchString;
    }

    /**
     * Get barcode options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Product\BarcodeOptionsInterface[]|null
     */
    public function getBarcodeOptions()
    {
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            /** @var \Magestore\Webpos\Model\Catalog\Product\Type\Configurable $configurable */
            $configurable = \Magento\Framework\App\ObjectManager::getInstance()->create(
                'Magestore\Webpos\Model\Catalog\Product\Type\Configurable'
            );
            $configurable->setProduct($this);
            $currentProduct = $this;

            $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\ConfigurableProduct\Helper\Data'
            );
            $options = $helper->getOptions($currentProduct, $configurable->getAllowProducts());
            $attributesData = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\ConfigurableProduct\Model\ConfigurableAttributeData'
            )->getAttributesData($currentProduct, $options);

            $config = [
                'index' => isset($options['index']) ? $options['index'] : [],
            ];

            if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
                $config['defaultValues'] = $attributesData['defaultValues'];
            }
            $config = array_merge($config, $configurable->getAdditionalConfig());
            $productIds = $config['index'];
            $configOptions = $this->getConfigOptions();
            $collection = $this->getChildrenCollection();
            $collection->addFinalPrice();
            //$barcodeAttribute = $this->getBarcodeAttribute();
            $barcodeOptions = [];
            foreach ($collection as $product) {
                $barcode = $this->getBarCodeByProduct($product);
                if (!is_null($barcode) && $barcode != '') {
                    if (!empty($productIds[$product->getId()])) {
                        $label = '';
                        $i = 0;
                        $data = [];
                        foreach ($productIds[$product->getId()] as $id => $value) {
                            foreach ($configOptions as $configOption) {
                                if (isset($configOption[$value])) {
                                    if ($i > 0)
                                        $label .= ', ';
                                    $label .= $configOption[$value];
                                    $i++;
                                    break;
                                }
                            }
                            $data['options'][] = ['id' => $id, 'value' => $value];
                        }
                        $productPrice = $product->getFinalPrice();
                        $productId = $product->getId();
                        $data['product'] = ['product_id' => $productId, 'price' => $productPrice];
                        $data['label'] = $label;
                        $barcodeOptions[][trim($barcode)] = $data;
                    }
                }
            }
            return $barcodeOptions;
        }
    }

    /**
     * get children collection of configurable product
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     */
    public function getChildrenCollection()
    {
        if (!$this->_chidrenCollection) {
            $configurable = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\ConfigurableProduct\Model\Product\Type\Configurable'
            );
            $collection = $configurable->getUsedProductCollection($this);
            $collection->addAttributeToSelect($this->getBarcodeAttribute());
            $this->_chidrenCollection = $collection;
        }
        return $this->_chidrenCollection;
    }

    /**
     * get barcode string
     *
     * @return string
     */
    public function getBarcodeString()
    {
        $barcodeString = '';
        if ($this->getBarcodeAttribute() && $this->getBarCodeByProduct()) {
            $barcodeString .= ',' . $this->getBarCodeByProduct() . ',';
        }

        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $collection = $this->getChildrenCollection();
            foreach ($collection as $product) {
                $barcode = $this->getBarCodeByProduct($product);
                if ($barcode) {
                    $barcodeString .= ',' . $barcode . ',';
                }
            }
        }
        return $barcodeString;
    }

    public function getBarCodeByProduct($product = null)
    {
        $barcodeAttribute = $this->getBarcodeAttribute();
        if (!$product) {
            $product = $this;
        }
        $barcode = $product->getData($barcodeAttribute);
        $barcodeObject = new \Magento\Framework\DataObject();
        $barcodeObject->setBarcode($barcode);
        \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Framework\Event\ManagerInterface')->dispatch('webpos_product_get_barcode_after', ['object_barcode' => $barcodeObject, 'product' => $product]);
        $barcode = $barcodeObject->getBarcode();
        return $barcode;
    }

    /**
     * get All category ids include anchor cateogries
     * @param type $ids
     * @return type
     */
    public function getShowedCategoryIds()
    {
        $categoryCollection = $this->getCategoryCollection();
        $categoryIds = $categoryCollection->getAllIds();
        $anchorIds = [];
        foreach ($categoryCollection as $category) {
            $pathIds = $category->getPathIds();
            array_pop($pathIds);
            $anchorIds = array_unique(array_merge($anchorIds, $pathIds));
        }
        $anchorCollection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Catalog\Model\ResourceModel\Category\Collection')
            ->addFieldToFilter('is_anchor', 1)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $anchorIds));
        return array_unique(array_merge($categoryIds, $anchorCollection->getAllIds()));
    }

    public function getIsVirtual()
    {
        $virtualTypes = [
            'customercredit', 'virtual'
        ];
        if (in_array($this->getTypeId(), $virtualTypes)) {
            return true;
        }
        return false;
    }

    /**
     * Product credit value
     *
     * @return string
     */
    public function getCustomercreditValue()
    {
        if ($this->getTypeId() === 'customercredit') {

            if ($this->getStorecreditType() == 1)
                return $this->getStorecreditValue();
            elseif ($this->getStorecreditType() == 3) {
                return (string)$this->getStorecreditDropdown();
            }
        }
        return;
    }

    /**
     * Product credit type
     *
     * @return int
     */
    public function getStorecreditType()
    {
        if ($this->getTypeId() === 'customercredit') {
            return $this->getData('storecredit_type');
        }
        return;
    }

    /**
     * Product credit rate
     *
     * @return float|null
     */
    public function getStorecreditRate()
    {
        if ($this->getTypeId() === 'customercredit') {
            return $this->getData('storecredit_rate');
        }
        return;
    }

    /**
     * Product credit min value
     *
     * @return float|null
     */
    public function getStorecreditMin()
    {
        if ($this->getTypeId() === 'customercredit') {
            return $this->getData('storecredit_from');
        }
        return;
    }

    /**
     * Product credit max value
     *
     * @return float|null
     */
    public function getStorecreditMax()
    {
        if ($this->getTypeId() === 'customercredit') {
            return $this->getData('storecredit_to');
        }
        return;
    }

    /**
     *
     * @return float
     */
    public function getGiftvoucherValue(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_value');
        }
        return;
    }

    /**
     *
     * @return int
     */
    public function getGiftvoucherSelectPriceType(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_type');
        }
        return;
    }

    /**
     *
     * @return float
     */
    public function getGiftvoucherPrice(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_price');
        }
        return;
    }


    /**
     *
     * @return float
     */
    public function getGiftvoucherFrom(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_from');
        }
        return;
    }

    /**
     *
     * @return float
     */
    public function getGiftvoucherTo(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_to');
        }
        return;
    }

    /**
     *
     * @return string
     */
    public function getGiftvoucherDropdown(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_dropdown');
        }
        return;
    }

    /**
     *
     * @return int
     */
    public function getGiftvoucherPriceType(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_price_type');
        }
        return;
    }

    /**
     *
     * @return string
     */
    public function getGiftvoucherTemplate(){
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_template_ids');
        }
        return;
    }

    /**
     *
     * @return int
     */
    public function getGiftvoucherType() {
        if ($this->getTypeId()=== 'giftvoucher') {
            return $this->getData('gift_card_type');
        }
        return;
    }

    /**
     *
     * @return int
     */
    public function getAllowOpenAmount() {
        if ($this->getTypeId()=== 'giftcard') {
            return $this->getData('allow_open_amount');
        }
        return null;
    }
    /**
     *
     * @return int
     */
    public function getGiftcardAmounts() {
        if ($this->getTypeId()=== 'giftcard') {
            $amounts = [];
            foreach ((array)$this->getData('giftcard_amounts') as $amount) {
                $amounts[] = $amount['website_value'];
            }
            sort($amounts);
            return $amounts;
        }
        return null;
    }
    /**
     *
     * @return int
     */
    public function getOpenAmountMin() {
        if ($this->getTypeId()=== 'giftcard') {
            return $this->getData('open_amount_min');
        }
        return null;
    }
    /**
     *
     * @return int
     */
    public function getOpenAmountMax() {
        if ($this->getTypeId()=== 'giftcard') {
            return $this->getData('open_amount_max');
        }
        return null;
    }

    /**
     *
     * @return int
     */
    public function getGiftcardType() {
        if ($this->getTypeId()=== 'giftcard') {
            return $this->getData('giftcard_type');
        }
        return null;
    }


    /**
     * Get product qty increment
     *
     * @return float|null
     */
    public function getQtyIncrement()
    {
        $qtyIncrement = 0;
//        if($this->getData('enable_qty_increments') && $this->getData('qty_increments')){
//            $qtyIncrement = $this->getData(self::QTY_INCREMENT);
//        }
//        return $qtyIncrement;
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        if (is_array($stockData) && array_key_exists('enable_qty_increments', $stockData) && $stockData['enable_qty_increments'] == 1) {
            if (array_key_exists('qty_increments', $stockData) && $stockData['qty_increments'] > 0) {
                $qtyIncrement = $stockData['qty_increments'];
            }
        }
        return $qtyIncrement;
    }

    /**
     * Get is in stock
     *
     * @return int
     */
    public function getIsInStock()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $isInStock = $this->getData(self::IS_IN_STOCK);
        if (!$isInStock) {
            if (array_key_exists(self::IS_IN_STOCK, $stockData)) {
                $isInStock = $stockData[self::IS_IN_STOCK];
            }
        }
        return $isInStock;
    }

    /**
     * Get minimum qty
     *
     * @return float|null
     */
    public function getMinimumQty()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $qty = $this->getData(self::MIN_SALE_QTY);
        if (!$qty) {
            if (array_key_exists(self::MIN_SALE_QTY, $stockData)) {
                $qty = $stockData[self::MIN_SALE_QTY];
            }
        }
        return $qty;
    }

    /**
     * Get maximum float|null
     *
     * @return float|null
     */
    public function getMaximumQty()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $qty = $this->getData(self::MAX_SALE_QTY);
        if (!$qty) {
            if (array_key_exists(self::MAX_SALE_QTY, $stockData)) {
                $qty = $stockData[self::MAX_SALE_QTY];
            }
        }
        return $qty;
    }

    /**
     * Get qty
     *
     * @return string
     */
    public function getQty()
    {
        $qty = $this->getData(self::QTY);
        if (!$qty) {
            $product = $this->getProduct();
            $stockItem = $product->getStockItem();
            $extendedAttributes = $this->getExtensionAttributes();
            if ($extendedAttributes !== null) {
                $stockItem = $extendedAttributes->getStockItem();
            }
            $stockData = $stockItem->getData();
            if (array_key_exists(self::QTY, $stockData)) {
                $qty = $stockData[self::QTY];
            }
        }
        return $qty;
    }

    /**
     * Get stock data by product
     *
     * @return \Magestore\Webpos\Api\Data\Inventory\StockItemInterface[]
     */
    public function getStocks()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stockItemRepository = $objectManager->create('\Magestore\Webpos\Api\Inventory\StockItemRepositoryInterface');
        $searchCriteriaBuilder = $objectManager->create('\Magento\Framework\Api\SearchCriteriaBuilder');
        try {
            $stocks = [];
            $productId = ($this->getProduct()) ? $this->getProduct()->getId() : false;
            if ($productId) {
                $searchCriteriaBuilder->addFilter('e.entity_id', $productId);
                $searchCriteria = $searchCriteriaBuilder->create();
                $stockItems = $stockItemRepository->getStockItems($searchCriteria);
                $stocks = $stockItems->getItems();
            }
            return $stocks;
        } catch (\Exception $w) {
            return [];
        }
    }

    /**
     * Get is in stock
     *
     * @return int
     */
    public function getBackorders()
    {
        return $this->getData('backorders');
    }

    public function getFinalPrice($qty = null)
    {
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $rate = 1;
            if ($this->_storeManager->getStore()->getCurrentCurrencyRate()) {
                $rate = $this->_storeManager->getStore()->getCurrentCurrencyRate();
            }
            $priceConverted = $this->getPriceModel()->getFinalPrice($qty, $this) / $rate;
            return $priceConverted;
        }
        return parent::getFinalPrice($qty);
    }

    /**
     * @return string
     */
    public function getQtyIncrements()
    {
        return $this->getData(self::QTY_INCREMENTS);
    }

    /**
     * @return bool
     */
    public function getEnableQtyIncrements()
    {
        return $this->getData(self::ENABLE_QTY_INCREMENTS);
    }

    /**
     * @return bool
     */
    public function getIsQtyDecimal()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $isQtyDecimal = $this->getData(self::IS_QTY_DECIMAL);
        if (!$isQtyDecimal) {
            if (array_key_exists(self::IS_QTY_DECIMAL, $stockData)) {
                $isQtyDecimal = $stockData[self::IS_QTY_DECIMAL];
            }
        }
        return $isQtyDecimal;
    }

    /**
     * Get is sale able
     *
     * @return int
     */
    public function getIsSalable()
    {
        $request = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\Request\Http');
        $showOutOfStock = $request->getParam('show_out_stock');
        if (!$showOutOfStock) {
            return $this->getIsInStock();
        } else {
            return parent::getIsSalable();
        }
    }

    /**
     * Get data of children product
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductInterface[]|null
     */
    public function getChildrenProducts()
    {
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $this->getChildrenCollection()->getItems();
        }
        return null;
    }

    /**
     * Get qty in online mode
     *
     * @return float|null
     */
    public function getQtyOnline()
    {
        return $this->getQty();
    }
}
