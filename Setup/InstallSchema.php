<?php

namespace Ezdefi\Payment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\DB\Ddl\Table;
//use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ezdefi_currency'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'id'
            )
            ->addColumn(
                'currency_id',
                Table::TYPE_TEXT,
                50,
                [
                    'nullable' => false,
                ],
                'currency_id'
            )
            ->addColumn(
                'logo',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'logo'
            )
            ->addColumn(
                'symbol',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'symbol'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'name'
            )->addColumn(
                'discount',
                Table::TYPE_FLOAT,
                '5,2',
                [],
                'discount'
            )->addColumn(
                'payment_lifetime',
                Table::TYPE_INTEGER,
                11,
                [],
                'payment lifetime'
            )
            ->addColumn(
                'wallet_address',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'wallet address'
            )
            ->addColumn(
                'block_confirmation',
                Table::TYPE_INTEGER,
                11,
                [],
                'block confirmation'
            )->addColumn(
                'decimal',
                Table::TYPE_INTEGER,
                11,
                ['default' => 8],
                'decimal'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'description'
            )->addIndex(
                $installer->getIdxName(
                    $installer->getTable('ezdefi_currency'),
                    ['currency_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                'currency_id',
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment('Ezdefi currency Table');
        $installer->getConnection()->createTable($table);


        $tableAmount = $installer->getConnection()->newTable(
            $installer->getTable('ezdefi_amount'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'id'
            )
            ->addColumn(
                'temp',
                Table::TYPE_INTEGER,
                11,
                [
                    'nullable' => false,
                ],
                'temp'
            )
            ->addColumn(
                'amount',
                Table::TYPE_DECIMAL,
                '25,14',
                ['nullable' => false],
                'amount'
            )
            ->addColumn(
                'tag_amount',
                Table::TYPE_DECIMAL,
                '25,14',
                ['nullable' => false],
                'tag_amount'
            )
            ->addColumn(
                'expiration',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'expiration'
            )->addColumn(
                'currency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [],
                'currency'
            )->addColumn(
                'decimal',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                5,
                ['nullable' => false],
                'decimal'
            )->setComment('Ezdefi amount Table');
        $installer->getConnection()->createTable($tableAmount);

        $installer->endSetup();
    }
}