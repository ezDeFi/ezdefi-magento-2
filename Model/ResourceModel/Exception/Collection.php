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
            ->joinLeft(
            ['od' => $this->getTable('sales_order')],
            'main_table.order_id = od.entity_id',
            [
                'email' => 'od.customer_email',
                'customer' => 'CONCAT(od.customer_firstname, " ", od.customer_lastname)',
                'total' => 'od.grand_total',
                'date' => 'od.created_at',
                'increment_id' => 'od.increment_id'
            ])->joinLeft(
                array(
                    'new_order' => $this->getTable('sales_order')
                ),
                'new_order.entity_id = main_table.order_assigned',
                array(
                    'new_email' => 'new_order.customer_email',
                    'new_customer' => 'CONCAT(new_order.customer_firstname, " ", new_order.customer_lastname)',
                    'new_total' => 'new_order.grand_total',
                    'new_date' => 'new_order.created_at',
                    'new_increment_id' => 'new_order.increment_id'
                ));
        $this->addFilterToMap('increment_id', 'od.increment_id');
        $this->addFilterToMap('new_increment_id', 'new_order.increment_id');

    }
}