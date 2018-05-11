<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

    $installer = $this;
    $installer->startSetup();

    $installer->getConnection()->dropTable($installer->getTable('webpos_shift'));
    $tableShift = $installer->getConnection()->newTable(
        $installer->getTable('webpos_shift')
    )->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Shift ID'
    )->addColumn(
        'shift_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Shift ID'
    )->addColumn(
        'staff_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Staff ID'
    )->addColumn(
        'location_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Location ID'
    )->addColumn(
        'float_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Float Amount'
    )->addColumn(
        'base_float_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Float Amount'
    )->addColumn(
        'closed_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Closed Amount'
    )->addColumn(
        'base_closed_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Closed Amount'
    )->addColumn(
        'cash_left',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Cash Left'
    )->addColumn(
        'base_cash_left',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Cash Left'
    )->addColumn(
        'total_sales',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Total Sales'
    )->addColumn(
        'base_total_sales',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Total Sales'
    )->addColumn(
        'base_balance',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Balance'
    )->addColumn(
        'balance',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Balance'
    )->addColumn(
        'cash_sale',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Cash Sale'
    )->addColumn(
        'base_cash_sale',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Cash Sale'
    )->addColumn(
        'cash_added',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        128,
        array(),
        'Cash Added'
    )->addColumn(
        'base_cash_added',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        128,
        array(),
        'Base Cash Added'
    )->addColumn(
        'cash_removed',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        128,
        array(),
        'Cash Removed'
    )->addColumn(
        'base_cash_removed',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        128,
        array(),
        'Base Cash Removed'
    )->addColumn(
        'opened_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Opened At'
    )->addColumn(
        'closed_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array('nullable' => true),
        'Closed At'
    )->addColumn(
        'opened_note',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Open NOte'
    )->addColumn(
        'closed_note',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Closed Note'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        1,
        array('unsigned' => true, 'nullable' => false),
        'Status'
    )->addColumn(
        'base_currency_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        3,
        array(),
        'Base Currency Code'
    )->addColumn(
        'shift_currency_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        3,
        array(),
        'Shift Currency Code'
    )->addColumn(
        'indexeddb_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'indexeddb_id'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP),
        'Updated At'
    );
    $installer->getConnection()->createTable($tableShift);

    $installer->getConnection()->addColumn(
        $installer->getTable('webpos_shift'),
        'pos_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable'  => true,
            'comment'   => 'POS ID'
        )
    );
    $installer->getConnection()->addColumn(
        $installer->getTable('webpos_user'),
        'pos_ids',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'comment'   => 'POS IDs'
        )
    );
    $installer->getConnection()->addColumn(
        $installer->getTable('webpos_user'),
        'pin',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'length'    => 6,
            'default'   => '0000',
            'comment'   => 'PIN'
        )
    );

    $installer->getConnection()->dropTable($installer->getTable('webpos_pos'));
    $table = $installer->getConnection()->newTable(
        $installer->getTable('webpos_pos')
    )->addColumn(
        'pos_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'pos_id'
    )->addColumn(
        'pos_name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('nullable' => false, 'default' => ''),
        'pos_name'
    )->addColumn(
        'location_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => false, 'default' => 0),
        'location_id'
    )->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => false, 'default' => 0),
        'store_id'
    )->addColumn(
        'user_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => true),
        'user_id'
    )->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array('nullable' => false, 'default' => 0),
        'status'
    );
     $installer->getConnection()->createTable($table);

    $installer->getConnection()->dropTable($installer->getTable('webpos_cash_transaction'));
    $tableCashTransaction = $installer->getConnection()->newTable(
        $installer->getTable('webpos_cash_transaction')
    )->addColumn(
        'transaction_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Transaction ID'
    )->addColumn(
        'shift_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Shift ID'
    )->addColumn(
        'location_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Location ID'
    )->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => true),
        'Order ID'
    )->addColumn(
        'value',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Value'
    )->addColumn(
        'base_value',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Value'
    )->addColumn(
        'balance',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Balance'
    )->addColumn(
        'base_balance',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base Balance'
    )->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT),
        'Created At'
    )->addColumn(
        'note',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Note'
    )->addColumn(
        'type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'Type'
    )->addColumn(
        'base_currency_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        3,
        array(),
        'Base Currency Code'
    )->addColumn(
        'transaction_currency_code',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        3,
        array(),
        'Transaction Currency Code'
    )->addColumn(
        'indexeddb_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        null,
        array(),
        'indexeddb_id'
    )->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array('nullable' => false, 'default' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP),
        'Updated At'
    );
     $installer->getConnection()->createTable($tableCashTransaction);

     $installer->getConnection()->dropTable($installer->getTable('webpos_cash_denomination'));
     $table = $installer->getConnection()->newTable(
         $installer->getTable('webpos_cash_denomination')
     )->addColumn(
         'denomination_id',
         Varien_Db_Ddl_Table::TYPE_INTEGER,
         null,
         array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
         'denomination_id'
     )->addColumn(
         'denomination_name',
         Varien_Db_Ddl_Table::TYPE_TEXT,
         null,
         array('nullable' => false, 'default' => ''),
         'denomination_name'
     )->addColumn(
         'denomination_value',
         Varien_Db_Ddl_Table::TYPE_DECIMAL,
         '12,4',
         array('nullable' => true),
         'denomination_value'
     )->addColumn(
         'pos_ids',
         Varien_Db_Ddl_Table::TYPE_TEXT,
         null,
         array('nullable' => true, 'default' => ''),
         'pos_ids'
     )->addColumn(
         'sort_order',
         Varien_Db_Ddl_Table::TYPE_INTEGER,
         null,
         array('unsigned' => true, 'nullable' => true),
         'sort_order'
     );

     $installer->getConnection()->createTable($table);
     $installer->getConnection()->addColumn(
         $installer->getTable('webpos_pos'),
         'denomination_ids',
         array(
             'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
             'nullable'  => true,
             'comment'   => 'Denomination IDs'
         )
     );
     $installer->getConnection()->changeColumn(
         $installer->getTable('webpos_user'),
         'location_id',
         'location_id',
         array(
             'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
             'nullable'  => true,
             'length'    => '255',
             'comment'   => 'Webpos Location'
         )
     );
     $installer->getConnection()->addColumn(
         $installer->getTable('webpos_shift'),
         'profit_loss_reason',
         array(
             'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
             'nullable'  => true,
             'length'    => '255',
             'comment'   => 'profit_loss_reason'
         )
     );
     $installer->getConnection()->addColumn(
         $installer->getTable('webpos_api_session'),
         'location_id',
         array(
             'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
             'nullable'  => true,
             'comment'   => 'location_id'
         )
     );
     $installer->getConnection()->addColumn(
         $installer->getTable('webpos_api_session'),
         'pos_id',
         array(
             'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
             'nullable'  => true,
             'comment'   => 'pos_id'
         )
     );

    $installer->endSetup();