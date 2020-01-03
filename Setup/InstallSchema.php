<?php

namespace Ezdefi\PaymentMethod\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
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
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
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
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [
                    'nullable' => false,
                ],
                'currency_id'
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
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                '5,2',
                [],
                'discount'
            )->addColumn(
                'payment_lifetime',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'payment lifetime'
            )
            ->addColumn(
                'wallet_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'wallet address'
            )
            ->addColumn(
                'block_confirmation',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'block confirmation'
            )->addColumn(
                'decimal',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => 8],
                'decimal'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
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
        $installer->endSetup();
    }
}