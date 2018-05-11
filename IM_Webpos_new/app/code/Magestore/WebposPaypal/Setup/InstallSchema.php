<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposPaypal\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();


        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_paypal_invoice_number',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => true,
                'length'    => '255',
                'comment'   => 'Webpos Paypal Invoice Number'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'webpos_paypal_invoice_id',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => true,
                'length'    => '255',
                'comment'   => 'Webpos Paypal Invoice Id'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('webpos_order_payment'),
            'webpos_paypal_active',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'    => 1,
                'comment'   => 'Webpos Paypal Active'
            )
        );
        $installer->endSetup();
        return $this;
    }
}