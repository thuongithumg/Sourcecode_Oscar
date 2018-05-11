<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeData implements UpgradeDataInterface
{
    
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    
    /**
    * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
    */
    protected $_eavAttribute;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosFactory
     */
    protected $posFactory;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory
     */
    protected $staffCollectionFactory;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var \Magestore\Webpos\Api\InstallManagementInterface
     */
    protected $installManagementInterface;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magestore\Webpos\Api\InstallManagementInterface $installManagementInterface
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magestore\Webpos\Model\Pos\PosFactory $posFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationCollectionFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magestore\Webpos\Api\InstallManagementInterface $installManagementInterface,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationCollectionFactory,
        \Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        \Magento\Framework\App\State $appState
    ){
        $this->eavSetupFactory = $eavSetupFactory;
        $this->installManagementInterface = $installManagementInterface;
        $this->eavConfig = $eavConfig;
        $this->_eavAttribute = $eavAttribute;
        $this->productMetadata = $productMetadata;
        $this->posFactory = $posFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->staffCollectionFactory = $staffCollectionFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->_appState = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

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

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', 'webpos_visible');
            $action = \Magento\Framework\App\ObjectManager::getInstance()->create(
           '\Magento\Catalog\Model\ResourceModel\Product\Action'
            );
            $connection = $action->getConnection();
            $table = $setup->getTable('catalog_product_entity_int');
            //set invisible for default
            $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection'
            );
            $visibleInSite = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\Catalog\Model\Product\Visibility'
            )->getVisibleInSiteIds();

            $productCollection->addAttributeToFilter('visibility', ['nin' => $visibleInSite]);

            $version = $this->productMetadata->getVersion();
            $edition = $this->productMetadata->getEdition();
            foreach($productCollection->getAllIds() as $productId){
                if($edition == 'Enterprise' && version_compare($version, '2.1.5', '>=')){
                    $data = [
                        'attribute_id'  => $attributeId,
                        'store_id'  => 0,
                        'row_id' => $productId,
                        'value' => 0
                    ];
                }else{
                    $data = [
                        'attribute_id'  => $attributeId,
                        'store_id'  => 0,
                        'entity_id' => $productId,
                        'value' => 0
                    ];
                }
                $connection->insertOnDuplicate($table, $data, ['value']);
            }
        }
        if (version_compare($context->getVersion(), '1.1.5', '<')) {
            $data = array(
                'pos_name' => 'Store POS',
                'location_id' => $this->getDefaultLocationId(),
                'store_id' => $this->getDefaultStoreId(),
                'status' => 1
            );
            $posModel = $this->posFactory->create()->setData($data)->save();
            $posId = $posModel->getId();
            if($posId) {
                $this->assignDefaultPosForStaff($posId);
            }
        }
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
            $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

            /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
            $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
            $entityAttributesCodes = [
                'fulfill_online' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            ];
            foreach ($entityAttributesCodes as $code => $type) {
                $quoteInstaller->addAttribute('quote', $code, ['type' => $type, 'length'=> 11, 'visible' => false, 'nullable' => true,]);
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'length'=> 11, 'visible' => false,'nullable' => true,]);
                $salesInstaller->addAttribute('invoice', $code, ['type' => $type, 'length'=> 11, 'visible' => false, 'nullable' => true,]);
            }
        }
        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $this->installManagementInterface->convertSaleItemsData();
        }
        if (version_compare($context->getVersion(), '2.3.1.1', '<')) {
//            $this->installManagementInterface->createIndexTable(\Magestore\Webpos\Model\Service\Synchronization\Product::SYNCHRONIZATION_TYPE);
//            $this->installManagementInterface->addIndexTableData(\Magestore\Webpos\Model\Service\Synchronization\Product::SYNCHRONIZATION_TYPE);
//            $this->installManagementInterface->createIndexTable(\Magestore\Webpos\Model\Service\Synchronization\Stock::SYNCHRONIZATION_TYPE);
//            $this->installManagementInterface->addIndexTableData(\Magestore\Webpos\Model\Service\Synchronization\Stock::SYNCHRONIZATION_TYPE);
        }
        $setup->endSetup();
    }



    /**
     * @return int
     */
    public function getDefaultLocationId()
    {
        $collection = $this->locationCollectionFactory->create();
        $defaultLocation = $collection->getFirstItem();
        if($locationId = $defaultLocation->getId()) {
            return $locationId;
        }
        return 0;
    }

    /**
     * @return int
     */
    public function getDefaultStoreId()
    {
        $storeManager  = \Magento\Framework\App\ObjectManager::getInstance()
                            ->get('\Magento\Store\Model\StoreManagerInterface');
        $storeId = $storeManager->getStore()->getStoreId();
        return $storeId;
    }

    /**
     * @return void
     */
    public function assignDefaultPosForStaff($posId)
    {
        $collection = $this->staffCollectionFactory->create();
        foreach ($collection as $staff) {
            $staff->setPosIds($posId);
            try{
                $staff->save();
            }catch (\Exception $e){

            }
        }
    }
}
