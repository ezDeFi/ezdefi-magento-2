<?php
namespace Ezdefi\PaymentMethod\Model\ResourceModel\Currency;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ezdefi_currency_collection';
    protected $_eventObject = 'currency_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ezdefi\PaymentMethod\Model\Currency', 'Ezdefi\PaymentMethod\Model\ResourceModel\Currency');
    }

}