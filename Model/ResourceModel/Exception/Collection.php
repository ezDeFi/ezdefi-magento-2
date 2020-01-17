<?php
namespace Ezdefi\Payment\Model\ResourceModel\Exception;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ezdefi_exception_collection';
    protected $_eventObject = 'exception_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(
    )
    {
        $this->_init('Ezdefi\Payment\Model\Exception', 'Ezdefi\Payment\Model\ResourceModel\Exception');
    }

    public function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->columns('order_table.customer_email')
            ->joinLeft(
            ['order_table' => $this->getTable('sales_order')],
            'main_table.order_id = order_table.entity_id',
            []
        );
    }
}