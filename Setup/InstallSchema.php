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
                'currency\'s id'
            )
            ->addColumn(
                'order',
                Table::TYPE_INTEGER,
                11,
                [
                    'nullable' => false,
                    'default'  => 0
                ],
                'order factor, the factor to sort currency'
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
                [],
                'decimal, to create amount id'
            )->addColumn(
                'currency_decimal',
                Table::TYPE_INTEGER,
                11,
                [],
                'currency decimal'
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
                '60,30',
                ['nullable' => false],
                'amount'
            )
            ->addColumn(
                'tag_amount',
                Table::TYPE_DECIMAL,
                '60,30',
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

        $tableException = $installer->getConnection()->newTable(
            $installer->getTable('ezdefi_exception'))
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
                'payment_id',
                Table::TYPE_TEXT,
                50,
                [
                    'nullable' =>true,
                    'default'  => null,
                ],
                'payment id'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => true],
                'order id'
            )
            ->addColumn(
                'amount_id',
                Table::TYPE_DECIMAL,
                '60,30',
                ['nullable' => false],
                'amount id'
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
                'paid',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                4,
                ['default' => 0],
                'paid status'
            )->addColumn(
                'has_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                4,
                ['nullable' => false],
                '1: if payment use simple method, 0 if payment use ezdefi method'
            )->addColumn(
                'explorer_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'explorer url'
            )
            ->setComment('Ezdefi exception Table');
        $installer->getConnection()->createTable($tableException);



        $installer->endSetup();
    }
}