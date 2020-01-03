<?php

namespace Ezdefi\PaymentMethod\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateCoinTable implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ezdefi_currency'))
            ->addColumn(
                '',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ]
            )
            ->addColumn(
                'logo',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'logo'
            )
            ->addColumn(
                'symbol',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'symbol'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'name'
            )
            ->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                '5,2',
                [],
                'symbol'
            )
            ->addColumn(
                'payment_lifetime',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                'payment lifetime'
            )
            ->addColumn(
                'wallet_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'wallet address'
            )
            ->addColumn(
                'block_confirmation',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                'block confirmation'
            )->addColumn(
                'decimal',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => 'false'],
                'decimal'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'description'
            )->setComment('Ezdefi currency Table');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}