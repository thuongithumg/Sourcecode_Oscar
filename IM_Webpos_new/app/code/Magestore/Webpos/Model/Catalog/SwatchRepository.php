<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Eav\Model\Entity\Attribute\Option;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class SwatchRepository implements \Magestore\Webpos\Api\Catalog\SwatchRepositoryInterface
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $swatchHelper;

    protected $attributeCollection;

    protected $swatchResultInterface;
    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollection,
        \Magestore\Webpos\Api\Data\Catalog\SwatchResultInterfaceFactory $swatchResultInterface
    ){
        $this->_resultPageFactory = $resultPageFactory;
        $this->swatchHelper = $swatchHelper;
        $this->attributeCollection = $attributeCollection;
        $this->swatchResultInterface = $swatchResultInterface;
    }
    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $swatchAttributeArray= array();
        $swatchArray = array();
        $collection = $this->attributeCollection->create();
        foreach ($collection as $attributeModel) {
            $isSwatch = $this->swatchHelper->isSwatchAttribute($attributeModel);
            if ($isSwatch) {
                $swatchAttributeArray[] = $attributeModel->getId();
                $attributeOptions = [];
                foreach ($attributeModel->getOptions() as $option) {
                    $attributeOptions[$option->getValue()] = $this->getUnusedOption($option);
                }
                $attributeOptionIds = array_keys($attributeOptions);
                $swatches = $this->swatchHelper->getSwatchesByOptionsId($attributeOptionIds);
                $data = [
                    'attribute_id' => $attributeModel->getId(),
                    'attribute_code' => $attributeModel->getAttributeCode(),
                    'attribute_label' => $attributeModel->getStoreLabel(),
                    'swatches' => $swatches,
                ];
                $swatchArray[] = $data;
            }
        }
        $swatchInterface = $this->swatchResultInterface->create();
        $swatchInterface->setItems($swatchArray);
        $swatchInterface->setTotalCount(count($swatchArray));
        return $swatchInterface;
    }

    protected function getUnusedOption(Option $swatchOption)
    {
        return [
            'label' => $swatchOption->getLabel(),
            'link' => 'javascript:void();',
            'custom_style' => 'disabled'
        ];
    }

}