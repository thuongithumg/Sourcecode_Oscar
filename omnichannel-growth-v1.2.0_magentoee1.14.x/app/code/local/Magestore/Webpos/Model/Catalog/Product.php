<?php

class Magestore_Webpos_Model_Catalog_Product extends Mage_Catalog_Model_Product
{

    protected $_product;

    protected $_chidrenCollection;

    const SHORT_DESCRIPTION = 'short_description';

    const DESCRIPTION = 'description';

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getProduct()
    {
        return $this;
    }


    /**
     * @param null $product
     * @return false|Mage_Core_Model_Abstract
     */
    protected function _getProductTypeInstance($product = null)
    {
        if (is_null($product))
            $product = $this;
        $type = '';
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE)
            $type = 'catalog/product_type_configurable';
        return Mage::getModel($type);
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
        return;
    }

    /**
     * Retrieve images
     *
     * @return array/null
     */
    public function getImages()
    {
        $product = $this;//->getProduct();
        $images = array();
        $imageArray = $product->getMediaGallery('images');
        if (!empty($imageArray)) {
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
     * @return mixed
     */
    public function getCustomOptions()
    {
        return $this->getProduct()->getOptions();
    }

    /**
     * Get list of product config options
     *
     */
    public function getConfigOptions()
    {
        if ($this->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $product = $this;
            $productTypeInstance = $this->_getProductTypeInstance($product);
            $productAttributeOptions = $productTypeInstance->getConfigurableAttributesAsArray($product);
//            $allowOptions = array();
//            foreach ($this->getAllowProducts() as $itemProduct) {
//                $productId = $itemProduct->getId();
//                $productStock[$productId] = $itemProduct->getStockItem()->getIsInStock();
//                foreach ($this->getAllowAttributes() as $attribute) {
//                    $productAttribute = $attribute->getProductAttribute();
//                    $productAttributeId = $productAttribute->getId();
//                    $attributeValue = $itemProduct->getData($productAttribute->getAttributeCode());
//                    if (!isset($allowOptions[$productAttributeId])) {
//                        $allowOptions[$productAttributeId] = array();
//                    }
//
//                    if (!isset($allowOptions[$productAttributeId][$attributeValue])) {
//                        $allowOptions[$productAttributeId][$attributeValue] = array();
//                    }
//                    $allowOptions[$productAttributeId][$attributeValue][] = $productId;
//                }
//            }
            $options = array();
            foreach ($productAttributeOptions as $productAttributeOption) {
                $values = $productAttributeOption['values'];
                $optionId = $productAttributeOption['attribute_id'];
//                $checked = false;
//                foreach ($allowOptions as $optionsKey => $optionsValue) {
//                    if ($optionId == $optionsKey)
//                        $checked = true;
//                }
//                if (!$checked) continue;
                $code = $productAttributeOption['attribute_code'];
                $optionLabel = $productAttributeOption['label'];
                $options[$code]['optionId'] = $optionId;
                $options[$code]['optionLabel'] = $optionLabel;
                foreach ($values as $value) {
                    $optionValueId = $value['value_index'];
//                    $checked = false;
//                    foreach ($allowOptions[$optionId] as $key => $productIds) {
//                        if ($optionValueId == $key)
//                            $checked = true;
//                    }
//                    if (!$checked) continue;
                    $val = $value['label'];
                    $options[$code][$optionValueId] = $val;
                }
            }
            if (is_array($options)) {
                return array_values($options);
            } else {
                return null;
            }
        }
    }

    /**
     * @param $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getProductOptionsForPos($currentProduct, $allowedProducts = array())
    {
        $options = array();
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $images = $this->getGalleryImages($product);
            if ($images) {
                foreach ($images as $image) {
                    $options['images'][$productId][] =
                        array(
                            'thumb' => $image->getData('small_image_url'),
                            'img' => $image->getData('medium_image_url'),
                            'full' => $image->getData('large_image_url'),
                            'caption' => $image->getLabel(),
                            'position' => $image->getPosition(),
                            'isMain' => $image->getFile() == $product->getImage(),
                        );
                }
            }
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());

                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }
        return $options;
    }

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes()
    {
        return $this->getTypeInstance(true)->getConfigurableAttributes($this->getProduct());
    }

    /**
     * @param $product
     * @param array $options
     * @return array
     */
    public function getAttributesData($product, array $options = array())
    {
        $defaultValues = array();
        $attributes = array();
        foreach ($product->getTypeInstance()->getConfigurableAttributes($product) as $attribute) {
            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options);
            if ($attributeOptionsData) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeId = $productAttribute->getId();
                $attributes[$attributeId] = array(
                    'id' => $attributeId,
                    'code' => $productAttribute->getAttributeCode(),
                    'label' => $productAttribute->getStoreLabel($product->getStoreId()),
                    'options' => $attributeOptionsData,
                    'position' => $attribute->getPosition()
                );
                $defaultValues[$attributeId] = $this->getAttributeConfigValue($attributeId, $product);
            }
        }

        return array(
            'attributes' => $attributes,
            'defaultValues' => $defaultValues,
        );
    }

    /**
     * @param Attribute $attribute
     * @param array $config
     * @return array
     */
    protected function getAttributeOptionsData($attribute, $config)
    {
        $attributeOptionsData = array();

        foreach ($attribute->getProductAttribute()->getSource()->getAllOptions() as $attributeOption) {

            $optionId = $attributeOption['value'];

            $attributeOptionsData[] = array(
                'id' => $optionId,
                'label' => $attributeOption['label'],
                'products' => isset($config[$attribute->getAttributeId()][$optionId])
                    ? $config[$attribute->getAttributeId()][$optionId]
                    : array()
            );
        }

        return $attributeOptionsData;
    }

    /**
     * @param int $attributeId
     * @param Product $product
     * @return mixed|null
     */
    protected function getAttributeConfigValue($attributeId, $product)
    {
        return $product->hasPreconfiguredValues()
            ? $product->getPreconfiguredValues()->getData('super_attribute/' . $attributeId)
            : null;
    }


    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        /** if product is configurable */
        if ($this->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            /** @var \Magestore\Webpos\Model\Catalog\Product\Type\Configurable $configurable */
            $configurable = Mage::getModel('webpos/catalog_product_type_configurable');
            $configurable->setProduct($this->getProduct());

            $store = Mage::app()->getStore();
            $currentProduct = $this;

            $regularPrice = $this->getData('price');
            $finalPrice = $this->getFinalPrice();

            $product = $this->getProduct();

//            $allowProducts = $product->getTypeInstance(true)
//                ->getUsedProducts(null, $product);
            $allowProducts = $this->getAllowProducts();
            $options = $this->getProductOptionsForPos($currentProduct, $allowProducts);


            $attributesData = $this->getAttributesData($currentProduct, $options);
            $config = array(
                'attributes' => $attributesData['attributes'],
                'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
                'optionPrices' => $configurable->getOptionPrices(),
                'prices' => array(
                    'oldPrice' => array(
                        'amount' => $configurable->registerJsPrice($regularPrice),
                    ),
                    'basePrice' => array(
                        'amount' => $this->getPrice(),
                    ),
                    'finalPrice' => array(
                        'amount' => $configurable->registerJsPrice($finalPrice),
                    ),
                ),
                'productId' => $currentProduct->getId(),
                'chooseText' => Mage::helper('webpos')->__('Choose an Option...'),
                'images' => isset($options['images']) ? $options['images'] : array(),
                'index' => isset($options['index']) ? $options['index'] : array()
            );

            if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
                $config['defaultValues'] = $attributesData['defaultValues'];
            }
            $config = array_merge($config, $configurable->getAdditionalConfig());
            $str = json_encode($config);
            return $str;
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
        if ($this->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE)
            return;
        $product = $this;
        $priceCurrency = Mage::getModel('directory/currency');

        if (!$this->hasOptions()) {
            $config = array(
                'productId' => $product->getId(),
                'priceFormat' => Mage::helper('tax')->getPriceFormat()
            );
            return Zend_Json::encode($config);
        }

        $tierPrices = array();

        $config = array(
            'productId' => $product->getId(),
            'priceFormat' => Mage::helper('tax')->getPriceFormat(),
            'prices' => array(
                'oldPrice' => array(
                    'amount' => $priceCurrency->convert(
                        $product->getPrice()
                    ),
                    'adjustments' => array()
                ),
                'basePrice' => array(
                    'amount' => $priceCurrency->convert(
                        $product->getFinalPrice()
                    ),
                    'adjustments' => array()
                ),
                'finalPrice' => array(
                    'amount' => $priceCurrency->convert(
                        $product->getFinalPrice()
                    ),
                    'adjustments' => array()
                )
            ),
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        );

        return Zend_Json::encode($config);
    }

    /**
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getTypeInstance()->hasOptions($this)) {
            return true;
        }

        if ($this->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
            return true;
        }

        return false;
    }


    /**
     * @return array
     */
    public function getBundleOptions()
    {
        if ($this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $bundleChilds = array();
            $product = $this;
            $store_id = Mage::app()->getStore()->getId();
            $options = Mage::getResourceModel('bundle/option_collection')
                ->setProductIdFilter($product->getId())
                ->setPositionOrder();
            $options->joinValues($store_id);
            $typeInstance = Mage::getModel('bundle/product_type');
            $selections = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
            $price_type = $product->getData('price_type');
            foreach ($options->getItems() as $option) {
                $bundleChilds[$option->getId()]['title'] = $option->getTitle();
                $bundleChilds[$option->getId()]['required'] = $option->getRequired();
                $bundleChilds[$option->getId()]['type'] = $option->getType();
                $bundleChilds[$option->getId()]['id'] = $option->getId();
                $bundleChilds[$option->getId()]['product_id'] = $product->getId();
                foreach ($selections as $selection) {
                    $selection_price_type = $selection->getData('selection_price_type');
                    $selection_price_value = $selection->getData('selection_price_value');
                    $price = $selection->getData('price');
                    $selection_price = ($selection_price_type == 0) ? $selection_price_value : $price * $selection_price_value;

                    if ($price_type == 0) {
                        $selection_price = $price;
                    }
                    if ($option->getId() == $selection->getOptionId()) {
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()] = array();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()] = $selection->getData();
                        $bundleChilds[$option->getId()]['items'][$selection->getSelectionId()]['price'] = $selection_price;
                    }
                }
            };
            return $bundleChilds;
        }
    }


    /**
     * @return array
     */
    public function getGroupedOptions()
    {
        $storeId = Mage::app()->getStore()->getId();
        if ($this->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
            $childProducts = array();
            $product = $this;
            $typeInstance = Mage::getModel('catalog/product_type_grouped');
            $childs = $typeInstance->getAssociatedProducts($product);
            if (!empty($childs)) {
                foreach ($childs as $child) {
                    $stockItem = $child->getStockItem();
                    $childProducts[$child->getId()]['id'] = $child->getId();
                    $childProducts[$child->getId()]['type_id'] = $child->getTypeId();
                    $childProducts[$child->getId()]['sku'] = $child->getSku();
                    $childProducts[$child->getId()]['name'] = $child->getName();
                    $childProducts[$child->getId()]['price'] = $child->getFinalPrice();
                    $childProducts[$child->getId()]['default_qty'] = $child->getQty();
                    $childProducts[$child->getId()]['minimum_qty'] = $stockItem->getMinSaleQty();
                    $childProducts[$child->getId()]['maximum_qty'] = $stockItem->getMaxSaleQty();
                    $childProductModel = Mage::getModel('catalog/product')->load($child->getId());
                    $imageString = $childProductModel->getImage();
                    if ($imageString && $imageString != 'no_selection') {
                        $imgSrc = $this->getMediaConfig()->getMediaUrl($imageString);
                    } else {
                        $url = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
                        $imgSrc = $url . 'webpos/catalog/category/image.jpg';
                    }
                    $childProducts[$child->getId()]['image'] = $imgSrc;
                    $childProducts[$child->getId()]['tax_class_id'] = $child->getData('tax_class_id');

                    try {
                        $stockData = array(); //Todo
                        $childProducts[$child->getId()]['stock'] = array($stockData);
                    } catch (Exception $w) {
                        $childProducts[$child->getId()]['stock'] = array();
                    }

                    $childProducts[$child->getId()]['tier_price'] = $this->getPriceModel()->getTierPrice(null, Mage::getModel('catalog/product')->load($child->getId()));
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
        return Mage::helper('core')->currency($price, true, false);
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
     * @return array
     */
    public function getBarcodeOptions()
    {
        if ($this->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {

            $product = $this->getProduct();
            $configurable = Mage::getModel('webpos/catalog_product_type_configurable');
            $configurable->setProduct($product);
            $currentProduct = $this;

            $allowProducts = $product->getTypeInstance(true)
                ->getUsedProducts(null, $product);
            $options = $this->getProductOptionsForPos($currentProduct, $allowProducts);


            $attributesData = $this->getAttributesData($currentProduct, $options);

            $config = array(
                'index' => isset($options['index']) ? $options['index'] : array(),
            );

            if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
                $config['defaultValues'] = $attributesData['defaultValues'];
            }


            $config = array_merge($config, $configurable->getAdditionalConfig());
            $productIds = $config['index'];
            $configOptions = $this->getConfigOptions();
            $collection = $this->getChildrenCollection();
            $collection->addFinalPrice();
            //$barcodeAttribute = $this->getBarcodeAttribute();
            $barcodeOptions = array();
            foreach ($collection as $product) {
                $barcode = $this->getBarCodeByProduct($product);
                if (!is_null($barcode) && $barcode != '') {
                    if (!empty($productIds[$product->getId()])) {
                        $label = '';
                        $i = 0;
                        $data = array();
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
                            $data['options'][] = array('id' => $id, 'value' => $value);
                        }
                        $productPrice = $product->getFinalPrice();
                        $productId = $product->getId();
                        $data['product'] = array('product_id' => $productId, 'price' => $productPrice);
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
     * @return type
     */
    public function getChildrenCollection()
    {
        if (!$this->_chidrenCollection) {
            $configurable = Mage::getModel('catalog/product_type_configurable');
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

        if ($this->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
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
     * @return array
     */
    public function getAttributesToSearch()
    {
        $attributeSearch = Mage::helper('webpos')->getStoreConfig('webpos/product_search/product_attribute');
        return explode(',', $attributeSearch);
    }


    /**
     * @param null $product
     * @return mixed
     */
    public function getBarCodeByProduct($product = null)
    {
        $barcodeAttribute = $this->getBarcodeAttribute();
        if (!$product) {
            $product = $this;
        }
        $barcode = $product->getData($barcodeAttribute);
        $barcodeObject = new Varien_Object();
        $barcodeObject->setBarcode($barcode);
        Mage::dispatchEvent('webpos_product_get_barcode_after', array('object_barcode' => $barcodeObject, 'product' => $product));
        $barcode = $barcodeObject->getBarcode();
        return $barcode;
    }

    /**
     * @return string
     */
    public function getBarcodeAttribute()
    {
        return Mage::helper('webpos')->getStoreConfig('webpos/product_search/barcode');
    }

    /**
     * @return bool
     */
    public function getIsVirtual()
    {
        $virtualTypes = array(
            'customercredit'
        );
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
        return null;
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
        return null;
    }

    /**
     * Product credit rate
     *
     * @return float|null
     */
    public function getStorecreditRate()
    {
        if ($this->getTypeId() === 'customercredit') {
            return $this->getData('credit_rate');
        }
        return null;
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
        return null;
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
        return null;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        $products = array();
        $product = $this;
//        $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
        $allProducts = $this->getTypeInstance(true)
            ->getUsedProducts(null, $product);
        foreach ($allProducts as $childProduct) {
            if ($childProduct->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED
//                || $skipSaleableCheck
//                || (!$childProduct->getStockItem()->getIsInStock()
//                    && Mage::helper('cataloginventory')->isShowOutOfStock())
                && ($childProduct->getStockItem()->getQty() > 0 ||
                    ($childProduct->getIsSalable() && $childProduct->getStockItem()->getBackorders())
                )
            ) {
                $products[] = $childProduct;
            }
        }
        return $products;
    }


    /**
     * Is product salable detecting by product type
     *
     * @return bool
     */
    public function getIsSalable()
    {
        if (Mage::getStoreConfig('webpos/general/ignore_checkout')) {
            return 1;
        }
        return parent::getIsSalable();
    }
}
