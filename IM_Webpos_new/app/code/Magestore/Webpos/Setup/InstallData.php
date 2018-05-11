<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magestore\Webpos\Model\Staff\RoleFactory
     */
    protected $_roleFactory;
    /**
     * @var \Magestore\Webpos\Model\Staff\AuthorizationRuleFactory
     */
    protected $_authorizationRuleFactory;
    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $_userCollectionFactory;
    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $_staffFactory;
    /**
     * @var \Magestore\Webpos\Model\Location\LocationFactory
     */
    protected $_locationFactory;

    /**
     * @var
     */
    protected $_appState;
    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;
    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;
    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    protected $_websiteCollectionFactory;
    /**
     * {@inheritdoc}
     */
    protected $_product;
    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     *
     */
    const IS_ACTIVE = 1;
    /**
     *
     */
    const NOT_ENCODE_PASSWORD = 1;
    /**
     *
     */
    const DEFAULT_DISCOUNT_PERCENT = 100;
    /**
     *
     */
    const DEFAULT_RESOURCE_ACCESS = 'Magestore_Webpos::all';

    /**
     * InstallData constructor.
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory
     * @param \Magestore\Webpos\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory
     * @param \Magestore\Webpos\Model\Location\LocationFactory $locationFactory
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory,
        \Magestore\Webpos\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory,
        \Magestore\Webpos\Model\Location\LocationFactory $locationFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $collectionFactory,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\State $appState,
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ){
        $this->_roleFactory = $roleFactory;
        $this->_authorizationRuleFactory = $authorizationRuleFactory;
        $this->_userCollectionFactory = $collectionFactory;
        $this->_staffFactory = $staffFactory;
        $this->_locationFactory = $locationFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
        $this->_product = $product;
        $this->_appState = $appState;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProductModel() {
        return $this->_product;
    }
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        $roleData = array(
            'display_name' => 'admin',
            'description' => 'Admin',
            'maximum_discount_percent' => self::DEFAULT_DISCOUNT_PERCENT
        );

        $role = $this->_roleFactory->create()->setData($roleData)->save();
        $roleId = $role->getId();


        $authorizeRule = array(
            'role_id' => $roleId,
            'resource_id' => self::DEFAULT_RESOURCE_ACCESS
        );
        $this->_authorizationRuleFactory->create()->setData($authorizeRule)->save();

        $data = array(
            'display_name' => 'Store Address',
            'address' => 'Store Address',
            'description' => 'Store Address'
        );
        $curLocation = $this->_locationFactory->create()
            ->load('Store Address', 'display_name');
        if(!$curLocation->getId()) {
            $locationModel = $this->_locationFactory->create()->setData($data)->save();
        } else {
            $locationModel = $curLocation;
        }
        $locationId = $locationModel->getId();

        $userModel = $this->_userCollectionFactory->create()->addFieldToFilter('is_active',self::IS_ACTIVE)
            ->getFirstItem();
        if ($userModel->getId()) {
            $username = $userModel->getUsername();
            $email = $userModel->getEmail();
            $password = $userModel->getPassword();
            $name = $userModel->getFirstname(). ' '.$userModel->getLastname();
            $customerGroup = 'all';
            $data = array(
                'username' => $username,
                'password' => $password,
                'display_name' => $name,
                'email' => $email,
                'customer_group' => $customerGroup,
                'role_id' => $roleId,
                'location_id' => $locationId,
                'status' => self::IS_ACTIVE,
                'not_encode' => self::NOT_ENCODE_PASSWORD,
                'can_use_sales_report' => 1
            );
            $curUser = $this->_staffFactory->create()->load($username, 'username');
            if(!$curUser->getId()) {
                $this->_staffFactory->create()->setData($data)->save();
            }
        }

        $version = $this->productMetadata->getVersion();
        
        try{
            if(version_compare($version, '2.2.0', '>=')) {
                $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } else {
                $this->_appState->setAreaCode('admin');
            }
        } catch(\Exception $e) {
            $this->_appState->getAreaCode();
        }
        
        $product = $this->getProductModel();
        if ($productId = $product->getIdBySku('webpos-customsale')) {
            return $product->load($productId);
        }else{
            $product = $product->getCollection()->addAttributeToFilter('type_id','simple')->getFirstItem();
            $product->setId(null);
        }

        $websiteIds = $this->_websiteCollectionFactory->create()
            ->addFieldToFilter('website_id', array('neq' => 0))
            ->getAllIds();

        $attributeSet = $this->attributeSetFactory->create();
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $this->attributeSetFactory->create()
            ->getCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->addFieldToFilter('attribute_set_name', 'Custom_Sale_Attribute_Set')
            ->getFirstItem()
            ->getAttributeSetId();
        if (!$attributeSetId) {
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
            $data = [
                'attribute_set_name' => 'Custom_Sale_Attribute_Set', // define custom attribute set name here
                'entity_type_id' => $entityTypeId,
                'sort_order' => 200,
            ];
            $attributeSet->setData($data);
            $attributeSet->validate();
            $attributeSet->save();
            $attributeSet->initFromSkeleton($attributeSetId);
            $attributeSet->save();
            $attributeSetId = $attributeSet->getId();
        }


        $product->setAttributeSetId($attributeSetId)
            ->setTypeId('customsale')
            ->setStoreId(0)
            ->setSku('webpos-customsale')
            ->setWebsiteIds($websiteIds)
            ->setStockData(array(
                'manage_stock' => 0,
                'use_config_manage_stock' => 0,
            ));
        $product->addData(array(
            'name' => 'Custom Sale',
            'weight' => 1,
            'status' => 1,
            'visibility' => 1,
            'price' => 0,
            'description' => 'Custom Sale for POS system',
            'short_description' => 'Custom Sale for POS system',
            'quantity_and_stock_status' => array()
        ));
        if (!is_array($errors = $product->validate())) {
            try {
                $product->save();
                if (!$product->getId()) {
                    $lastProduct = $this->getProductModel()->getCollection()->setOrder('entity_id', 'DESC')->getFirstItem();
                    $lastProductId = $lastProduct->getId();
                    $product->setName('Custom Sale')->setId($lastProductId + 1)->save();
                    $this->getProductModel()->load(0)->delete();
                }
            } catch (\Exception $e) {
                return $this;
            }
        }

        $setup->endSetup();
    }
}
