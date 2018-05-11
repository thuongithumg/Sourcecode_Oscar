<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_product;
    protected $_entityAttributeSetCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\Product $product,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory
    ) {
        $this->_product = $product;
        $this->_entityAttributeSetCollectionFactory = $collectionFactory;
    }
    protected function getProductModel() {
        return $this->_product;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('webpos_staff'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_role'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_authorization_role'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_authorization_rule'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_transaction'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_staff_location'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_customer_complain'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_products'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_session'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_shift'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_cash_transaction'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_order_payment'));


        $table = $installer->getConnection()->newTable(
            $installer->getTable('webpos_staff')
        )->addColumn(
            'staff_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'staff_id'
        )->addColumn(
            'store_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'store_ids'
        )->addColumn(
            'username',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'username'
        )->addColumn(
            'password',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'password'
        )->addColumn(
            'display_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'display_name'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'email'
        )->addColumn(
            'monthly_target',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'monthly_target'
        )->addColumn(
            'customer_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'customer_group'
        )->addColumn(
            'location_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'location_id'
        )->addColumn(
            'role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'role_id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'status'
        )->addColumn(
            'auto_logout',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'auto_logout'
        )->addColumn(
            'can_use_sales_report',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'can_use_sales_report'
        );

        $installer->getConnection()->createTable($table);
        $tableRole = $installer->getConnection()->newTable(
            $installer->getTable('webpos_authorization_role')
        )->addColumn(
            'role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'role_id'
        )->addColumn(
            'display_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'display_name'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'description'
        )->addColumn(
            'maximum_discount_percent',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'maximum_discount_percent'
        )->addColumn(
            'parent_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true],
            'parent_id'
        )->addColumn(
            'tree_level',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            '5',
            ['nullable' => true, 'unsigned' => true],
            'tree_level'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            '5',
            ['nullable' => true, 'unsigned' => true],
            'sort_order'
        )->addColumn(
            'role_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '1',
            ['nullable' => true],
            'role_type'
        );
        $installer->getConnection()->createTable($tableRole);

        $tableAuthorizeRule = $installer->getConnection()->newTable(
            $installer->getTable('webpos_authorization_rule')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'rule_id'
        )->addColumn(
            'role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            'role_id'
        )->addColumn(
            'resource_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => true],
            'resource_id'
        );
        $installer->getConnection()->createTable($tableAuthorizeRule);
        
        $tableLocation = $installer->getConnection()->newTable(
            $installer->getTable('webpos_staff_location')
        )->addColumn(
            'location_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'location_id'
        )->addColumn(
            'display_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'display_name'
        )->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'address'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'description'
        );
        $installer->getConnection()->createTable($tableLocation);

        $tableCustomerComplain = $installer->getConnection()->newTable(
            $installer->getTable('webpos_customer_complain')
        )->addColumn(
            'complain_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'complain_id'
        )->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'customer_email'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'content'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'created_at'
        );
        $installer->getConnection()->createTable($tableCustomerComplain);



        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_discount_amount',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable'  => true,
                'length'    => '12,4',
                'comment'   => 'Webpos Discount Amount'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_staff_id',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable'  => true,
                'length'    => '10',
                'comment'   => 'Webpos Staff ID'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_staff_name',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => true,
                'length'    => '255',
                'comment'   => 'Webpos Staff Name'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_shift_id',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => true,
                'length'    => '255',
                'comment'   => 'Webpos Shift Id'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'location_id',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable'  => true,
                'length'    => '10',
                'comment'   => 'Location Id'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_change',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable'  => true,
                'length'    => '12,4',
                'comment'   => 'Webpos Change'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_base_change',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable'  => true,
                'length'    => '12,4',
                'comment'   => 'Webpos Base Change'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_delivery_date',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'nullable'  => true,
                'length'    => null,
                'comment'   => 'Webpos Delivery Date'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_order_id',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => true,
                'length'    => '255',
                'comment'   => 'Webpos Order Id'
            )
        );
        
        $webposSession = $installer->getConnection()->newTable(
            $installer->getTable('webpos_session')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'id'
        )->addColumn(
            'staff_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'staff_id'
        )->addColumn(
            'logged_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'logged_date'
        )->addColumn(
            'session_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '40',
            ['nullable' => true],
            'session_id'
        )->addForeignKey(
            $setup->getFkName('webpos_session', 'staff_id', 'webpos_staff', 'staff_id'),
            'staff_id',
            $setup->getTable('webpos_staff'),
            'staff_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($webposSession);

        $tableOrderPayment = $installer->getConnection()->newTable(
            $installer->getTable('webpos_order_payment')
        )->addColumn(
            'payment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Payment ID'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Order ID'
        )->addColumn(
            'shift_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Shift ID'
        )->addColumn(
            'base_payment_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Base Payment Amount'
        )->addColumn(
            'payment_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Payment Amount'
        )->addColumn(
            'base_real_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Base Real Amount'
        )->addColumn(
            'real_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Real Amount'
        )->addColumn(
            'method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Method'
        )->addColumn(
            'method_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Method Title'
        )->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Transaction ID'
        )->addColumn(
            'invoice_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Invoice Id'
        )->addColumn(
            'reference_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Reference Number'
        );

        $installer->getConnection()->createTable($tableOrderPayment);

        $tableShift = $installer->getConnection()->newTable(
            $installer->getTable('webpos_shift')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Shift ID'
        )->addColumn(
            'shift_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Shift ID'
        )->addColumn(
            'staff_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Staff ID'
        )->addColumn(
            'location_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Location ID'
        )->addColumn(
            'float_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Float Amount'
        )->addColumn(
            'base_float_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Float Amount'
        )->addColumn(
            'closed_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Closed Amount'
        )->addColumn(
            'base_closed_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Closed Amount'
        )->addColumn(
            'cash_left',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Cash Left'
        )->addColumn(
            'base_cash_left',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Cash Left'
        )->addColumn(
            'total_sales',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Total Sales'
        )->addColumn(
            'base_total_sales',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Total Sales'
        )->addColumn(
            'base_balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Balance'
        )->addColumn(
            'balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Balance'
        )->addColumn(
            'cash_sale',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Cash Sale'
        )->addColumn(
            'base_cash_sale',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Cash Sale'
        )->addColumn(
            'cash_added',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Cash Added'
        )->addColumn(
            'base_cash_added',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Base Cash Added'
        )->addColumn(
            'cash_removed',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Cash Removed'
        )->addColumn(
            'base_cash_removed',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Base Cash Removed'
        )->addColumn(
            'opened_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Opened At'
        )->addColumn(
            'closed_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Closed At'
        )->addColumn(
            'opened_note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Open NOte'
        )->addColumn(
            'closed_note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Closed Note'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            1,
            ['unsigned' => true, 'nullable' => false],
            'Status'
        )->addColumn(
            'base_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            [],
            'Base Currency Code'
        )->addColumn(
            'shift_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            [],
            'Shift Currency Code'
        )->addColumn(
            'indexeddb_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'indexeddb_id' 
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
            'Updated At'
        );

        $installer->getConnection()->createTable($tableShift);


        $tableCashTransaction = $installer->getConnection()->newTable(
            $installer->getTable('webpos_cash_transaction')
        )->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Transaction ID'
        )->addColumn(
            'shift_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Shift ID'
        )->addColumn(
            'location_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Location ID'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Order ID'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Value'
        )->addColumn(
            'base_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Value'
        )->addColumn(
            'balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Balance'
        )->addColumn(
            'base_balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Balance'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Note'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Type'
        )->addColumn(
            'base_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            [],
            'Base Currency Code'
        )->addColumn(
            'transaction_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            [],
            'Transaction Currency Code'
        )->addColumn(
            'indexeddb_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'indexeddb_id'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
            'Updated At'
        );

        $installer->getConnection()->createTable($tableCashTransaction);
        
        $setup->getConnection()->addColumn(
            $setup->getTable('cataloginventory_stock_item'),
            'updated_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
            'Updated Time'
        );


        $installer->endSetup();
        return $this;
    }
}