<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Giftcard;

use \Magento\Framework\ObjectManagerInterface as ObjectManagerInterface;


use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;


/**
 * Class StockItemRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TemplateRepository implements \Magestore\Webpos\Api\Integration\Giftcard\TemplateRepositoryInterface {

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     *
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $_permissionHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_permissionHelper = $permissionHelper;
        $this->_request = $request;
        $this->_moduleManager = $moduleManager;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get template list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\TemplateResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        if (!$this->_moduleManager->isEnabled('Magestore_Giftvoucher')) {
            throw new NoSuchEntityException(__('Gift card template is not available'));
        }
        $items = [];
        $giftTemplateCollection = $this->_objectManager->get('Magestore\Giftvoucher\Model\GiftTemplate')
            ->getCollection();
        $giftTemplateCollection->load();
        $giftImageUrl = $this->_storeManager
                ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'giftvoucher/template/images';
        if($giftTemplateCollection->getSize() > 0){
            $giftTemplateItems = $giftTemplateCollection->getItems();
            foreach ($giftTemplateItems as $giftTemplate){
                $data = $giftTemplate->getData();
                $data['template_id'] = $giftTemplate->getData('giftcard_template_id');
                $imageUrls = array();
                if($giftTemplate->getData('images')) {
                    $images = explode(',', $giftTemplate->getData('images'));
                    if(count($images)) {
                        foreach ($images as $image) {
                            $imageUrl['name'] =  $image;
                            $imageUrl['url'] =  $giftImageUrl . '/' . $image;
                            $imageUrls[] = $imageUrl;
                        }
                    }
                }
                $data['images_url'] = $imageUrls;
                $items[] = $data;
            }
        }
        $searchResult = $this->_objectManager->get('Magento\Framework\Api\Search\SearchResultFactory')->create();
        $searchResult->setItems($items);
        $searchResult->setTotalCount(count($items));
        return $searchResult;
    }

}