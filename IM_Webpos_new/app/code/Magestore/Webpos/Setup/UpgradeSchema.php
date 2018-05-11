<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * UpgradeSchema constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            //add tax_class_id for sales_order_table table
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_item'),
                'custom_tax_class_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'length' => '11',
                    'comment' => 'Custom Tax Class Id'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('catalog_product_entity'),
                'updated_datetime',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
                'Updated Time'
            );
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create();
            /**
             * Remove attribute webpos_visible
             */
            //Find these in the eav_entity_type table
            $action = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Catalog\Model\ResourceModel\Product\Action'
            );
            $attribute = $action->getAttribute('webpos_visible');
            if ($attribute) {
                $entityTypeId = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('Magento\Eav\Model\Config')
                    ->getEntityType(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
                    ->getEntityTypeId();
                $eavSetup->removeAttribute($entityTypeId, 'webpos_visible');
            }

            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'webpos_visible'
            );

            /**
             * Add attributes to the eav/attribute
             */
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'webpos_visible',
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Visible on Webpos',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
            $action = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\Catalog\Model\ResourceModel\Product\Action'
            );
            $table = $setup->getTable('catalog_product_entity_int');
            $connection = $action->getConnection();
            $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', 'webpos_visible');
            //set invisible for default
            $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection'
            );
            $version = $this->productMetadata->getVersion();
            $edition = $this->productMetadata->getEdition();
            foreach ($productCollection->getAllIds() as $productId) {
                if ($edition == 'Enterprise' && version_compare($version, '2.1.5', '>=')) {
                    $data = [
                        'attribute_id' => $attributeId,
                        'store_id' => 0,
                        'row_id' => $productId,
                        'value' => 1
                    ];
                } else {
                    $data = [
                        'attribute_id' => $attributeId,
                        'store_id' => 0,
                        'entity_id' => $productId,
                        'value' => 1
                    ];
                }
                $connection->insertOnDuplicate($table, $data, ['value']);
            }

        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            //add customer full name for sales_order table
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'customer_fullname',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Customer Full Name'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'webpos_init_data',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Web POS init data use for on hold order'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'webpos_cart_discount_type',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '5',
                    'comment' => 'Web POS Discount Type'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'webpos_cart_discount_value',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Web POS Discount Value'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'webpos_cart_discount_name',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Web POS Discount Name'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_session'),
                'current_shift_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Current Shift ID'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_session'),
                'current_quote_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Current Quote ID'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_session'),
                'current_store_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Current Store ID'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'base_gift_voucher_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Base Gifvoucher Discount Value'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Discount Value'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_base_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Hidden Tax Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Hidden Tax Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_base_shipping_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Shipping Hidden Tax Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_shipping_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Shipping Hidden Tax Amount'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'rewardpoints_earn',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'length' => '11',
                    'comment' => 'Reward Points Earn'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'rewardpoints_spent',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'length' => '11',
                    'comment' => 'Reward Points Spent'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'rewardpoints_base_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Reward Points Base Discount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'rewardpoints_base_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Reward Points Base Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'rewardpoints_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Reward Points Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'rewardpoints_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Reward Points Discount'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.1.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_shift'),
                'pos_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'POS ID'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_staff'),
                'pos_ids',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'POS IDs'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_staff'),
                'pin',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 6,
                    'default' => '0000',
                    'comment' => 'PIN'
                )
            );

            $setup->getConnection()->dropTable($setup->getTable('webpos_pos'));
            $installer = $setup;
            $table = $installer->getConnection()->newTable(
                $installer->getTable('webpos_pos')
            )->addColumn(
                'pos_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'pos_id'
            )->addColumn(
                'pos_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'pos_name'
            )->addColumn(
                'location_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'location_id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'store_id'
            )->addColumn(
                'staff_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'staff_id'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 0],
                'status'
            );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {

            $setup->getConnection()->dropTable($setup->getTable('webpos_cash_denomination'));
            $installer = $setup;
            $table = $installer->getConnection()->newTable(
                $installer->getTable('webpos_cash_denomination')
            )->addColumn(
                'denomination_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'denomination_id'
            )->addColumn(
                'denomination_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'denomination_name'
            )->addColumn(
                'denomination_value',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'denomination_value'
            )->addColumn(
                'pos_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => ''],
                'pos_ids'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'sort_order'
            );

            $installer->getConnection()->createTable($table);

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_pos'),
                'denomination_ids',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Denomination IDs'
                )
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('webpos_staff'),
                'location_id',
                'location_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Webpos Location'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_shift'),
                'profit_loss_reason',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'profit_loss_reason'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_session'),
                'location_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'location_id'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_session'),
                'pos_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'pos_id'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_cash_transaction'),
                'staff_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'staff_id'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_cash_transaction'),
                'staff_name',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'staff_name'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $quote = 'quote';
            $orderTable = 'sales_order';
            $orderGridTable = 'sales_order_grid';
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quote),
                    'fulfill_online',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => 11,
                        'comment' => 'Fulfill Online',
                        'default' => 0
                    ]
                );
            //Order table
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($orderTable),
                    'fulfill_online',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => 11,
                        'comment' => 'Fulfill Online',
                        'default' => 0
                    ]
                );
            //Order grid table
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($orderGridTable),
                    'fulfill_online',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => 11,
                        'comment' => 'Fulfill Online',
                        'default' => 0
                    ]
                );

            if (!$setup->getConnection()->tableColumnExists($setup->getTable('sales_order_grid'), 'webpos_delivery_date')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_order_grid'),
                    'webpos_delivery_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable('webpos_staff_location'), 'store_id')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('webpos_staff_location'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER
                );
            }
        }

        if (version_compare($context->getVersion(), '2.3.1.1', '<')) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('webpos_order_payment'),
                    'card_type',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '50',
                        'comment' => 'Card Type',
                        'nullable' => true,
                        'default' => ''
                    ]
                );
        }

        if (version_compare($context->getVersion(), '2.3.2.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_pos'),
                'pin',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 4,
                    'comment' => 'Pin number',
                    'nullable' => true
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_pos'),
                'staff_locked',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'comment' => 'Staff Locked POS',
                    'nullable' => true
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_shift'),
                'cash_refunded',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable'  => true,
                    'length'    => '12,4',
                    'comment'   => 'Cash refunded'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_shift'),
                'base_cash_refunded',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable'  => true,
                    'length'    => '12,4',
                    'comment'   => 'Base cash refunded'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_creditmemo'),
                'webpos_shift_id',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'length'    => '255',
                    'comment'   => 'Webpos Shift Id'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.4.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_pos'),
                'is_allow_to_lock',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Is allow to lock',
                    'nullable' => false,
                    'default' => 0
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.4.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_session'),
                'is_allow_multi_pos',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'comment' => 'Is accept multi pos',
                    'nullable' => false,
                    'default' => 0
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.4.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_shift'),
                'open_by',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 6,
                    'comment' => 'open by',
                    'nullable' => false,
                    'default' => 0
                ]
            );

//            $setup->getConnection()->addColumn(
//                $setup->getTable('webpos_shift'),
//                'close_by',
//                [
//                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
//                    'length' => 6,
//                    'comment' => 'close by',
//                    'nullable' => false,
//                    'default' => 0
//                ]
//            );
        }

        $setup->endSetup();
    }
}
