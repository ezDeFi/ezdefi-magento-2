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
    CONST TIME_REMOVE_EXCEPTION = 7;

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
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
                'order_assigned',
                Table::TYPE_INTEGER,
                11,
                [
                    'nullable' => true,
                    'default'  => null
                ],
                'order assigned id'
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
            )->addColumn(
                'confirmed',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                2,
                ['default' => 0],
                '0: not confirm from exceptin, 1: be confirmed from exception'
            )
            ->setComment('Ezdefi exception Table');
        $installer->getConnection()->createTable($tableException);

        $installer->run("
            CREATE EVENT  IF NOT EXISTS `ezdefi_remove_exception_event`
            ON SCHEDULE EVERY ".self::TIME_REMOVE_EXCEPTION." DAY
            STARTS DATE(NOW())
            DO
            DELETE FROM `{$installer->getTable('ezdefi_cryptocurrencypayment/exception')}` WHERE DATEDIFF( NOW( ) ,  expiration ) >= 5;
        ");

        $installer->endSetup();
    }
}